<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('documents', function (Blueprint $table) {
            // type: incoming (Surat Masuk), outgoing (Surat Keluar)
            $table->enum('type', ['incoming', 'outgoing'])->default('outgoing')->after('title');
            $table->string('origin')->nullable()->after('type'); // Instansi pengirim (Surat Masuk)
            $table->string('destination')->nullable()->after('origin'); // Instansi penerima (Surat Keluar)
        });
    }

    public function down(): void {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['type', 'origin', 'destination']);
        });
    }
};
