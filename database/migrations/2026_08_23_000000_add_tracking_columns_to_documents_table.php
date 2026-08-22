<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->date('activity_date')->nullable()->after('title');
            $table->string('letter_link')->nullable()->after('drive_url');
            $table->string('scan_link')->nullable()->after('letter_link');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['activity_date', 'letter_link', 'scan_link']);
        });
    }
};
