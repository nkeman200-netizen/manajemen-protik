<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CLEAR CACHE & SETUP ROLES
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'advisor', 'guard_name' => 'web']);

        // 2. SETUP DIVISIONS
        $divisionNames = [
            'BPH', 'Divisi Web', 'Divisi Mobile', 'Divisi UI/UX', 
            'Divisi DevOps', 'Divisi Data', 'Divisi Humas', 'Divisi Kominfo'
        ];
        foreach ($divisionNames as $name) {
            Division::firstOrCreate(['name' => $name]);
        }

        // 3. SETUP USERS (SINGLE SOURCE OF TRUTH DARI PDF STO)
        $usersData = [
            // PEMBINA
            ['name' => 'Rahmawan Bagus Trianto, S.Kom, M.Kom', 'nim' => '199112012024061001', 'phone' => '087746310727', 'prodi' => null, 'role' => 'advisor', 'div' => null, 'is_coord' => false],
            
            // BPH (ADMIN)
            ['name' => 'Sofyan Yunus Rohman', 'nim' => '250102125', 'phone' => '088802457102', 'prodi' => 'TI', 'role' => 'admin', 'div' => 'BPH', 'is_coord' => false, 'pass' => '1212'],
            ['name' => 'Raja Ubaid Fawwaz', 'nim' => '250109084', 'phone' => '085183700433', 'prodi' => 'RKS', 'role' => 'admin', 'div' => 'BPH', 'is_coord' => false],
            ['name' => 'Almas Salsabila Fidiarti', 'nim' => '250215005', 'phone' => '085727669488', 'prodi' => 'TRPL', 'role' => 'admin', 'div' => 'BPH', 'is_coord' => false],
            ['name' => 'Sukmaratih Nirmalasari', 'nim' => '250109059', 'phone' => '082136392612', 'prodi' => 'RKS', 'role' => 'admin', 'div' => 'BPH', 'is_coord' => false],
            ['name' => 'Ahmad Fakhri Abdullah', 'nim' => '250215003', 'phone' => '089602469511', 'prodi' => 'TRPL', 'role' => 'admin', 'div' => 'BPH', 'is_coord' => false],
            ['name' => 'Dea Ameliana Saputri', 'nim' => '250215011', 'phone' => '085777269126', 'prodi' => 'TRPL', 'role' => 'admin', 'div' => 'BPH', 'is_coord' => false],

            // KOORDINATOR DIVISI (MEMBER)
            ['name' => 'Afif Nur Faizin', 'nim' => '250215002', 'phone' => '0895384922113', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi Web', 'is_coord' => true],
            ['name' => 'Bintang Fajar Jolya Anggara', 'nim' => '250315037', 'phone' => '085173384560', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi Mobile', 'is_coord' => true],
            ['name' => 'Lussy Ana Syarif', 'nim' => '250202019', 'phone' => '088237169266', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi UI/UX', 'is_coord' => true],
            ['name' => 'Ari Dwi Saputra', 'nim' => '250109064', 'phone' => '085869592005', 'prodi' => 'RKS', 'role' => 'member', 'div' => 'Divisi DevOps', 'is_coord' => true],
            ['name' => 'Igo Ilham Ramadhan', 'nim' => '250102016', 'phone' => '088802660915', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Data', 'is_coord' => true],
            ['name' => 'Rahmawati', 'nim' => '250102123', 'phone' => '0882008288696', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Humas', 'is_coord' => true],
            ['name' => 'Kayla Radifan Pramudya', 'nim' => '250209079', 'phone' => '085974088420', 'prodi' => 'RKS', 'role' => 'member', 'div' => 'Divisi Kominfo', 'is_coord' => true],

            // ANGGOTA DIVISI (MEMBER)
            ['name' => 'Bagus Daffa Albany', 'nim' => '250102100', 'phone' => '082134059578', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Web', 'is_coord' => false],
            ['name' => 'Assyifa Saisarita', 'nim' => '250302005', 'phone' => '0816652097', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Web', 'is_coord' => false],
            ['name' => 'Bhadra Nur Rouf Rudin', 'nim' => '250202038', 'phone' => '081325326819', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Mobile', 'is_coord' => false],
            ['name' => 'Hazel Ransy Krishna', 'nim' => '250315018', 'phone' => '089677500703', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi Mobile', 'is_coord' => false],
            ['name' => 'Galuh Dwi Putra', 'nim' => '250215015', 'phone' => '082133598541', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi UI/UX', 'is_coord' => false],
            ['name' => 'Wanda Tiara Levina', 'nim' => '250215063', 'phone' => '088238162248', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi UI/UX', 'is_coord' => false],
            ['name' => 'Faathimah Annaafi\'ah', 'nim' => '250202011', 'phone' => '081225373339', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi UI/UX', 'is_coord' => false],
            ['name' => 'Ade Ariansyah Anggoro', 'nim' => '250315034', 'phone' => '082136552823', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi DevOps', 'is_coord' => false],
            ['name' => 'Nafisa Raihana', 'nim' => '250109053', 'phone' => '085942102402', 'prodi' => 'RKS', 'role' => 'member', 'div' => 'Divisi DevOps', 'is_coord' => false],
            ['name' => 'Hikmal', 'nim' => '240109076', 'phone' => '087797365066', 'prodi' => 'RKS', 'role' => 'member', 'div' => 'Divisi DevOps', 'is_coord' => false],
            ['name' => 'Raihan Afdhal Athallah', 'nim' => '250202027', 'phone' => '081464435647', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Data', 'is_coord' => false],
            ['name' => 'Gendhis Yuwita Sari', 'nim' => '250202014', 'phone' => '085951400581', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Data', 'is_coord' => false],
            ['name' => 'Rizqi Radhityanto', 'nim' => '250215060', 'phone' => '08813992163', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi Humas', 'is_coord' => false],
            ['name' => 'Nabila Islami Cinta Widianti', 'nim' => '250202117', 'phone' => '085166481629', 'prodi' => 'TI', 'role' => 'member', 'div' => 'Divisi Humas', 'is_coord' => false],
            ['name' => 'Rindang Permatasari', 'nim' => '250110024', 'phone' => '085641559302', 'prodi' => 'ALKS', 'role' => 'member', 'div' => 'Divisi Kominfo', 'is_coord' => false],
            ['name' => 'Ayla Azzura Putri Mulianingrum', 'nim' => '250215008', 'phone' => '089609904487', 'prodi' => 'TRPL', 'role' => 'member', 'div' => 'Divisi Kominfo', 'is_coord' => false],
        ];

        $adminSofyanId = null;

        foreach ($usersData as $u) {
            $firstName = strtolower(explode(' ', trim($u['name']))[0]);
            $firstName = preg_replace('/[^a-z]/', '', $firstName);
            // Handling nama "Faathimah" agar emailnya tidak aneh
            if ($firstName === 'faathimah') {
                $firstName = 'faathimah';
            }
            $email    = $firstName . '@protik.com';
            $divId    = $u['div'] ? Division::where('name', $u['div'])->first()->id : null;
            $password = isset($u['pass']) ? $u['pass'] : $u['nim'];

            $user = User::create([
                'name'           => $u['name'],
                'email'          => $email,
                'nim'            => $u['nim'],
                'phone'          => $u['phone'],
                'prodi'          => $u['prodi'],
                'division_id'    => $divId,
                'is_coordinator' => $u['is_coord'],
                'password'       => Hash::make($password),
                'status'         => 'active',
            ]);

            $user->assignRole($u['role']);

            if ($u['name'] === 'Sofyan Yunus Rohman') {
                $adminSofyanId = $user->id;
            }
        }

        // 4. SETUP EVENT: MAKRAB PROTIC 2026
        if ($adminSofyanId) {
            $makrab = Event::create([
                'name'              => 'Makrab Protic 2026',
                'budget_approved'   => 769000.00,
                'start_date'        => '2026-12-11',
                'end_date'          => '2026-12-12',
                'document_sync_url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQvJV5TM2D0ujaKNSKT6N5MewF8UHs3b5VT7fPKkbzvQrmSdtlu5LQfmWObpOJkjVgQC_slcKiRUK0_/pub?gid=1458019832&single=true&output=csv',
                'finance_sync_url'  => 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQvJV5TM2D0ujaKNSKT6N5MewF8UHs3b5VT7fPKkbzvQrmSdtlu5LQfmWObpOJkjVgQC_slcKiRUK0_/pub?gid=1948428631&single=true&output=csv',
            ]);

            Finance::create([
                'user_id'        => $adminSofyanId,
                'event_id'       => $makrab->id,
                'type'           => 'income',
                'category'       => 'Saldo Awal',
                'title'          => 'Dana IOM Cair',
                'description'    => 'Dana IOM Cair',
                'funding_source' => 'IOM',
                'pic'            => 'Fakhri',
                'payment_method' => 'Cash',
                'qty'            => 1,
                'unit'           => 'Paket',
                'unit_price'     => 669000.00,
                'amount'         => 669000.00,
                'receipt_url'    => 'https://docs.google.com/document/u/0/d/1Bb8lkrxJJ7YwoqmX-gw2B7RvrPhF2pIh/edit',
                'date'           => '2026-08-22',
            ]);

            Document::create([
                'created_by'    => $adminSofyanId,
                'event_id'      => $makrab->id,
                'letter_number' => '178/PM/PROTIC/VIII/2027',
                'title'         => 'Surat Peminjaman Vila',
                'letter_link'   => 'https://drive.google.com/open?id=1m8Hrq7MmJ0tDAlOaWSaD5naqcBZNH9uG',
                'scan_link'     => 'https://drive.google.com/open?id=1WnKetLMYji-HsvbaCLGeqIj_zULiStgq',
                'activity_date' => '2026-12-11',
                'created_at'    => '2026-08-22 20:42:58',
            ]);
        }
    }
}
