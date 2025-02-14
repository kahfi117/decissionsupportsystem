<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="p-6 mb-12 border border-primary-100 bg-gradient-to-r from-primary-50 to-indigo-50 dark:bg-primary rounded-xl">
        <h2 class="mb-4 text-xl font-semibold text-primary-900 dark:text-white">How it works</h2>
        <ol class="space-y-3 text-primary-800 dark:text-gray-300">
          @foreach ([
            'Add your decision criteria and assign weights to reflect their importance',
            'Specify if each criterion is a benefit (higher is better) or cost (lower is better)',
            'Add alternatives and provide scores for each criterion',
            'Select a method and view the calculated results for optimal decision making',
          ] as $index => $text)
            <li class="flex items-start gap-3">
              <span class="flex items-center justify-center flex-none w-6 h-6 text-sm font-medium bg-blue-600 rounded-full dark:text-white dark:bg-blue-500">
                {{ $index + 1 }}
              </span>
              <span>{{ $text }}</span>
            </li>
          @endforeach
        </ol>
      </div>
</x-dynamic-component>
