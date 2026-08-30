<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'org_name', 'value' => 'Protik', 'type' => 'string', 'description' => 'Nama Organisasi'],
            ['key' => 'org_logo', 'value' => '', 'type' => 'file', 'description' => 'URL Logo Organisasi'],
            ['key' => 'bph_users_sync_url', 'value' => '', 'type' => 'url', 'description' => 'URL Sync Master Data Pengurus BPH Pusat'],
            ['key' => 'bph_agenda_sync_url', 'value' => '', 'type' => 'url', 'description' => 'URL Sync Agenda BPH Pusat'],
            ['key' => 'bph_document_sync_url', 'value' => '', 'type' => 'url', 'description' => 'URL Sync Dokumen BPH Pusat'],
            ['key' => 'bph_finance_sync_url', 'value' => '', 'type' => 'url', 'description' => 'URL Sync Keuangan BPH Pusat'],
            ['key' => 'bph_kas_sync_url', 'value' => '', 'type' => 'url', 'description' => 'URL Sync Kas Pengurus BPH Pusat'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
