<?php

use App\Models\Topic;
use App\Models\Method;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('topic_methods', function (Blueprint $table) {
            $table->foreignIdFor(Topic::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Method::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_methods');
    }
};
