<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('event_id')->nullable()->constrained('events')->onDelete('set null');
            $table->string('letter_number')->unique();
            $table->string('title');
            $table->string('drive_url');
            $table->timestamps();

            $table->index('created_by');
            $table->index('event_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('documents');
    }
};
