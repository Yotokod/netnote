<?php

namespace App\Filament\SuperAdmin\Pages;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;

class SuperAdminDashboard extends Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Tableau de bord';
    protected static string $routePath = '/';
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\SuperAdmin\Widgets\SuperAdminStatsWidget::class,
            AccountWidget::class,
        ];
    }
}
