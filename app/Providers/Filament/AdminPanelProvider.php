<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Auth\Login;
use App\Filament\Admin\Pages\Auth\Register;
use App\Http\Middleware\ResolveTpqTenant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use App\Http\Middleware\AuthenticateFilamentAdmin;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('{tpq_slug}/admin')    // parameter slug dinamis
            ->brandName(
                fn() =>
                request()->route('tpq_slug')
                ? (\App\Support\CurrentTpq::get()?->name ?? config('app.name'))
                : config('app.name')
            )
            ->spa()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->registration(Register::class)
            ->colors([
                'primary' => Color::Indigo,
            ])
            //set navigation icon

            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Kehadiran')
                    ->icon('heroicon-o-check-badge')
                ,
                NavigationGroup::make()
                    ->label('Master Data')->icon('heroicon-o-circle-stack')
                ,
                NavigationGroup::make()
                    ->label('Administrasi')->icon('heroicon-o-document-text')
                ,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                ResolveTpqTenant::class,   // resolve tenant dari slug
            ])
            ->authMiddleware([
                AuthenticateFilamentAdmin::class,
            ]);
    }
}
