<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Classes;
use App\Models\Schedules;
use App\Models\Student;
use App\Models\User;
use App\Support\CurrentTpq;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $studentQuery = CurrentTpq::where(Student::query());
        $classQuery = CurrentTpq::where(Classes::query());
        $scheduleQuery = CurrentTpq::where(Schedules::query());
        $userQuery = User::whereHas('profile', fn($query) => $query->where('tpq_profile_id', CurrentTpq::id()));

        return [
            Stat::make("Jumlah Murid", $studentQuery->count())
                ->description("Total murid terdaftar")
                ->chart($this->getChartData($studentQuery))
                ->color('success'),

            Stat::make("Jumlah Kelas", $classQuery->count())
                ->description("Total kelas aktif")
                ->chart($this->getChartData($classQuery))
                ->color('info'),

            Stat::make("Jumlah Jadwal", $scheduleQuery->count())
                ->description("Total jadwal aktif")
                ->chart($this->getChartData($scheduleQuery))
                ->color('warning'),

            Stat::make("Jumlah Pengguna", $userQuery->count())
                ->description("Total pengguna sistem")
                ->chart($this->getChartData($userQuery))
                ->color('primary'), 
        ];
    }

    /**
     * Mengambil data pertumbuhan 30 hari terakhir untuk Chart
     */
    private function getChartData(Builder $query): array
    {
        $q = clone $query;
        $table = $q->getModel()->getTable();

        $trend = $q->where("$table.created_at", '>=', now()->subDays(30))
            ->selectRaw("DATE($table.created_at) as date, COUNT(*) as aggregate")
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $chart = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chart[] = $trend[$date] ?? 0;
        }

        return $chart;
    }
}