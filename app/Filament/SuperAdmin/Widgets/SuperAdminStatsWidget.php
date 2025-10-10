<?php

namespace App\Filament\SuperAdmin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Models\Payment;

class SuperAdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Écoles totales', School::count())
                ->description('Nombre total d\'écoles inscrites')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
            
            Stat::make('Écoles actives', School::where('is_active', true)->count())
                ->description('Écoles actuellement actives')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
            
            Stat::make('Élèves totaux', Student::count())
                ->description('Nombre total d\'élèves')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            Stat::make('Professeurs', User::where('global_role', 'teacher')->count())
                ->description('Nombre total de professeurs')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning'),
            
            Stat::make('Revenus du mois', 'N/A')
                ->description('Revenus générés ce mois')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            
            Stat::make('Nouvelles inscriptions', School::whereMonth('created_at', now()->month)->count())
                ->description('Écoles inscrites ce mois')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('primary'),
        ];
    }
}
