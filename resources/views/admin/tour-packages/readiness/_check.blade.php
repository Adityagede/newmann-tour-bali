<li class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 py-3">
    <div class="min-w-0">
        <p class="text-sm font-semibold text-newman-navy">
            {{ $check['label'] }}
            @if ($check['severity'] === 'warning')
                <span class="ml-1 rounded bg-amber-50 px-2 py-0.5 text-xs text-amber-800">Warning</span>
            @endif
        </p>
        <p class="mt-1 text-sm text-gray-600">
            {{ $check['message'] }}
            @if (isset($check['context']['current_count'], $check['context']['minimum_count']))
                {{ $check['context']['current_count'] }} of {{ $check['context']['minimum_count'] }} completed.
            @endif
        </p>
    </div>
    <a href="{{ $url }}" class="text-sm font-semibold text-newman-blue hover:underline">
        {{ $check['severity'] === 'warning' ? 'Review' : 'Open section' }}
        <span class="sr-only">{{ $check['label'] }}</span>
    </a>
</li>
