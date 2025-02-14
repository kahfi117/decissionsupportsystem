<?php

namespace App\Http\Controllers;

use App\Helpers\WpHelper;
use App\Helpers\SawHelper;
use Illuminate\Http\Request;
use App\Helpers\TopsisHelper;

class WpController extends Controller
{
    public function start()
    {

        // Data alternatif dari tabel yang Anda berikan
        $alternatives = [
            'Indra' => ['C1' => 70, 'C2' => 50, 'C3' => 80, 'C4' => 60],
            'Roni' => ['C1' => 50, 'C2' => 60, 'C3' => 82, 'C4' => 70],
            'Putri' => ['C1' => 85, 'C2' => 55, 'C3' => 80, 'C4' => 75],
            'Dani' => ['C1' => 82, 'C2' => 70, 'C3' => 65, 'C4' => 85],
            'Ratna' => ['C1' => 75, 'C2' => 75, 'C3' => 85, 'C4' => 74],
            'Mira' => ['C1' => 62, 'C2' => 50, 'C3' => 75, 'C4' => 80]
        ];

        // Bobot kriteria
        $weights = [
            'C1' => 0.5,
            'C2' => 0.3,
            'C3' => 0.4,
            'C4' => 0.2
        ];

        // Jenis kriteria (semua benefit)
        $criteriaTypes = [
            'C1' => 'benefit',
            'C2' => 'benefit',
            'C3' => 'benefit',
            'C4' => 'benefit'
        ];

        // Jalankan metode WP untuk nested criteria
        // $ranking = WpHelper::wpProcess($alternatives, $weights, $criteriaTypes);
        $ranking = WpHelper::calculatePreferences($alternatives, $weights, $criteriaTypes);
        $weights = WpHelper::normalizeWeights($weights);

        $criteriaKeys = array_keys($weights);
        $result = [];

        foreach ($alternatives as $name => $criteria) {
            $calculatedValues = [];
            foreach ($criteriaKeys as $key) {
                $calculatedValues[$key] = isset($criteria[$key]) ? round($criteria[$key] * $weights[$key], 3) : 0;
            }
            $result[$name] = $calculatedValues;
        }


        // $normalizeNestedWeights = WpHelper::normalizeNestedWeights($weights);

        return response()->json($result);
    }

    public function calculateSAW()
    {
        // Data alternatif dengan sub-kategori
        $matrix = [
            [150, 10, 5, 2, 2, 3],  // A1
            [500, 150, 50, 2, 3, 2], // A2
            [200, 5, 5, 3, 1, 3],  // A3
            [350, 75, 25, 3, 1, 2]  // A4
        ];

        // Alternatif
        $alternatives = ["A1", "A2", "A3", "A4"];

        // Bobot kategori utama (C1, C2, C3, C4, C5)
        $weights = [0.250, 0.090, 0.060, 0.300, 0.250, 0.050];

        // Jenis kriteria (1 = benefit, 0 = cost)
        $types = [1, 0, 1, 1, 0, 1];

        // Normalisasi Bobot
        $normalizedMatrix = SawHelper::normalizeMatrix($matrix, $weights, $types);

        // Nilai Preferensi
        $preferences = SawHelper::calculatePreference($normalizedMatrix, $weights);

        // Ranking
        $ranking = SawHelper::rankAlternatives($preferences);

        return response()->json([
            'normalized_matrix' => $normalizedMatrix,
            'preferences' => $preferences,
            'ranking' => $ranking
        ]);
    }

    public function calculateTopsis()
    {
        // Data alternatif dengan sub-kategori
        $alternatif = [
            [3, 3, 3, 2],  // A4
            [4, 4, 3, 2],  // A4
            [4, 5, 3, 3],  // A4
            [3, 4, 4, 4],  // A4
            [4, 5, 4, 4.2],  // A4
        ];

        // Alternatif
        $alternatives = ["A1", "A2", "A3", "A4", "E"];

        // Bobot kategori utama (C1, C2, C3, C4, C5, C6)
        $weights = [0.10, 0.20, 0.20, 0.50];

        // Jenis kriteria (1 = benefit, 0 = cost)
        $types = [1, 1, 1, 0];

        // Jalankan perhitungan TOPSIS
        $result = TopsisHelper::calculateTOPSIS($alternatif, $alternatives, $weights, $types);

        return response()->json($result);
    }
    public function calculateWp()
    {
        // Data alternatif dengan sub-kategori
        $alternatif = [
            [70, 50, 80, 60],  // A4
            [50, 60, 82, 70],  // A4
            [85, 55, 80, 75],  // A4
            [82, 70, 65, 85],  // A4
            [75, 75, 85, 74],  // A4
            [62, 50, 75, 80],  // A4
        ];

        // Alternatif
        $alternatives = ["A1", "A2", "A3", "A4", "E", "F"];

        // Bobot kategori utama (C1, C2, C3, C4, C5, C6)
        $weights = [5, 3, 4, 2];

        // Jenis kriteria (1 = benefit, 0 = cost)
        $types = [1, 1, 1, 1];

        // Jalankan perhitungan WP
        // $result = TopsisHelper::calculateTOPSIS($alternatif, $alternatives, $weights, $types);
        $result = WpHelper::calculateWP($alternatif, $alternatives, $weights, $types);

        return response()->json($result);
    }
}