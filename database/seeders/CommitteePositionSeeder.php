<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommitteePosition;

class CommitteePositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Ketua Pelaksana', 'is_bph' => true],
            ['name' => 'Wakil Ketua', 'is_bph' => true],
            ['name' => 'Sekretaris', 'is_bph' => true],
            ['name' => 'Bendahara', 'is_bph' => true],
            ['name' => 'Koordinator Acara', 'is_bph' => false],
            ['name' => 'Anggota Acara', 'is_bph' => false],
            ['name' => 'Koordinator Humas', 'is_bph' => false],
            ['name' => 'Anggota Humas', 'is_bph' => false],
            ['name' => 'Koordinator Pubdekdok', 'is_bph' => false],
            ['name' => 'Anggota Pubdekdok', 'is_bph' => false],
        ];

        foreach ($positions as $pos) {
            CommitteePosition::updateOrCreate(['name' => $pos['name']], $pos);
        }
    }
}
