<?php

namespace App\Helpers;

class WpHelper
// {
//     /**
//      * Normalisasi bobot untuk kriteria bertingkat (rekursif).
//      * @param array $weights
//      * @return array
//      */
//     public static function normalizeNestedWeights(array $weights): array
//     {
//         $totalWeight = array_sum(array_column($weights, 'weight'));
//         $normalizedWeights = [];

//         foreach ($weights as $key => $value) {
//             if (isset($value['sub'])) {
//                 // Jika memiliki sub-kriteria, normalisasi bobot sub-kriteria
//                 $normalizedWeights[$key] = [
//                     'weight' => $value['weight'] / $totalWeight,
//                     'sub' => self::normalizeNestedWeights($value['sub'])
//                 ];
//             } else {
//                 // Jika tidak ada sub-kriteria, normalisasi langsung
//                 $normalizedWeights[$key] = $value['weight'] / $totalWeight;
//             }
//         }

//         return $normalizedWeights;
//     }

//     /**
//      * Menghitung nilai preferensi dengan metode WP untuk kriteria bertingkat (rekursif).
//      * @param array $criteriaValues
//      * @param array $weights
//      * @param array $criteriaTypes
//      * @return float
//      */
//     public static function calculateNestedPreference(array $criteriaValues, array $weights, array $criteriaTypes): float
//     {
//         $preference = 1;

//         foreach ($criteriaValues as $key => $value) {
//             if (is_array($value)) {
//                 // Jika ada sub-kriteria, lakukan rekursi
//                 $subPreference = self::calculateNestedPreference($value, $weights[$key]['sub'], $criteriaTypes[$key]);
//                 $preference *= pow($subPreference, $weights[$key]['weight']);
//             } else {
//                 // Hitung berdasarkan jenis kriteria (benefit atau cost)
//                 $exponent = ($criteriaTypes[$key] === 'benefit') ? $weights[$key] : -$weights[$key];
//                 $preference *= pow($value, $exponent);
//             }
//         }

//         return $preference;
//     }

//     /**
//      * Proses metode WP untuk kriteria bertingkat.
//      * @param array $alternatives
//      * @param array $weights
//      * @param array $criteriaTypes
//      * @return array
//      */
//     public static function wpProcessNested(array $alternatives, array $weights, array $criteriaTypes): array
//     {
//         // Normalisasi bobot terlebih dahulu
//         $normalizedWeights = self::normalizeNestedWeights($weights);

//         // Hitung nilai preferensi untuk setiap alternatif
//         $preferences = [];
//         foreach ($alternatives as $altId => $criteriaValues) {
//             $preferences[$altId] = self::calculateNestedPreference($criteriaValues, $normalizedWeights, $criteriaTypes);
//         }

//         // Ranking berdasarkan nilai preferensi
//         arsort($preferences);
//         return $preferences;
//     }
// }

{
    /**
     * Normalisasi bobot agar totalnya 1.
     * @param array $weights
     * @return array
     */
    public static function normalizeWeights(array $weights): array
    {
        $totalWeight = array_sum($weights);
        return array_map(fn($weight) => $weight / $totalWeight, $weights);
    }

    /**
     * Menghitung nilai preferensi setiap alternatif dengan metode WP.
     * @param array $alternatives Matriks alternatif
     * @param array $weights Bobot kriteria
     * @param array $criteriaTypes Jenis kriteria (benefit atau cost)
     * @return array
     */
    public static function calculatePreferences(array $alternatives, array $weights, array $criteriaTypes): array
    {
        // Normalisasi bobot
        $normalizedWeights = self::normalizeWeights($weights);

        $preferences = [];
        foreach ($alternatives as $id => $values) {
            $preference = 1;
            foreach ($values as $index => $value) {
                $exponent = ($criteriaTypes[$index] === 'benefit') ? $normalizedWeights[$index] : -$normalizedWeights[$index];
                $preference *= pow($value, $exponent);
            }
            $preferences[$id] = $preference;
        }

        return $preferences;
    }

    /**
     * Meranking alternatif berdasarkan nilai preferensi.
     * @param array $preferences
     * @return array
     */
    public static function rankAlternatives(array $preferences): array
    {
        arsort($preferences);
        return $preferences;
    }

    /**
     * Proses lengkap metode WP.
     * @param array $alternatives Matriks alternatif
     * @param array $weights Bobot kriteria
     * @param array $criteriaTypes Jenis kriteria (benefit/cost)
     * @return array
     */
    public static function wpProcess(array $alternatives, array $weights, array $criteriaTypes): array
    {
        $preferences = self::calculatePreferences($alternatives, $weights, $criteriaTypes);
        return self::rankAlternatives($preferences);
    }
}
