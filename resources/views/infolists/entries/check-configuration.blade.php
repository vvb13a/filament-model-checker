@php
    $configuration = $getState() ?? [];
    $hasRenderableContent = false;
@endphp

@if (is_array($configuration))
    <div
        class="check-details-view rounded-lg border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-400">
            Check Configuration:
        </span>
        <pre
            class="mt-1 w-full overflow-x-auto rounded bg-gray-100 p-2 text-xs dark:bg-gray-800">
{{ json_encode($configuration, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre
        >
        @php
            $hasRenderableContent = true;
        @endphp
    </div>
@endif
