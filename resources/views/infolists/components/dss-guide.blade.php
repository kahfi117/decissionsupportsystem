<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="p-6 mb-12 border border-primary-100 bg-gradient-to-r from-primary-50 to-indigo-50 dark:bg-primary rounded-xl">
        <h2 class="mb-4 text-xl font-semibold text-primary-900 dark:text-white">Cara Kerja</h2>
        <ol class="space-y-3 text-primary-800 dark:text-gray-300">
          @foreach ([
            '1️⃣ Tambahkan kriteria keputusan sesuai dengan faktor-faktor yang dianggap penting, lalu berikan bobot pada masing-masing kriteria sesuai tingkat kepentingannya',
            '2️⃣ Tentukan jenis setiap kriteria, apakah termasuk benefit (semakin tinggi nilainya semakin baik) atau cost (semakin rendah nilainya semakin baik).',
            '3️⃣ Masukkan alternatif pilihan yang akan dievaluasi, kemudian berikan nilai atau skor pada setiap kriteria untuk masing-masing alternatif.',
            '4️⃣ Pilih metode perhitungan yang sesuai, kemudian lihat hasil perhitungan untuk mendapatkan keputusan yang paling optimal.',
          ] as $index => $text)
            <li class="flex items-start gap-3">
              {{-- <span class="flex items-center justify-center flex-none w-6 h-6 text-sm font-medium bg-blue-600 rounded-full dark:text-white dark:bg-blue-500">
                {{ $index + 1 }}
              </span> --}}
              <span>{{ $text }}</span>
            </li>
          @endforeach
        </ol>
      </div>
</x-dynamic-component>
