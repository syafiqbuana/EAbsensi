<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Classes;
use App\Models\Schedules;
use App\Models\Student;
use App\Models\TpqProfile;
use App\Models\User;
use App\Models\UserProfile;
use App\Support\CurrentTpq;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class TpqDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Create / Retrieve TPQ Demo Profile
        $tpqProfile = TpqProfile::firstOrCreate(
            ['slug' => 'TPQ-DEMO'],
            [
                'name' => 'TPQ Demo',
                'registration_number' => 'DEMO123456',
                'address' => 'Jl. Pendidikan No. 10, Jakarta',
                'contact_number' => '081299990000',
                'status' => TpqProfile::STATUS_ACTIVE,
            ]
        );

        // Set current TPQ context in session and Spatie team ID
        CurrentTpq::setFromProfile($tpqProfile);
        app(PermissionRegistrar::class)->setPermissionsTeamId($tpqProfile->id);

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'head_tpq', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);

        $defaultPassword = Hash::make('password');

        // 2. Create 1 Head of TPQ
        $headUser = User::firstOrCreate(
            ['email' => 'head.demo@tpq.com'],
            [
                'name' => 'Bapak Kepala TPQ Demo',
                'password' => $defaultPassword,
                'phone_number' => '081211112222',
                'is_active' => true,
                'address' => 'Jl. Utama No. 1, Jakarta',
            ]
        );
        UserProfile::updateOrCreate(
            ['user_id' => $headUser->id],
            [
                'tpq_profile_id' => $tpqProfile->id,
                'full_name' => 'Bapak Kepala TPQ, S.Pd.I',
                'phone_number' => '081211112222',
                'address' => 'Jl. Utama No. 1, Jakarta',
            ]
        );
        $headUser->assignRole('head_tpq');

        // 3. Create 5 Teachers
        $teachers = [];
        for ($i = 1; $i <= 5; $i++) {
            $teacherName = "Guru Demo " . $i;
            $teacher = User::firstOrCreate(
                ['email' => "teacher{$i}.demo@tpq.com"],
                [
                    'name' => $teacherName,
                    'password' => $defaultPassword,
                    'phone_number' => "08123333440{$i}",
                    'is_active' => true,
                    'address' => "Jl. Guru No. {$i}, Jakarta",
                ]
            );
            UserProfile::updateOrCreate(
                ['user_id' => $teacher->id],
                [
                    'tpq_profile_id' => $tpqProfile->id,
                    'full_name' => "{$teacherName}, S.Pd.",
                    'phone_number' => "08123333440{$i}",
                    'address' => "Jl. Guru No. {$i}, Jakarta",
                ]
            );
            $teacher->assignRole('teacher');
            $teachers[] = $teacher;
        }

        // 4. Create 94 Parent Users
        $parents = [];
        for ($i = 1; $i <= 94; $i++) {
            $parentName = $faker->name();
            $parent = User::firstOrCreate(
                ['email' => "parent{$i}.demo@tpq.com"],
                [
                    'name' => $parentName,
                    'password' => $defaultPassword,
                    'phone_number' => $faker->phoneNumber(),
                    'is_active' => true,
                    'address' => $faker->address(),
                ]
            );
            UserProfile::updateOrCreate(
                ['user_id' => $parent->id],
                [
                    'tpq_profile_id' => $tpqProfile->id,
                    'full_name' => $parentName,
                    'phone_number' => $parent->phone_number,
                    'address' => $parent->address,
                ]
            );
            $parent->assignRole('parent');
            $parents[] = $parent;
        }

        // 5. Generate 5 Classes
        $classData = [
            'Kelas A (Iqro 1-2)',
            'Kelas B (Iqro 3-4)',
            'Kelas C (Iqro 5-6)',
            'Kelas D (Al-Qur\'an Dasar)',
            'Kelas E (Al-Qur\'an Lanjutan)',
        ];
        $classes = [];
        foreach ($classData as $idx => $name) {
            $classes[] = Classes::create([
                'tpq_profile_id' => $tpqProfile->id,
                'name' => $name,
                'order' => $idx + 1,
            ]);
        }

        // 6. Generate 5 Non-Overlapping Schedules
        $scheduleDefinitions = [
            [
                'name' => 'Senin-Rabu Siang',
                'day' => ['monday', 'tuesday', 'wednesday'],
                'time_open' => '13:30:00',
                'time_close' => '14:45:00',
            ],
            [
                'name' => 'Senin-Rabu Sore',
                'day' => ['monday', 'tuesday', 'wednesday'],
                'time_open' => '15:15:00',
                'time_close' => '16:30:00',
            ],
            [
                'name' => 'Kamis-Sabtu Siang',
                'day' => ['thursday', 'friday', 'saturday'],
                'time_open' => '13:30:00',
                'time_close' => '14:45:00',
            ],
            [
                'name' => 'Kamis-Sabtu Sore',
                'day' => ['thursday', 'friday', 'saturday'],
                'time_open' => '15:15:00',
                'time_close' => '16:30:00',
            ],
            [
                'name' => 'Pagi Akhir Pekan',
                'day' => ['sunday'],
                'time_open' => '08:00:00',
                'time_close' => '09:30:00',
            ],
        ];

        $schedules = [];
        foreach ($scheduleDefinitions as $idx => $sDef) {
            $schedule = Schedules::create([
                'tpq_profile_id' => $tpqProfile->id,
                'name' => $sDef['name'],
                'day' => $sDef['day'],
                'time_open' => $sDef['time_open'],
                'time_close' => $sDef['time_close'],
            ]);
            $schedules[] = $schedule;

            // Link each class to a schedule
            if (isset($classes[$idx])) {
                $classes[$idx]->schedules()->attach($schedule->id);
            }
        }

        // 7. Generate Children (2 to 3 per parent) & assign to random classes
        $allStudents = [];
        foreach ($parents as $parent) {
            $numChildren = rand(2, 3);
            for ($c = 1; $c <= $numChildren; $c++) {
                $gender = $faker->randomElement(['male', 'female']);
                $age = rand(3, 10);
                $birthDate = Carbon::now()
                    ->subYears($age)
                    ->subDays(rand(0, 364))
                    ->toDateString();

                $randomClass = $classes[array_rand($classes)];

                $student = Student::create([
                    'tpq_profile_id' => $tpqProfile->id,
                    'name' => $gender === 'male' ? $faker->firstNameMale() . ' ' . $faker->lastName() : $faker->firstNameFemale() . ' ' . $faker->lastName(),
                    'birth_date' => $birthDate,
                    'birth_place' => $faker->city(),
                    'class_id' => $randomClass->id,
                    'gender' => $gender,
                    'status' => Student::STATUS_ACTIVE,
                ]);

                // Attach to parent
                $student->users()->attach($parent->id);

                $allStudents[] = $student;
            }
        }

        // 8. Generate Attendance for past 2 weeks (14 days)
        $today = Carbon::today();
        $startDate = $today->copy()->subDays(13);

        $attendanceBatch = [];
        foreach ($allStudents as $student) {
            $studentClass = $student->classes;
            if (!$studentClass) {
                continue;
            }

            // Get schedules associated with student's class
            $classSchedules = $studentClass->schedules;
            if ($classSchedules->isEmpty()) {
                continue;
            }

            for ($date = $startDate->copy(); $date->lte($today); $date->addDay()) {
                $dayName = strtolower($date->format('l'));

                foreach ($classSchedules as $sch) {
                    $schDays = is_array($sch->day) ? $sch->day : explode(',', $sch->day);
                    $schDays = array_map('trim', $schDays);

                    if (in_array($dayName, $schDays)) {
                        // Determine random attendance status
                        $rand = rand(1, 100);
                        if ($rand <= 75) {
                            $status = Attendance::STATUS_PRESENT;
                        } elseif ($rand <= 85) {
                            $status = Attendance::STATUS_SICK;
                        } elseif ($rand <= 95) {
                            $status = Attendance::STATUS_PERMISSION;
                        } else {
                            $status = Attendance::STATUS_ABSENT;
                        }

                        $timeIn = null;
                        if ($status === Attendance::STATUS_PRESENT) {
                            $timeOpen = Carbon::parse($date->toDateString() . ' ' . $sch->time_open);
                            // Vary time_in between -10 minutes early to +15 minutes late
                            $offsetMinutes = rand(-10, 15);
                            $timeIn = $timeOpen->copy()->addMinutes($offsetMinutes)->format('H:i:s');
                        }

                        $attendanceBatch[] = [
                            'tpq_profile_id' => $tpqProfile->id,
                            'student_id' => $student->id,
                            'schedule_id' => $sch->id,
                            'date' => $date->toDateString(),
                            'time_in' => $timeIn,
                            'status' => $status,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        // Chunk insert attendance for performance
        foreach (array_chunk($attendanceBatch, 500) as $chunk) {
            Attendance::insert($chunk);
        }
    }
}
