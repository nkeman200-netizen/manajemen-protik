<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_bph')->default(false)->index(); // INDEXED untuk pencarian O(log n)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_positions');
    }
};
