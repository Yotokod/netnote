<?php

return [
    App\Providers\AppServiceProvider::class,
    Spatie\Multitenancy\MultitenancyServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\SchoolPanelProvider::class,
    App\Providers\Filament\SuperAdminPanelProvider::class,
    App\Providers\Filament\TeacherPanelProvider::class,
];
