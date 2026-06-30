<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Services\ContactImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportExcel($request);
        }

        $query = Contact::forUser($userId)->with('groups')
            ->search($request->search)
            ->inGroup($request->group_id ? (int) $request->group_id : null);

        if ($request->favorites === '1') $query->favorites();
        if ($request->sync_status) $query->bySyncStatus($request->sync_status);

        $contacts = $query->latest()->paginate($request->per_page ?? 15)->withQueryString();
        $groups = ContactGroup::forUser($userId)->active()->get();
        $stats = [
            'total' => Contact::forUser($userId)->count(),
            'synced' => Contact::forUser($userId)->bySyncStatus('synced')->count(),
            'unsynced' => Contact::forUser($userId)->whereIn('sync_status', ['local_only', 'pending_sync', 'sync_failed'])->count(),
            'favorites' => Contact::forUser($userId)->favorites()->count(),
        ];

        return view('contacts.index', compact('contacts', 'groups', 'stats'));
    }

    public function create()
    {
        $groups = ContactGroup::forUser(auth()->id())->active()->get();
        return view('contacts.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => ['required','string','max:20', Rule::unique('contacts')->where(fn ($q) => $q->where('user_id', auth()->id()))],
            'file_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'tags' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:contact_groups,id',
        ], ['name.required' => 'اسم العميل مطلوب', 'phone_number.required' => 'رقم الهاتف مطلوب', 'phone_number.unique' => 'رقم الهاتف مسجل مسبقاً']);

        $importService = app(ContactImportService::class);
        $validated['phone_number'] = $importService->normalizePhoneNumber($validated['phone_number']);
        if (!empty($validated['tags'])) $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        $validated['user_id'] = auth()->id();
        $validated['sync_status'] = 'local_only';

        $contact = Contact::create($validated);

        if (!empty($request->groups)) {
            $validGroups = ContactGroup::forUser(auth()->id())->whereIn('id', $request->groups)->pluck('id');
            $contact->groups()->sync($validGroups);
        }

        return redirect()->route('contacts.index')->with('success', __('contacts.contact_created'));
    }

    public function show(Contact $contact)
    {
        if ($contact->user_id !== auth()->id()) abort(403);
        $contact->load('groups');
        $contact->refreshMessageCount();
        $recentMessages = $contact->messages()->latest()->take(10)->get();
        return view('contacts.show', compact('contact', 'recentMessages'));
    }

    public function edit(Contact $contact)
    {
        if ($contact->user_id !== auth()->id()) abort(403);
        $groups = ContactGroup::forUser(auth()->id())->active()->get();
        $selectedGroups = $contact->groups->pluck('id')->toArray();
        return view('contacts.edit', compact('contact', 'groups', 'selectedGroups'));
    }

    public function update(Request $request, Contact $contact)
    {
        if ($contact->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => ['required','string','max:20', Rule::unique('contacts')->where(fn ($q) => $q->where('user_id', auth()->id()))->ignore($contact->id)],
            'file_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'tags' => 'nullable|string',
            'is_favorite' => 'nullable|boolean',
            'groups' => 'nullable|array',
        ]);

        $importService = app(ContactImportService::class);
        $validated['phone_number'] = $importService->normalizePhoneNumber($validated['phone_number']);
        $validated['tags'] = !empty($validated['tags']) ? array_map('trim', explode(',', $validated['tags'])) : null;
        if ($contact->isSynced()) $validated['sync_status'] = 'pending_sync';

        $contact->update($validated);

        $validGroups = [];
        if (!empty($request->groups)) {
            $validGroups = ContactGroup::forUser(auth()->id())->whereIn('id', $request->groups)->pluck('id')->toArray();
        }
        $contact->groups()->sync($validGroups);

        return redirect()->route('contacts.index')->with('success', __('contacts.contact_updated'));
    }

    public function destroy(Contact $contact)
    {
        if ($contact->user_id !== auth()->id()) abort(403);
        $contact->groups()->detach();
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', __('contacts.contact_deleted'));
    }

    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,add_to_group,toggle_favorite',
            'selected' => 'required|array|min:1',
            'selected.*' => 'exists:contacts,id',
            'group_id' => 'nullable|exists:contact_groups,id',
        ]);

        $contacts = Contact::forUser(auth()->id())->whereIn('id', $request->selected)->get();

        switch ($request->action) {
            case 'delete':
                foreach ($contacts as $c) { $c->groups()->detach(); $c->delete(); }
                return back()->with('success', __('contacts.contacts_deleted'));
            case 'toggle_favorite':
                foreach ($contacts as $c) $c->toggleFavorite();
                return back()->with('success', 'تم تبديل حالة المفضلة');
            case 'add_to_group':
                if ($request->group_id) {
                    $group = ContactGroup::forUser(auth()->id())->findOrFail($request->group_id);
                    foreach ($contacts as $c) {
                        if (!$c->groups()->where('contact_groups.id', $group->id)->exists()) $c->groups()->attach($group->id);
                    }
                    return back()->with('success', 'تم إضافة جهات الاتصال للمجموعة');
                }
                return back()->with('error', 'يرجى تحديد المجموعة');
        }
        return back();
    }

    public function toggleFavorite(Contact $contact)
    {
        if ($contact->user_id !== auth()->id()) return response()->json(['error' => 'غير مصرح'], 403);
        $contact->toggleFavorite();
        return response()->json(['success' => true, 'is_favorite' => $contact->fresh()->is_favorite]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        if (strlen($query) < 2) return response()->json([]);
        return response()->json(
            Contact::forUser(auth()->id())->search($query)
                ->select('id', 'name', 'phone_number', 'file_number', 'company_name')->limit(10)->get()
        );
    }

    private function exportExcel(Request $request)
    {
        $contacts = Contact::forUser(auth()->id())->with('groups')
            ->search($request->search)->inGroup($request->group_id ? (int) $request->group_id : null)
            ->latest()->get();
        $filePath = app(ContactImportService::class)->exportContacts($contacts);
        return response()->download($filePath, 'contacts_' . now()->format('Y_m_d') . '.xlsx')->deleteFileAfterSend(true);
    }

    // ===== المجموعات =====
    public function groups()
    {
        $groups = ContactGroup::forUser(auth()->id())->withCount('contacts')->latest()->get();
        return view('contacts.groups', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('contact_groups')->where(fn ($q) => $q->where('user_id', auth()->id()))],
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ], ['name.required' => 'اسم المجموعة مطلوب', 'name.unique' => 'اسم المجموعة موجود مسبقاً']);
        $validated['user_id'] = auth()->id();
        ContactGroup::create($validated);
        return back()->with('success', __('contacts.group_created'));
    }

    public function updateGroup(Request $request, ContactGroup $group)
    {
        if ($group->user_id !== auth()->id()) abort(403);
        $validated = $request->validate([
            'name' => ['required','string','max:255', Rule::unique('contact_groups')->where(fn ($q) => $q->where('user_id', auth()->id()))->ignore($group->id)],
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ]);
        $group->update($validated);
        return back()->with('success', __('contacts.group_updated'));
    }

    public function destroyGroup(ContactGroup $group)
    {
        if ($group->user_id !== auth()->id()) abort(403);
        $group->contacts()->detach();
        $group->delete();
        return back()->with('success', __('contacts.group_deleted'));
    }

    public function syncNow()
    {
        $userId = auth()->id();
        $key = 'sync-contacts-user-' . $userId;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "الرجاء الانتظار $seconds ثانية قبل المحاولة مرة أخرى.");
        }

        RateLimiter::hit($key, 60); // 60 seconds limit

        try {
            Artisan::call('contacts:sync', [
                '--now' => true,
                '--user' => $userId
            ]);
            
            return back()->with('success', 'تم بدء المزامنة بنجاح، يرجى تحديث الصفحة بعد قليل لرؤية التغييرات.');
        } catch (\Exception $e) {
            Log::error('Manual Sync Failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'حدث خطأ أثناء محاولة المزامنة.');
        }
    }
}
