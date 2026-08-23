<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. Drop tabel lama dengan urutan yang benar (child -> parent)
        Schema::dropIfExists('meeting_attendances');
        Schema::dropIfExists('meetings');

        // 2. Buat tabel Agendas (Mencakup Rapat, Gladi, Acara, dll)
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained('events')->nullOnDelete();
            $table->string('title'); // Dari "Nama Agenda"
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable(); // Dari "Tempat"
            $table->string('pic')->nullable(); // Dari "PJ/Divisi"
            $table->string('status')->nullable(); // Dari "Status"
            $table->string('minutes_url')->nullable(); // Dari "Link Notulensi"
            $table->timestamps();
        });

        // 3. Buat ulang tabel absensi untuk agenda yang membutuhkannya
        Schema::create('agenda_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->constrained('agendas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['present', 'permit', 'sick', 'absent']);
            $table->string('proof_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agenda_attendances');
        Schema::dropIfExists('agendas');
    }
};
