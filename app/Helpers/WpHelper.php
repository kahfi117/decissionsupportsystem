<?php

namespace App\Helpers;

class WpHelper
{
    public static function calculateWP($alternatif, $alternatives, $weights, $types)
    {
        $n = count($alternatif); // Jumlah alternatif
        $m = count($weights); // Jumlah kriteria
        $normalized_weights = [];
        $preference_scores = [];
        $ranking_weights = [];
        $calweightrank = [];

        // 1️⃣ Normalisasi bobot agar totalnya = 1
        $total_weight = array_sum($weights);
        foreach ($weights as $j => $weight) {
            $normalized_weights[$j] = round($weight / $total_weight, 3);
        }

        // 2️⃣ Hitung nilai preferensi WP
        for ($i = 0; $i < $n; $i++) {
            $preference_scores[$i] = 1;
            for ($j = 0; $j < $m; $j++) {
                // Jika benefit → pangkat positif, jika cost → pangkat negatif
                $power = $types[$j] == 1 ? $normalized_weights[$j] : -$normalized_weights[$j];
                $preference_scores[$i] *= pow($alternatif[$i][$j], $power);
            }
            $preference_scores[$i] = round($preference_scores[$i], 3);
        }

        // 3️⃣ Normalisasi nilai preferensi WP
        $sum_preference_scores = array_sum($preference_scores);
        foreach ($preference_scores as $i => $score) {
            $ranking_weights[$i] = round($score / $sum_preference_scores, 3); // Bobot ranking
            $calweightrank[$i] = round($score / $sum_preference_scores, 3); // Perhitungan sesuai Excel
        }

        // 4️⃣ Gabungkan dengan nama alternatif untuk output
        $result = [];
        foreach ($alternatives as $i => $alt_name) {
            $result[] = [
                'alternative' => $alt_name,
                'score' => $preference_scores[$i],
                'ranking_weight' => $ranking_weights[$i],
                'calweightrank' => $calweightrank[$i]
            ];
        }

        // 5️⃣ Urutkan berdasarkan nilai bobot ranking tertinggi
        // usort($result, function ($a, $b) {
        //     return $b['ranking_weight'] <=> $a['ranking_weight'];
        // });

        // 6️⃣ Kembalikan hasil
        return [
            'normalized_weights' => $normalized_weights,
            'preference_scores' => $preference_scores,
            'ranking_weights' => $ranking_weights,
            'calweightrank' => $calweightrank,
            'ranking' => $result
        ];
    }
}
