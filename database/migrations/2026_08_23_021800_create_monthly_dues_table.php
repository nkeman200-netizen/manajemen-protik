<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('monthly_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('month'); // 1-12
            $table->integer('year');
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
            
            // Mencegah duplikasi data per bulan untuk user yang sama
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('monthly_dues');
    }
};
