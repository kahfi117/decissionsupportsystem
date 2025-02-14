<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class CalculateSaw
{
    /**
     * Normalisasi matriks berdasarkan jenis kriteria (Benefit atau Cost)
     *
     * @param Collection $alternatifScores Data alternatif beserta nilai kriteria
     * @param array $kriteria Array kriteria dengan tipe (benefit/cost)
     * @return array Matriks yang telah dinormalisasi
     */
    public static function normalize(Collection $alternatifScores, array $kriteria): array
    {
        $normalizedMatrix = [];
        $maxValues = [];
        $minValues = [];

        // Menentukan nilai max dan min untuk setiap kriteria
        foreach ($kriteria as $key => $type) {
            $maxValues[$key] = $alternatifScores->max($key);
            $minValues[$key] = $alternatifScores->min($key);
        }

        // Normalisasi
        foreach ($alternatifScores as $alternatif) {
            $normalizedRow = ['id' => $alternatif->id, 'name' => $alternatif->name];

            foreach ($kriteria as $key => $type) {
                if ($type === 'benefit') {
                    $normalizedRow[$key] = $alternatif->$key / $maxValues[$key];
                } else {
                    $normalizedRow[$key] = $minValues[$key] / $alternatif->$key;
                }
            }

            $normalizedMatrix[] = $normalizedRow;
        }

        return $normalizedMatrix;
    }

    /**
     * Menghitung skor SAW berdasarkan bobot kriteria
     *
     * @param array $normalizedMatrix Matriks yang telah dinormalisasi
     * @param array $bobotKriteria Bobot untuk setiap kriteria
     * @return array Skor SAW untuk setiap alternatif
     */
    public static function calculateSAWScore(array $normalizedMatrix, array $bobotKriteria): array
    {
        $scores = [];

        foreach ($normalizedMatrix as $row) {
            $score = 0;
            foreach ($bobotKriteria as $key => $bobot) {
                $score += $row[$key] * $bobot;
            }
            $scores[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'score' => round($score, 4)
            ];
        }

        // Urutkan berdasarkan skor (tertinggi ke terendah)
        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        // Tambahkan peringkat
        foreach ($scores as $index => &$score) {
            $score['rank'] = $index + 1;
        }

        return $scores;
    }
}
