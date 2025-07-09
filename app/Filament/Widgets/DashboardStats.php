<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ActivitySummary;
use App\Models\PlannedProducts;
use App\Models\PlannedPopulation;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Actividades', ActivitySummary::count())
                ->icon('heroicon-o-list-bullet')
                ->color('primary'),
            Stat::make('Total de Beneficiarios', ActivitySummary::sum('beneficiaries_count'))
                ->icon('heroicon-o-user-group')
                ->color('success'),
            Stat::make('Total de Productos Planificados', PlannedProducts::count())
                ->icon('heroicon-o-cube')
                ->color('info'),
            Stat::make('Total de Población Planificada', PlannedPopulation::count())
                ->icon('heroicon-o-users')
                ->color('warning'),
        ];
    }
}
