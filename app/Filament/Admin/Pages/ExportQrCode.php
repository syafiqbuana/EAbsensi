<?php

namespace App\Filament\Admin\Pages;

use App\Jobs\GenerateQrPdfJob;
use App\Models\Classes;
use App\Models\Student;
use App\Support\CurrentTpq;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ExportQrCode extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.admin.pages.export-qr-code';
    protected static ?string $slug = 'ekspor-kode-qr';
    protected static ?string $navigationLabel = 'Ekspor Kode QR';
    protected static ?string $title = 'Ekspor Kode QR';
    protected static string | BackedEnum | null $navigationIcon = Heroicon::Printer;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'export_type' => 'all',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Filter Ekspor Kode QR')
                    ->description('Pilih lingkup data kelas atau siswa yang akan dicetak kode QR PDF-nya.')
                    ->schema([
                        Select::make('export_type')
                            ->label('Lingkup Data')
                            ->options([
                                'all' => 'Seluruh Kelas / Seluruh Santri',
                                'classes' => 'Beberapa Kelas Tertentu',
                                'student' => '1 Siswa Saja',
                            ])
                            ->default('all')
                            ->native(false)
                            ->live()
                            ->required(),

                        Select::make('class_ids')
                            ->label('Pilih Kelas')
                            ->multiple()
                            ->options(fn () => Classes::query()->byTpqProfile(CurrentTpq::id())->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->required(fn ($get) => $get('export_type') === 'classes')
                            ->visible(fn ($get) => $get('export_type') === 'classes'),

                        Select::make('student_id')
                            ->label('Pilih Siswa')
                            ->options(fn () => Student::query()->where('tpq_profile_id', CurrentTpq::id())->active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(fn ($get) => $get('export_type') === 'student')
                            ->visible(fn ($get) => $get('export_type') === 'student'),
                    ]),
            ])
            ->statePath('data');
    }

    public function export(): void
    {
        $data = $this->form->getState();

        $tpqId = CurrentTpq::id();
        if (!$tpqId) {
            Notification::make()
                ->title('Gagal')
                ->body('Profil TPQ aktif tidak ditemukan.')
                ->danger()
                ->send();

            return;
        }

        $exportType = $data['export_type'] ?? 'all';
        $classIds = $data['class_ids'] ?? [];
        $studentId = $data['student_id'] ?? null;

        GenerateQrPdfJob::dispatch(
            auth()->user(),
            $tpqId,
            $exportType,
            is_array($classIds) ? $classIds : [],
            $studentId ? (int) $studentId : null
        );

        Notification::make()
            ->title('Proses Ekspor Dimulai')
            ->body('Proses ekspor PDF telah dimasukkan ke dalam antrean (queue). Anda akan menerima notifikasi jika PDF sudah selesai diproses.')
            ->success()
            ->send();
    }
}
