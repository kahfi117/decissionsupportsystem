<div class="p-4">
    <div class="p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 rounded-lg dark:border-gray-600">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-gray-900 border border-gray-300 dark:border-gray-600 dark:text-gray-100">Alternatif</th>
                        @foreach ($categories as $category)
                            <th class="px-4 py-2 text-gray-900 border border-gray-300 dark:border-gray-600 dark:text-gray-100">{{ $category->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alternatifs as $alt)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 text-gray-900 border border-gray-300 dark:border-gray-600 dark:text-gray-100">{{ $alt->name }}</td>
                            @foreach ($categories as $cat)
                                <td class="px-4 py-2 border border-gray-300 dark:border-gray-600">
                                    <input type="number" step="0.01"
                                        wire:model.lazy="scores.{{ $alt->id }}.{{ $cat->id }}"
                                        class="w-full p-2 text-gray-900 border border-gray-300 rounded dark:text-gray-100 bg-gray-50 dark:bg-gray-900 dark:border-gray-600">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
