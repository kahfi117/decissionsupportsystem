<?php

namespace App\Helpers;

class TopsisHelper
{
    /**
     * Metode untuk menghitung perankingan dengan TOPSIS
     *
     * @param array $alternatives Daftar alternatif
     * @param array $criteria Daftar kriteria dan sub-kriteria
     * @return array Hasil perankingan
     */
    public static function topsis(array $alternatives, array $criteria)
    {
        // 1. Normalisasi Matriks Keputusan
        $normalizedMatrix = self::normalizeMatrix($alternatives, $criteria);

        // 2. Hitung Normalisasi Bobot
        $weightedMatrix = self::applyWeights($normalizedMatrix, $criteria);

        // 3. Tentukan Solusi Ideal Positif & Negatif
        list($idealPositive, $idealNegative) = self::calculateIdealSolutions($weightedMatrix, $criteria);

        // 4. Hitung Jarak terhadap Solusi Ideal
        $distances = self::calculateDistances($weightedMatrix, $idealPositive, $idealNegative);

        // 5. Hitung Nilai Preferensi Setiap Alternatif
        $ranking = self::calculatePreference($distances);

        // Urutkan berdasarkan ranking tertinggi
        usort($ranking, function ($a, $b) {
            return $b['preference'] <=> $a['preference'];
        });

        return $ranking;
    }

    private static function normalizeMatrix($alternatives, $criteria)
    {
        $normalizedMatrix = [];
        foreach ($criteria as $key => $criterion) {
            $sumSquares = array_sum(array_map(fn($alt) => pow($alt[$key], 2), $alternatives));
            $sqrtSum = sqrt($sumSquares);

            foreach ($alternatives as $index => $alt) {
                $normalizedMatrix[$index][$key] = $alt[$key] / $sqrtSum;
            }
        }
        return $normalizedMatrix;
    }

    private static function applyWeights($normalizedMatrix, $criteria)
    {
        foreach ($normalizedMatrix as $index => $alt) {
            foreach ($alt as $key => $value) {
                $normalizedMatrix[$index][$key] *= $criteria[$key]['weight'];
            }
        }
        return $normalizedMatrix;
    }

    private static function calculateIdealSolutions($weightedMatrix, $criteria)
    {
        $idealPositive = [];
        $idealNegative = [];

        foreach ($criteria as $key => $criterion) {
            $values = array_column($weightedMatrix, $key);

            if ($criterion['type'] === 'benefit') {
                $idealPositive[$key] = max($values);
                $idealNegative[$key] = min($values);
            } else {
                $idealPositive[$key] = min($values);
                $idealNegative[$key] = max($values);
            }
        }

        return [$idealPositive, $idealNegative];
    }

    private static function calculateDistances($weightedMatrix, $idealPositive, $idealNegative)
    {
        $distances = [];
        foreach ($weightedMatrix as $index => $alt) {
            $distancePositive = sqrt(array_sum(array_map(fn($key) => pow($alt[$key] - $idealPositive[$key], 2), array_keys($alt))));
            $distanceNegative = sqrt(array_sum(array_map(fn($key) => pow($alt[$key] - $idealNegative[$key], 2), array_keys($alt))));

            $distances[$index] = [
                'dPositive' => $distancePositive,
                'dNegative' => $distanceNegative
            ];
        }
        return $distances;
    }

    private static function calculatePreference($distances)
    {
        $ranking = [];
        foreach ($distances as $index => $distance) {
            $preference = $distance['dNegative'] / ($distance['dPositive'] + $distance['dNegative']);
            $ranking[] = [
                'alternative' => $index + 1,
                'preference' => $preference
            ];
        }
        return $ranking;
    }

    public static function topsis_calculation($alternatif, $kriteria, $bobot)
    {
        // Step 1: Normalisasi Matriks Keputusan
        $normalized_matrix = [];
        foreach ($kriteria as $k => $sub_kriteria) {
            $sum_square = 0;
            foreach ($alternatif as $alt) {
                $sum_square += pow($alt[$k], 2);
            }
            $sqrt_sum = sqrt($sum_square);
            foreach ($alternatif as $id => $alt) {
                $normalized_matrix[$id][$k] = $alt[$k] / $sqrt_sum;
            }
        }

        // Step 2: Normalisasi Bobot
        $weighted_matrix = [];
        foreach ($normalized_matrix as $id => $alt) {
            foreach ($alt as $k => $value) {
                $weighted_matrix[$id][$k] = $value * $bobot[$k];
            }
        }

        // Step 3: Mencari Solusi Ideal Positif & Negatif
        $ideal_positive = [];
        $ideal_negative = [];
        foreach ($kriteria as $k => $sub_kriteria) {
            $values = array_column($weighted_matrix, $k);
            $ideal_positive[$k] = max($values);
            $ideal_negative[$k] = min($values);
        }

        // Step 4: Menghitung Jarak ke Solusi Ideal Positif & Negatif
        $distance_positive = [];
        $distance_negative = [];
        foreach ($weighted_matrix as $id => $alt) {
            $distance_positive[$id] = 0;
            $distance_negative[$id] = 0;
            foreach ($alt as $k => $value) {
                $distance_positive[$id] += pow($ideal_positive[$k] -$value , 2);
                $distance_negative[$id] += pow($ideal_negative[$k] -$value , 2);
            }
            $distance_positive[$id] = sqrt($distance_positive[$id]);
            $distance_negative[$id] = sqrt($distance_negative[$id]);
        }

        // Step 5: Menghitung Preferensi Nilai
        $preferensi = [];
        foreach ($alternatif as $id => $alt) {
            $preferensi[$id] = $distance_negative[$id] / ($distance_positive[$id] + $distance_negative[$id]);
        }

        // Step 6: Ranking Alternatif
        arsort($preferensi); // Urutkan secara descending

        // Hasil Akhir
        return [
            'normalized_matrix' => $normalized_matrix,
            'weighted_matrix' => $weighted_matrix,
            'ideal_positive' => $ideal_positive,
            'ideal_negative' => $ideal_negative,
            'distance_positive' => $distance_positive,
            'distance_negative' => $distance_negative,
            'preferensi' => $preferensi
        ];
    }
}
