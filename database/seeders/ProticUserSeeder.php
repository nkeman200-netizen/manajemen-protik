<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProticUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Sofyan Yunus Rohman', 'nim' => '250102125'],
            ['name' => 'Raja Ubaid Fawwaz', 'nim' => '250109084'],
            ['name' => 'Almas Salsabila Fidiarti', 'nim' => '250215005'],
            ['name' => 'Sukmaratih Nirmalasari', 'nim' => '250109059'],
            ['name' => 'Ahmad Fakhri Abdullah', 'nim' => '250215003'],
            ['name' => 'Dea Ameliana Saputri', 'nim' => '250215011'],
            ['name' => 'Afif Nur Faizin', 'nim' => '250215002'],
            ['name' => 'Bintang Fajar Jolya Anggara', 'nim' => '250315037'],
            ['name' => 'Lussy Ana Syarif', 'nim' => '250202019'],
            ['name' => 'Ari Dwi Saputra', 'nim' => '250109064'],
            ['name' => 'Igo Ilham Ramadhan', 'nim' => '250102016'],
            ['name' => 'Rahmawati', 'nim' => '250102123'],
            ['name' => 'Kayla Radifan Pramudya', 'nim' => '250209079'],
            ['name' => 'Bagus Daffa Albany', 'nim' => '250102100'],
            ['name' => 'Assyifa Saisarita', 'nim' => '250302005'],
            ['name' => 'Bhadra Nur Rouf Rudin', 'nim' => '250202038'],
            ['name' => 'Hazel Ransy Krishna', 'nim' => '250315018'],
            ['name' => 'Galuh Dwi Putra', 'nim' => '250215015'],
            ['name' => 'Wanda Tiara Levina', 'nim' => '250215063'],
            ['name' => 'Faathimah Annaafi\'ah', 'nim' => '250202011'],
            ['name' => 'Ade Ariansyah Anggoro', 'nim' => '250315034'],
            ['name' => 'Nafisa Raihana', 'nim' => '250109053'],
            ['name' => 'Hikmal', 'nim' => '240109076'],
            ['name' => 'Raihan Afdhal Athallah', 'nim' => '250202027'],
            ['name' => 'Gendhis Yuwita Sari', 'nim' => '250202014'],
            ['name' => 'Rizqi Radhityanto', 'nim' => '250215060'],
            ['name' => 'Nabila Islami Cinta Widianti', 'nim' => '250202117'],
            ['name' => 'Rindang Permatasari', 'nim' => '250110024'],
            ['name' => 'Ayla Azzura Putri Mulianingrum', 'nim' => '250215008'],
        ];

        foreach ($users as $u) {
            // Ekstrak nama depan untuk email (huruf kecil semua)
            $firstName = strtolower(explode(' ', trim($u['name']))[0]);
            // Filter karakter non-alfabet (untuk kasus nama seperti Annaafi'ah)
            $firstName = preg_replace('/[^a-z]/', '', $firstName);
            $email = $firstName . '@protik.com';

            // Jangan sentuh Admin Sofyan jika emailnya sudah ada
            $existing = User::where('email', $email)->orWhere('nim', $u['nim'])->first();
            
            if (!$existing) {
                User::create([
                    'name'     => $u['name'],
                    'email'    => $email,
                    'nim'      => $u['nim'],
                    'password' => Hash::make($u['nim']),
                    'status'   => 'active',
                ]);
            } else {
                // Hanya update NIM jika masih kosong
                if (empty($existing->nim)) {
                    $existing->update(['nim' => $u['nim']]);
                }
            }
        }
    }
}
