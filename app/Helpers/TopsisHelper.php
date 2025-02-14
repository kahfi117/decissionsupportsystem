<?php

namespace App\Helpers;

class TopsisHelper
{
    public static function calculateTOPSIS($alternatif, $alternatives, $weights, $types)
    {
        $normalized_matrix = [];
        $weighted_matrix = [];
        $ideal_positive = [];
        $ideal_negative = [];
        $distance_positive = [];
        $distance_negative = [];
        $preferensi = [];

        $n = count($alternatif); // Jumlah alternatif
        $m = count($weights); // Jumlah kriteria

        // 1️⃣ Hitung akar kuadrat dari setiap kolom
        $column_sums = array_fill(0, $m, 0);
        for ($j = 0; $j < $m; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $column_sums[$j] += pow($alternatif[$i][$j], 2);
            }
            $column_sums[$j] = sqrt($column_sums[$j]);
        }

        // 2️⃣ Normalisasi matriks
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                $normalized_matrix[$i][$j] = $alternatif[$i][$j] / $column_sums[$j];
            }
        }

        // 3️⃣ Hitung Matriks Tertimbang
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                $weighted_matrix[$i][$j] = $normalized_matrix[$i][$j] * $weights[$j];
            }
        }

        // 4️⃣ Tentukan Solusi Ideal Positif & Negatif
        for ($j = 0; $j < $m; $j++) {
            $column_values = array_column($weighted_matrix, $j);
            if ($types[$j] == 1) { // Benefit criteria
                $ideal_positive[$j] = max($column_values);
                $ideal_negative[$j] = min($column_values);
            } else { // Cost criteria
                $ideal_positive[$j] = min($column_values);
                $ideal_negative[$j] = max($column_values);
            }
        }

        // 5️⃣ Hitung Jarak ke Solusi Ideal Positif & Negatif
        for ($i = 0; $i < $n; $i++) {
            $distance_positive[$i] = 0;
            $distance_negative[$i] = 0;

            for ($j = 0; $j < $m; $j++) {
                $distance_positive[$i] += pow($weighted_matrix[$i][$j] - $ideal_positive[$j], 2);
                $distance_negative[$i] += pow($weighted_matrix[$i][$j] - $ideal_negative[$j], 2);
            }

            $distance_positive[$i] = sqrt($distance_positive[$i]);
            $distance_negative[$i] = sqrt($distance_negative[$i]);
        }

        // 6️⃣ Hitung Nilai Preferensi
        for ($i = 0; $i < $n; $i++) {
            $preferensi[$i] = $distance_negative[$i] / ($distance_positive[$i] + $distance_negative[$i]);
        }

        // 7️⃣ Gabungkan dengan nama alternatif untuk output
        $result = [];
        foreach ($alternatives as $i => $alt_name) {
            $result[] = [
                'alternative' => $alt_name,
                'score' => $preferensi[$i]
            ];
        }

        // Urutkan berdasarkan nilai preferensi tertinggi
        // usort($result, function ($a, $b) {
        //     return $b['score'] <=> $a['score'];
        // });

        // 8️⃣ Kembalikan hasil
        return [
            'normalized_matrix' => $normalized_matrix,
            'weighted_matrix' => $weighted_matrix,
            'ideal_positive' => $ideal_positive,
            'ideal_negative' => $ideal_negative,
            'distance_positive' => $distance_positive,
            'distance_negative' => $distance_negative,
            'preferensi' => $result
        ];
    }
}
