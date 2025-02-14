<?php

namespace App\Helpers;

class SawHelper
{
    /**
     * Normalisasi Matriks Keputusan
     * @param array $matrix Matriks keputusan
     * @param array $weights Bobot kriteria
     * @param array $types Jenis kriteria (1 = benefit, 0 = cost)
     * @return array Normalisasi bobot
     */
    public static function normalizeMatrix($matrix, $weights, $types, $subWeights = [])
    {
        $normalizedMatrix = [];
        $criteriaCount = count($weights);
        $rowCount = count($matrix);

        // Cari nilai max/min tiap kriteria
        $extremes = [];
        for ($j = 0; $j < $criteriaCount; $j++) {
            $columnValues = array_column($matrix, $j);
            $extremes[$j] = $types[$j] ? max($columnValues) : min($columnValues);
        }

        // Normalisasi berdasarkan benefit/cost
        for ($i = 0; $i < $rowCount; $i++) {
            for ($j = 0; $j < $criteriaCount; $j++) {
                if ($types[$j] == 1) {
                    $normalizedMatrix[$i][$j] = $matrix[$i][$j] / $extremes[$j]; // Benefit
                } else {
                    $normalizedMatrix[$i][$j] = $extremes[$j] / $matrix[$i][$j]; // Cost
                }
                // Jika ada sub-kategori, bobotnya diperhitungkan
                if (!empty($subWeights) && isset($subWeights[$j])) {
                    $normalizedMatrix[$i][$j] *= $subWeights[$j];
                }
            }
        }

        return $normalizedMatrix;
    }

    /**
     * Hitung Nilai Preferensi
     * @param array $normalizedMatrix Matriks yang telah dinormalisasi
     * @param array $weights Bobot kriteria
     * @return array Nilai preferensi alternatif
     */
    public static function calculatePreference($normalizedMatrix, $weights)
    {
        $preferences = [];

        foreach ($normalizedMatrix as $index => $row) {
            $preferenceValue = 0;
            foreach ($row as $j => $value) {
                $preferenceValue += $value * $weights[$j];
            }
            $preferences[$index] = $preferenceValue;
        }

        return $preferences;
    }

    /**
     * Mengurutkan Nilai Preferensi
     * @param array $preferences Nilai preferensi setiap alternatif
     * @return array Bobot ranking
     */
    public static function rankAlternatives($preferences)
    {
        arsort($preferences); // Mengurutkan dari terbesar ke terkecil
        return $preferences;
    }
}


