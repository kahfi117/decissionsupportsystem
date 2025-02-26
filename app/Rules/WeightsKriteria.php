<?php

namespace App\Rules;

use Closure;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;

class WeightsKriteria implements ValidationRule
{
    protected ?int $parentId;
    protected ?int $exceptId;
    protected ?int $ownerId;
    protected ?float $tb;

    public function __construct(?int $parentId = null, ?int $exceptId = null, ?int $ownerId = null)
    {
        $this->parentId = $parentId;
        $this->exceptId = $exceptId;
        $this->ownerId = $ownerId;
        $this->tb = 0;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (float) $value;

        // $fail($this->parentId ?? 'halo');
        // Jika tidak punya parent, cek range 0-1
        if (is_null($this->parentId)) {


            $totalBobot = Category::whereNull('parent_id')
                ->where('topic_id', '=', $this->ownerId)
                ->when($this->exceptId, fn($query) =>
                    $query->where('id', '!=', $this->exceptId)
                        ->where('is_active', TRUE))
                ->sum('weight');

            $totalBobot += $value;

            if ($totalBobot < 0 || $totalBobot > 1) {
                $fail("Total bobot untuk Parameter ini tidak boleh lebih dari 1. Total saat ini: $totalBobot");
            }
        } else {
            // Jika punya parent, cek jumlah bobot pada parent
            $totalBobotC = Category::where('parent_id', $this->parentId)
                ->where('topic_id', '=', $this->ownerId)
                ->when($this->exceptId, fn($query) =>
                    $query->where('id', '!=', $this->exceptId)
                        ->where('is_active', TRUE))
                ->sum('weight');

            $totalBobotC += $value;

            if ($totalBobotC > 1) {
                $fail("Total bobot untuk parent ini tidak boleh lebih dari 1. Total saat ini: $totalBobotC");
            }
        }
    }
}
