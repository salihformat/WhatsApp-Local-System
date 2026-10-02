<?php

namespace Tests\Feature;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SendMessageJobTokenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:' . base64_encode(str_repeat('a', 32)),
            'app.central_api_url' => 'https://central.test/api',
            'app.company_id' => 1,
        ]);

        // جدول مصغّر بالأعمدة التي تحتاجها المهمة فقط (مهاجرات المشروع الكاملة لا تعمل على sqlite)
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->nullable();
            $table->text('message_text')->nullable();
            $table->string('message_type')->default('text');
            $table->string('status')->default('pending');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->string('central_message_id')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    private function createMessage(): Message
    {
        return Message::withoutEvents(fn () => Message::create([
            'phone_number' => '966500000000',
            'message_text' => 'hello',
            'message_type' => 'text',
            'status' => 'pending',
        ]));
    }

    public function test_job_fails_without_sending_when_token_is_invalid(): void
    {
        Http::fake();
        config(['app.central_api_token' => "  \n"]);
        $message = $this->createMessage();

        (new SendMessageJob($message->id))->handle();

        Http::assertNothingSent();
        $message->refresh();
        $this->assertSame('failed', $message->status);
        $this->assertStringContainsString('CENTRAL_API_TOKEN', $message->error_message);
    }

    public function test_job_sends_cleaned_bearer_header(): void
    {
        Http::fake(['*' => Http::response(['success' => true, 'message_id' => 'abc', 'status' => 'sent'], 200)]);
        config(['app.central_api_token' => "  tok123\n"]);
        $message = $this->createMessage();

        (new SendMessageJob($message->id))->handle();

        Http::assertSent(fn ($request) => $request->header('Authorization') === ['Bearer tok123']);
    }
}
