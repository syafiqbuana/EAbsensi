<?php

namespace App\Livewire;

use App\Models\TpqRegistration;
use App\Support\CurrentTpq;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class RegistrationPending extends Component
{
    public string $status = 'pending';
    public ?string $rejectedReason = null;

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('filament.admin.auth.login', ['tpq_slug' => 'universal']));
            return;
        }

        $this->checkStatus();
    }

    #[On('check-status')]
    public function checkStatus(): void
    {
        $registration = Auth::user()
            ?->tpqRegistrations()
            ->latest()
            ->first();

        $this->status         = $registration?->status ?? 'pending';
        $this->rejectedReason = $registration?->rejected_reason;

        if ($this->status === TpqRegistration::STATUS_APPROVED) {
            $tpqProfile = $registration->tpqProfile;

            if ($tpqProfile) {
                CurrentTpq::setFromProfile($tpqProfile);
                $this->redirect(url("/{$tpqProfile->slug}/admin"), navigate: false);
            }
        }
    }

    public function render(): View
    {
        return view('livewire.registration-pending') ->layout('components.layouts.guest');
    }
}
