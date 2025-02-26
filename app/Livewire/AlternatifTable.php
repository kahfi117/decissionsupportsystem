<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Alternatif;
use App\Models\Category;
use App\Models\AlternatifScore;

class AlternatifTable extends Component
{
    public $topicId;
    public $alternatifs = [];
    public $categories = [];
    public $scores = [];

    public function mount($topicId)
    {
        $this->topicId = $topicId;
        $this->alternatifs = Alternatif::where('topic_id', $topicId)->get();
        $this->categories = Category::where('topic_id', $topicId)
            ->where('is_active', true)
            ->get();

        foreach ($this->alternatifs as $alt) {
            foreach ($this->categories as $cat) {
                $this->scores[$alt->id][$cat->id] = AlternatifScore::where('alternatif_id', $alt->id)
                    ->where('category_id', $cat->id)
                    ->value('value') ?? null;
            }
        }
    }

    // public function save()
    // {
    //     foreach ($this->scores as $altId => $categoryScores) {
    //         foreach ($categoryScores as $catId => $value) {
    //             AlternatifScore::updateOrCreate(
    //                 ['alternatif_id' => $altId, 'category_id' => $catId],
    //                 ['value' => $value]
    //             );
    //         }
    //     }

    //     session()->flash('message', 'Data berhasil disimpan.');
    // }

    public function updated($propertyName)
    {
        // Extract alternatif_id dan category_id dari property yang berubah
        preg_match('/scores\.(\d+)\.(\d+)/', $propertyName, $matches);
        if ($matches) {
            $altId = $matches[1];
            $catId = $matches[2];
            $value = $this->scores[$altId][$catId];

            $value = ($value === '' || $value === null) ? null : (float) $value;
            // Simpan data secara otomatis ke database
            AlternatifScore::updateOrCreate(
                ['alternatif_id' => $altId, 'category_id' => $catId],
                ['value' => $value ?? 0]
            );
        }
    }


    public function render()
    {
        return view('livewire.alternatif-table');
    }
}
