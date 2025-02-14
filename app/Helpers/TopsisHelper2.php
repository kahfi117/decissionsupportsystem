<?php

namespace App\Helpers;

class TopsisHelper2
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
                if ($criterion['type'] === 'cost') {
                    $normalizedMatrix[$index][$key] = $sqrtSum / $alt[$key];
                } else {
                    $normalizedMatrix[$index][$key] = $alt[$key] / $sqrtSum;
                }
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

    public static function topsis_calculation($alternatif, $kriteria, $bobot, $type)
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
                if ($type === 1) {
                    $normalized_matrix[$id][$k] = $alt[$k] / $sqrt_sum;
                } else {
                    $normalized_matrix[$id][$k] = $sqrt_sum / $alt[$k];
                }
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
                $distance_positive[$id] += pow($ideal_positive[$k] - $value, 2);
                $distance_negative[$id] += pow($ideal_negative[$k] - $value, 2);
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

    public static function calculateTOPSIS($alternatives, $criteria, $weights)
    {
        $normalized_matrix = [];
        $weighted_matrix = [];
        $ideal_positive = [];
        $ideal_negative = [];
        $distance_positive = [];
        $distance_negative = [];
        $preferensi = [];

        $column_sums = [];
        $n = count($alternatives);
        $m = count($criteria);

        // 1️⃣ Hitung nilai kuadrat dari setiap kriteria
        foreach ($criteria as $key => $criterion) {
            $column_sums[$key] = 0;
            foreach ($alternatives as $alt) {
                $column_sums[$key] += pow($alt[$key], 2);
            }
            $column_sums[$key] = sqrt($column_sums[$key]); // Akar kuadrat
        }

        // 2️⃣ Normalisasi matriks
        foreach ($alternatives as $alt_key => $alt) {
            foreach ($criteria as $key => $criterion) {
                $normalized_matrix[$alt_key][$key] = $alt[$key] / $column_sums[$key];
            }
        }

        // 3️⃣ Hitung Matriks Tertimbang
        foreach ($normalized_matrix as $alt_key => $alt) {
            foreach ($criteria as $key => $criterion) {
                $weighted_matrix[$alt_key][$key] = $alt[$key] * $weights[$key];
            }
        }

        // 4️⃣ Tentukan Solusi Ideal Positif & Negatif
        foreach ($criteria as $key => $criterion) {
            if ($criterion['type'] == 'benefit') {
                $ideal_positive[$key] = max(array_column($weighted_matrix, $key));
                $ideal_negative[$key] = min(array_column($weighted_matrix, $key));
            } else {
                $ideal_positive[$key] = min(array_column($weighted_matrix, $key));
                $ideal_negative[$key] = max(array_column($weighted_matrix, $key));
            }
        }

        // 5️⃣ Hitung Jarak ke Solusi Ideal Positif & Negatif
        foreach ($weighted_matrix as $alt_key => $alt) {
            $distance_positive[$alt_key] = 0;
            $distance_negative[$alt_key] = 0;

            foreach ($criteria as $key => $criterion) {
                $distance_positive[$alt_key] += pow($alt[$key] - $ideal_positive[$key], 2);
                $distance_negative[$alt_key] += pow($alt[$key] - $ideal_negative[$key], 2);
            }

            $distance_positive[$alt_key] = sqrt($distance_positive[$alt_key]);
            $distance_negative[$alt_key] = sqrt($distance_negative[$alt_key]);
        }

        // 6️⃣ Hitung Nilai Preferensi
        foreach ($alternatives as $alt_key => $alt) {
            $preferensi[$alt_key] = $distance_negative[$alt_key] / ($distance_positive[$alt_key] + $distance_negative[$alt_key]);
        }

        // 7️⃣ Kembalikan hasil
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



