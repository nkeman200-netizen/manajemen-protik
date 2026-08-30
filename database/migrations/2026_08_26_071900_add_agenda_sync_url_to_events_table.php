<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('events', function (Blueprint $table) {
            $table->string('agenda_sync_url')->nullable()->after('finance_sync_url');
        });
    }

    public function down(): void {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('agenda_sync_url');
        });
    }
};
