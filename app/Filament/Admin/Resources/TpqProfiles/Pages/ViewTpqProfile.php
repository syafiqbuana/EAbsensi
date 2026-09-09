<?php

namespace App\Filament\Admin\Resources\TpqProfiles\Pages;

use App\Filament\Admin\Resources\TpqProfiles\Schemas\TpqProfileForm;
use App\Filament\Admin\Resources\TpqProfiles\TpqProfileResource;
use App\Models\TpqProfile;
use App\Models\User;
use App\Support\CurrentTpq;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ViewTpqProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TpqProfileResource::class;

    protected string $view = 'filament.admin.resources.tpq-profiles.pages.view-tpq-profile';

    public ?array $data = [];

    public array $tpqHeadData = [];

    public bool $isEditing = false;

    public function getTitle(): string|Htmlable
    {
        return $this->isEditing
            ? 'Edit Profil TPQ'
            : 'Lihat Profil TPQ';
    }

    public function getBreadcrumbs(): array
    {
        $slug = CurrentTpq::slug();

        return [
            $this->getResource()::getUrl(
                'index',
                ['tpq_slug' => $slug]
            ) => 'Profil TPQ',

            '' => $this->isEditing
                ? 'Edit'
                : 'Lihat',
        ];
    }
    public function form(Schema $form): Schema
    {
        return TpqProfileForm::configure(
            $form,
            $this->isEditing
        )
            ->statePath('data');
    }

    public function mount(): void
    {
        $tpqProfile = $this->getTpqProfile();

        if (!$tpqProfile) {
            abort(404, 'Profil TPQ tidak ditemukan.');
        }
        $this->getTpqHead($tpqProfile);
        $this->fillForm($tpqProfile);
    }

    public function toggleEdit(): void
    {
        $tpqProfile = $this->getTpqProfile();

        Gate::authorize('update', $tpqProfile);

        $this->isEditing = !$this->isEditing;

        if ($tpqProfile) {
            $this->getTpqHead($tpqProfile);
            $this->fillForm($tpqProfile);
        }
    }

    protected function getTpqProfile(): ?TpqProfile
    {
        $id = CurrentTpq::id();

        if (!$id) {
            return null;
        }
        return TpqProfile::find($id);
    }

    protected function getTpqHead(TpqProfile $tpqProfile): void
    {
        setPermissionsTeamId($tpqProfile->id);
        $tpqHead = User::query()
            ->headTpq($tpqProfile->id)
            ->with('profile')
            ->first();

        if (!$tpqHead) {
            $this->tpqHeadData = [];

            return;
        }

        $profile = $tpqHead->profile;

        $this->tpqHeadData = [
            'full_name' => $profile?->full_name
                ?? $tpqHead->name,

            'email' => $tpqHead->email,

            'phone_number' => $profile?->phone_number
                ?? '-',

            'address' => $profile?->address
                ?? '-',
            'photo_path' => $profile?->photo_path,
        ];
    }

    protected function fillForm(TpqProfile $tpqProfile): void
    {
        $this->form->fill([
            ...$tpqProfile->toArray(),
            'tpq_head_name' => $this->tpqHeadData['full_name']
                ?? null,

            'tpq_head_email' => $this->tpqHeadData['email']
                ?? null,

            'tpq_head_phone_number' => $this->tpqHeadData['phone_number']
                ?? null,

            'tpq_head_address' => $this->tpqHeadData['address']
                ?? null,

            'tpq_head_photo_path' => $this->tpqHeadData['photo_path']
                ?? null,
        ]);
    }

    public function save(): void
    {
        $tpqProfile = $this->getTpqProfile();

        if (!$tpqProfile) {
            abort(404, 'Profil TPQ tidak ditemukan.');
        }

        Gate::authorize('update', $tpqProfile);

        $state = $this->form->getState();

        // Only update fields belonging to tpq_profiles table.
        $tpqProfile->update(Arr::only($state, [
            'name',
            'registration_number',
            'contact_number',
            'address',
            'logo_path',
        ]));

        $tpqHead = User::query()
            ->headTpq($tpqProfile->id)
            ->with('profile')
            ->first();

        if ($tpqHead) {

            $tpqHead->update([
                'email' => $state['tpq_head_email'] ?? $tpqHead->email,
            ]);
            $tpqHead->profile()->update([
                'full_name' => $state['tpq_head_name'] ?? $tpqHead->profile?->full_name,
                'phone_number' => $state['tpq_head_phone_number'] ?? $tpqHead->profile?->phone_number,
                'address' => $state['tpq_head_address'] ?? $tpqHead->profile?->address,
                'photo_path' => $state['tpq_head_photo_path'] ?? $tpqHead->profile?->photo_path,
            ]);
        }

        $tpqProfile->refresh();
        $this->getTpqHead($tpqProfile);
        $this->fillForm($tpqProfile);
        $this->isEditing = false;

        Notification::make()
            ->title('Berhasil')
            ->body('Profil TPQ beserta data Kepala TPQ berhasil diperbarui.')
            ->success()
            ->send();
        $this->redirect(request()->header('Referer'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editData')
                ->label(
                    fn() => $this->isEditing
                    ? 'Batal Edit'
                    : 'Edit Data'
                )
                ->icon(
                    fn() => $this->isEditing
                    ? 'heroicon-o-x-mark'
                    : 'heroicon-o-pencil-square'
                )
                ->color(
                    fn() => $this->isEditing
                    ? 'gray'
                    : 'success'
                )
                ->action('toggleEdit')
                ->visible(
                    fn() => Gate::allows('update', $this->getTpqProfile())
                ),

            Action::make('saveData')
                ->label('Simpan')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save')
                ->visible(
                    fn() =>
                    $this->isEditing
                    && Gate::allows('update', $this->getTpqProfile())
                ),

        ];
    }
}