<?php

namespace Tests\Feature;

use App\Models\Archive;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArchiveFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->member = User::factory()->create();
        $this->member->assignRole('member');
    }

    public function test_can_list_archives(): void
    {
        Archive::create([
            'period_year' => '2025/2026',
            'name'        => 'Dokumentasi LPJ 2025',
            'drive_url'   => 'https://drive.google.com/drive/folders/test1',
        ]);

        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/archives');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonCount(1, 'data');
    }

    public function test_can_create_archive(): void
    {
        $payload = [
            'period_year' => '2025/2026',
            'name'        => 'Arsip Baru',
            'drive_url'   => 'https://drive.google.com/drive/folders/test2',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/archives', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Arsip dibuat.',
            ]);

        $this->assertDatabaseHas('archives', [
            'name' => 'Arsip Baru',
        ]);
    }

    public function test_can_update_archive(): void
    {
        $archive = Archive::create([
            'period_year' => '2025/2026',
            'name'        => 'Arsip Lama',
            'drive_url'   => 'https://drive.google.com/drive/folders/test3',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/archives/{$archive->id}", [
                'name' => 'Arsip Terupdate',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Arsip diperbarui.',
            ]);

        $this->assertDatabaseHas('archives', [
            'id'   => $archive->id,
            'name' => 'Arsip Terupdate',
        ]);
    }

    public function test_can_delete_archive(): void
    {
        $archive = Archive::create([
            'period_year' => '2025/2026',
            'name'        => 'Arsip Hapus',
            'drive_url'   => 'https://drive.google.com/drive/folders/test4',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/archives/{$archive->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Arsip dihapus.',
            ]);

        $this->assertDatabaseMissing('archives', [
            'id' => $archive->id,
        ]);
    }

    public function test_can_get_and_batch_update_settings(): void
    {
        Setting::create([
            'key'         => 'org_name',
            'value'       => 'Protik',
            'type'        => 'string',
            'description' => 'Nama Organisasi',
        ]);

        // Get settings
        $response = $this->actingAs($this->member, 'sanctum')
            ->getJson('/api/settings');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data'    => [
                    'org_name' => 'Protik',
                ],
            ]);

        // Update batch settings by admin
        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/settings/batch', [
                'settings' => [
                    ['key' => 'org_name', 'value' => 'Protik Indonesia'],
                ],
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Pengaturan berhasil diperbarui.',
            ]);

        $this->assertDatabaseHas('settings', [
            'key'   => 'org_name',
            'value' => 'Protik Indonesia',
        ]);
    }

    public function test_admin_can_upload_logo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('custom_logo.png');

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/settings/logo', [
                'logo' => $file,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logo berhasil diunggah.',
            ]);

        $this->assertDatabaseHas('settings', [
            'key'  => 'org_logo',
            'type' => 'file',
        ]);
    }

    public function test_setting_seeder_includes_all_required_keys(): void
    {
        $this->seed(\Database\Seeders\SettingSeeder::class);

        $expectedKeys = [
            'org_name',
            'org_logo',
            'bph_users_sync_url',
            'bph_agenda_sync_url',
            'bph_document_sync_url',
            'bph_finance_sync_url',
            'bph_kas_sync_url',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertDatabaseHas('settings', [
                'key' => $key,
            ]);
        }
    }
}
