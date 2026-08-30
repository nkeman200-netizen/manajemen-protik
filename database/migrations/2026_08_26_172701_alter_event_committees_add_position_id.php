<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_committees', function (Blueprint $table) {
            // Hapus kolom string lama jika ada
            if (Schema::hasColumn('event_committees', 'position')) {
                $table->dropColumn('position');
            }
            
            // Tambahkan Foreign Key baru
            $table->foreignId('position_id')
                  ->after('user_id')
                  ->nullable()
                  ->constrained('committee_positions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event_committees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('position_id');
            $table->string('position')->nullable();
        });
    }
};
