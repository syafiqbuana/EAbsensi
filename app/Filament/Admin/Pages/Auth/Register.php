<?php

namespace App\Filament\Admin\Pages\Auth;

use App\Actions\RegisterTpq;
use App\Models\TpqProfile;
use App\Models\TpqRegistration;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{

    public function mount(): void
{
    // Jika sudah login, redirect sesuai kondisi
    if (Filament::auth()->check()) {
        $this->redirect(route('filament.admin.auth.login', [
            'tpq_slug' => request()->route('tpq_slug') ?? 'universal'
        ]), navigate: false);
        return;
    }

    $this->form->fill(); // ← INI yang bikin form muncul
}

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Wizard::make([
                    Step::make('Kredensial')
                        ->description('Data akun login Anda')
                        ->icon('heroicon-o-key')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Panggilan')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique(table: User::class, column: 'email', ignoreRecord: false)
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            $user = User::where('email', $value)->first();
                                            if ($user) {
                                                $hasActive = TpqRegistration::where('applicant_id', $user->id)
                                                    ->whereIn('status', ['pending', 'approved'])
                                                    ->exists();
                                                if ($hasActive) {
                                                    $fail('Email sudah terdaftar dan memiliki antrian/akses aktif.');
                                                }
                                            }
                                        };
                                    },
                                ]),

                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->required()
                                ->rule(Password::defaults()),

                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->password()
                                ->required()
                                ->same('password'),
                        ]),

                    Step::make('Profil Diri')
                        ->description('Informasi data diri Anda')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextInput::make('full_name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('phone_number')
                                ->label('Nomor HP')
                                ->tel()
                                ->numeric()
                                ->maxLength(20),

                            Textarea::make('address')
                                ->label('Alamat')
                                ->rows(3)
                                ->maxLength(500),

                            FileUpload::make('photo_path')
                                ->label('Foto Profil (Opsional)')
                                ->image()
                                ->disk('public')
                                ->directory('user-photos')
                                ->maxSize(2048)
                                ->nullable(),
                        ]),

                    Step::make('Data TPQ')
                        ->description('Informasi TPQ yang akan didaftarkan')
                        ->icon('heroicon-o-building-library')
                        ->schema([
                            TextInput::make('tpq_name')
                                ->label('Nama TPQ')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('tpq_registration_number')
                                ->label('Nomor Induk TPQ (NITQ)')
                                ->required()
                                ->string()
                                ->maxLength(50)
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, Closure $fail) {
                                            $exists = TpqProfile::where('registration_number', $value)->exists()
                                                || TpqRegistration::where('tpq_registration_number', $value)
                                                    ->whereIn('status', ['pending', 'approved'])
                                                    ->exists();
                                            if ($exists) {
                                                $fail('Nomor Induk TPQ ini sudah terdaftar di sistem.');
                                            }
                                        };
                                    },
                                ]),

                            Textarea::make('tpq_address')
                                ->label('Alamat TPQ')
                                ->required()
                                ->rows(3),

                            TextInput::make('tpq_contact_number')
                                ->label('Nomor Kontak TPQ')
                                ->tel()
                                ->numeric()
                                ->required()
                                ->maxLength(20),

                            Textarea::make('notes')
                                ->label('Catatan (Opsional)')
                                ->rows(3)
                                ->nullable(),
                        ]),
                ])
->submitAction(new HtmlString(Blade::render( // ← Ganti ini
                '<x-filament::button type="submit" size="lg" class="w-full">Daftar Sekarang</x-filament::button>'
            ))),

            ]);
    }

    public function getFormActions(): array
    {
        return [];
    }

    public function register(): ?\Filament\Auth\Http\Responses\Contracts\RegistrationResponse
    {
        $data = $this->form->getState();

        RegisterTpq::execute($data);

        $this->redirect(route('registration.pending'));

        return null;
    }

    public function loginAction(): Action
    {
        $tpqSlug = request()->route('tpq_slug') ?? 'universal';

        return Action::make('login')
            ->link()
            ->label(__('filament-panels::auth/pages/register.actions.login.label'))
            ->url(route(
                'filament.admin.auth.login',
                ['tpq_slug' => $tpqSlug],
                false
            ));
    }
}