@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'group flex items-center px-3 py-2 text-sm font-medium rounded-md bg-indigo-50 text-indigo-700 transition-colors'
            : 'group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        @if($icon === 'home')
            <svg class="flex-shrink-0 -mr-1 ml-3 h-6 w-6 {{ $active ? 'text-indigo-700' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
        @elseif($icon === 'chat')
            <svg class="flex-shrink-0 -mr-1 ml-3 h-6 w-6 {{ $active ? 'text-indigo-700' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
        @elseif($icon === 'messages')
            <svg class="flex-shrink-0 -mr-1 ml-3 h-6 w-6 {{ $active ? 'text-indigo-700' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        @elseif($icon === 'contacts')
            <svg class="flex-shrink-0 -mr-1 ml-3 h-6 w-6 {{ $active ? 'text-indigo-700' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        @elseif($icon === 'pdf')
            <svg class="flex-shrink-0 -mr-1 ml-3 h-6 w-6 {{ $active ? 'text-indigo-700' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6" /></svg>
        @elseif($icon === 'docs')
            <svg class="flex-shrink-0 -mr-1 ml-3 h-6 w-6 {{ $active ? 'text-indigo-700' : 'text-gray-400 group-hover:text-indigo-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
        @endif
    @else
        <!-- No icon -->
        <span class="w-2 h-2 mr-2 ml-4 rounded-full {{ $active ? 'bg-indigo-500' : 'bg-transparent border border-gray-300 group-hover:border-indigo-400' }}"></span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>
