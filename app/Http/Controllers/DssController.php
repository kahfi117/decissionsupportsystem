<?php

namespace App\Http\Controllers;

use App\Helpers\WpHelper;
use App\Models\Topic;
use App\Models\Method;
use App\Models\Category;
use App\Models\Rangking;
use App\Helpers\SawHelper;
use App\Models\Alternatif;
use Illuminate\Http\Request;
use App\Helpers\CalculateSaw;
use App\Helpers\TopsisHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class DssController extends Controller
{
    public function sawCalculation($topicId): void
    {
        $alternatifs = Alternatif::with('scores')
            ->whereHas('topic', function ($q) use ($topicId): void {
                $q->where('id', $topicId);
            })
            ->get();

        $categories = Category::where('topic_id', $topicId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        // Buat matriks alternatif
        $alternatifMatrix = [];
        foreach ($alternatifs as $alternatif) {
            $row = [];
            for ($i = 0; $i < count($categories); $i++) {
                $category_id = $categories[$i];
                $score = $alternatif->scores->where('category_id', $category_id)->first();
                $row[] = $score ? $score->value : 0;
            }
            $alternatifMatrix[] = $row;
        }

        $weights = $this->getWeights($topicId);

        $types = $this->getCategoryTypes($topicId);

        // Normalisasi Bobot
        $normalizedMatrix = SawHelper::normalizeMatrix($alternatifMatrix, $weights, $types);

        // Nilai Preferensi
        $preferences = SawHelper::calculatePreference($normalizedMatrix, $weights);

        // Ranking
        $ranking = SawHelper::rankAlternatives($preferences);


        $methodId = Method::where('name', 'saw')->first()->id; // Pastikan Anda menggunakan metode yang sesuai

        DB::transaction(function () use ($ranking, $alternatifs, $methodId) {
            foreach ($ranking as $index => $score) {
                $alternatif = $alternatifs[$index]; // Ambil alternatif berdasarkan urutan

                if (!$alternatif->trashed()) {
                    $rankingData[] = [
                        'alternatif_id' => $alternatif->id,
                        'method_id' => $methodId,
                        'score' => $score
                    ];
                }
            }

            // Simpan/update ranking
            foreach ($rankingData as $data) {
                Rangking::updateOrCreate(
                    [
                        'alternatif_id' => $data['alternatif_id'],
                        'method_id' => $methodId
                    ],
                    [
                        'score' => $data['score']
                    ]
                );
            }

            // Perbarui ranking berdasarkan skor (descending)
            $rankings = Rangking::where('method_id', $methodId)
                ->orderByDesc('score')
                ->get();

            foreach ($rankings as $index => $rank) {
                $rank->update(['rank' => $index + 1]);
            }

        });

        Notification::make()
            ->title('Generated successfully')
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
    }

    public function topsisCalculation($topicId)
    {

        $alternatifs = Alternatif::with('scores')
            ->whereHas('topic', function ($q) use ($topicId): void {
                $q->where('id', $topicId);
            })
            ->get();

        $categories = Category::where('topic_id', $topicId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $alter = Alternatif::where('topic_id', $topicId)
            ->pluck('name')
            ->toArray();

        // Buat matriks alternatif
        $alternatifMatrix = [];
        foreach ($alternatifs as $alternatif) {
            $row = [];
            for ($i = 0; $i < count($categories); $i++) {
                $category_id = $categories[$i];
                $score = $alternatif->scores->where('category_id', $category_id)->first();
                $row[] = $score ? $score->value : 0;
            }
            $alternatifMatrix[] = $row;
        }

        $weights = $this->getWeights($topicId);

        $types = $this->getCategoryTypes($topicId);

        $result = TopsisHelper::calculateTOPSIS($alternatifMatrix, $alter, $weights, $types);

        $ranking = [];
        foreach ($result['preferensi'] as $res) {
            $ranking[] = $res['score'];
        }

        $methodId = Method::where('name', 'topsis')->first()->id;

        DB::transaction(function () use ($ranking, $alternatifs, $methodId) {
            foreach ($ranking as $index => $score) {
                $alternatif = $alternatifs[$index]; // Ambil alternatif berdasarkan urutan

                if (!$alternatif->trashed()) {
                    $rankingData[] = [
                        'alternatif_id' => $alternatif->id,
                        'method_id' => $methodId,
                        'score' => $score
                    ];
                }
            }

            // Simpan/update ranking
            foreach ($rankingData as $data) {
                Rangking::updateOrCreate(
                    [
                        'alternatif_id' => $data['alternatif_id'],
                        'method_id' => $methodId
                    ],
                    [
                        'score' => $data['score']
                    ]
                );
            }

            // Perbarui ranking berdasarkan skor (descending)
            $rankings = Rangking::where('method_id', $methodId)
                ->orderByDesc('score')
                ->get();

            foreach ($rankings as $index => $rank) {
                $rank->update(['rank' => $index + 1]);
            }

        });


        return response()->json($ranking);
    }
    public function wpCalculation($topicId)
    {

        $alternatifs = Alternatif::with('scores')
            ->whereHas('topic', function ($q) use ($topicId): void {
                $q->where('id', $topicId);
            })
            ->get();

        $categories = Category::where('topic_id', $topicId)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $alter = Alternatif::where('topic_id', $topicId)
            ->pluck('name')
            ->toArray();

        // Buat matriks alternatif
        $alternatifMatrix = [];
        foreach ($alternatifs as $alternatif) {
            $row = [];
            for ($i = 0; $i < count($categories); $i++) {
                $category_id = $categories[$i];
                $score = $alternatif->scores->where('category_id', $category_id)->first();
                $row[] = $score ? $score->value : 0;
            }
            $alternatifMatrix[] = $row;
        }

        $weights = $this->getWeights($topicId);

        $types = $this->getCategoryTypes($topicId);

        $result = WpHelper::calculateWP($alternatifMatrix, $alter, $weights, $types);

        $ranking = [];
        foreach ($result['ranking'] as $res) {
            $ranking[] = $res['ranking_weight'];
        }

        $methodId = Method::where('name', 'wp')->first()->id;

        DB::transaction(function () use ($ranking, $alternatifs, $methodId) {
            foreach ($ranking as $index => $score) {
                $alternatif = $alternatifs[$index]; // Ambil alternatif berdasarkan urutan

                if (!$alternatif->trashed()) {
                    $rankingData[] = [
                        'alternatif_id' => $alternatif->id,
                        'method_id' => $methodId,
                        'score' => $score
                    ];
                }
            }

            // Simpan/update ranking
            foreach ($rankingData as $data) {
                Rangking::updateOrCreate(
                    [
                        'alternatif_id' => $data['alternatif_id'],
                        'method_id' => $methodId
                    ],
                    [
                        'score' => $data['score']
                    ]
                );
            }

            // Perbarui ranking berdasarkan skor (descending)
            $rankings = Rangking::where('method_id', $methodId)
                ->orderByDesc('score')
                ->get();

            foreach ($rankings as $index => $rank) {
                $rank->update(['rank' => $index + 1]);
            }

        });


        return response()->json($ranking);
    }

    private function getWeights($topicId): array
    {
        $weights = Category::where('topic_id', '=', $topicId)
            ->where('is_active', true)
            ->pluck('weight', 'id')
            ->toArray();

        return array_values($weights);
    }

    private function getCategoryTypes($topicId): array
    {
        $type = Category::where('topic_id', '=', $topicId)
            ->where('is_active', true)
            ->pluck('cat', 'id')->toArray();

        return array_values($type);
    }
}
