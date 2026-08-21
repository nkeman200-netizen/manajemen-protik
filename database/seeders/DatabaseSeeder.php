<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Document;
use App\Models\Event;
use App\Models\Finance;
use App\Models\Meeting;
use App\Models\MeetingAttendance;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $this->call(RolePermissionSeeder::class);

        // 2. Divisi
        $divisions = collect(['BPH', 'Pendidikan', 'Ristek', 'Humas', 'Ekraf'])
            ->map(fn (string $name) => Division::create(['name' => $name]));

        $divisionBph    = $divisions->firstWhere('name', 'BPH');
        $divisionRistek = $divisions->firstWhere('name', 'Ristek');

        // 3. User Statis
        $admin = User::factory()->create([
            'name'        => 'Admin Sofyan',
            'email'       => 'admin@protik.com',
            'division_id' => $divisionBph->id,
        ]);
        $admin->assignRole('admin');

        $member = User::factory()->create([
            'name'        => 'Member User',
            'email'       => 'member@protik.com',
            'division_id' => $divisionRistek->id,
        ]);
        $member->assignRole('member');

        $advisor = User::factory()->create([
            'name'        => 'Advisor User',
            'email'       => 'advisor@protik.com',
            'division_id' => null,
        ]);
        $advisor->assignRole('advisor');

        // 4. 20 User Acak (member)
        $randomMembers = User::factory()->count(20)->create([
            'division_id' => fn () => $divisions->random()->id,
        ]);
        $randomMembers->each(fn (User $u) => $u->assignRole('member'));

        // Kumpulkan semua member untuk attendance
        $allMembers = collect([$member])->merge($randomMembers);

        // 5. 5 Event
        $events = Event::factory()->count(5)->create();

        // 6. Finance per Event (1 income besar, 1 expense kecil)
        $fundingSources = ['IOM', 'DIPA', 'KAS', 'SPONSOR'];

        $events->each(function (Event $event) use ($admin, $fundingSources) {
            Finance::create([
                'user_id'        => $admin->id,
                'event_id'       => $event->id,
                'type'           => 'income',
                'funding_source' => fake()->randomElement($fundingSources),
                'amount'         => fake()->randomFloat(2, 5_000_000, 50_000_000),
                'description'    => "Dana masuk untuk {$event->name}",
                'date'           => $event->start_date?->format('Y-m-d') ?? now()->toDateString(),
            ]);

            Finance::create([
                'user_id'        => $admin->id,
                'event_id'       => $event->id,
                'type'           => 'expense',
                'funding_source' => null,
                'amount'         => fake()->randomFloat(2, 100_000, 2_000_000),
                'description'    => "Pengeluaran operasional {$event->name}",
                'date'           => $event->start_date?->copy()->addDays(1)?->format('Y-m-d') ?? now()->toDateString(),
            ]);
        });

        // 7. 10 Meeting
        $meetings = Meeting::factory()->count(10)->create();

        // 8. Attendance setiap Meeting untuk semua member
        $statuses = ['present', 'permit', 'sick', 'absent'];

        $meetings->each(function (Meeting $meeting) use ($allMembers, $statuses) {
            $allMembers->each(function (User $member) use ($meeting, $statuses) {
                MeetingAttendance::create([
                    'meeting_id' => $meeting->id,
                    'user_id'    => $member->id,
                    'status'     => fake()->randomElement($statuses),
                    'proof_url'  => fake()->optional(0.3)->url(),
                ]);
            });
        });

        // 9. 15 Document
        Document::factory()->count(15)->create([
            'created_by' => $admin->id,
            'event_id'   => fn () => $events->random()->id,
        ]);

        // 10. 3 Warning
        $warnedUsers = $allMembers->random(3);
        $warnedUsers->each(function (User $user) use ($admin) {
            Warning::create([
                'user_id'  => $user->id,
                'admin_id' => $admin->id,
                'reason'   => fake()->randomElement([
                    'Tidak hadir 3 kali berturut-turut tanpa keterangan.',
                    'Melanggar tata tertib organisasi.',
                    'Tidak menyelesaikan tugas yang diberikan.',
                    'Terlambat mengumpulkan laporan kegiatan.',
                ]),
                'date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            ]);
        });
    }
}
