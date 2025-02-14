<?php

use App\Models\Alternatif;
use App\Models\Method;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rangkings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Alternatif::class)->constrained()->cascadeOnDelete();
            $table->double('score');
            $table->foreignIdFor(Method::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rangkings');
    }
};
