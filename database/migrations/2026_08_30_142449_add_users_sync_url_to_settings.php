<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key'         => 'bph_master_sync_url',
            'value'       => '',
            'type'        => 'url',
            'description' => 'URL Sync Master Data Pengurus BPH Pusat',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'bph_master_sync_url')->delete();
    }
};
