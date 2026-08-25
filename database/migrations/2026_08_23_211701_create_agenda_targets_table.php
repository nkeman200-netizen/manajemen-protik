<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agenda_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            
            // 'all', 'bph', 'coordinator', 'division', 'position', 'user'
            $table->string('target_type'); 
            
            // Berisi string, division_id, atau user_id (untuk target lepas)
            $table->string('target_value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agenda_targets');
    }
};
