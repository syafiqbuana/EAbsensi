<?php

namespace App\Actions;

use App\Models\TpqRegistration;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterTpq
{
    public static function execute(array $data): TpqRegistration
    {
        return DB::transaction(function () use ($data) {
            // 1. Upsert user (email bisa sudah ada jika sebelumnya rejected)
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make($data['password']),
                    'is_active' => true,
                ]
            );

            // 2. Upsert user profile
            $user->profile()->updateOrCreate([], [
                'full_name'    => $data['full_name'],
                'tpq_profile_id' => null,
                'phone_number' => $data['phone_number'] ?? null,
                'address'      => $data['address'] ?? null,
                'photo_path'   => $data['photo_path'] ?? null,
            ]);

            // 3. Buat registration record (data TPQ disimpan sementara sebelum approved)
            $registration = TpqRegistration::create([
                'applicant_id'            => $user->id,
                'status'                  => TpqRegistration::STATUS_PENDING,
                    'registration_number'     => TpqRegistration::generateRegistrationNumber($data['tpq_name']),
                'tpq_name'                => $data['tpq_name'],
                'tpq_registration_number' => $data['tpq_registration_number'],
                'tpq_address'             => $data['tpq_address'],
                'tpq_contact_number'      => $data['tpq_contact_number'],
                'notes'                   => $data['notes'] ?? null,
            ]);

            // 4. Login the user
            Auth::login($user);

            return $registration;
        });
    }
}
