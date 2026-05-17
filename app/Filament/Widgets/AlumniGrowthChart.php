<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AlumniGrowthChart extends ChartWidget
{
    protected ?string $heading = 'Alumni Registrations — Last 6 Months';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data   = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month    = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $data[]   = User::where('role', 'alumni')
                            ->whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month)
                            ->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'New Alumni',
                    'data'            => $data,
                    'borderColor'     => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}