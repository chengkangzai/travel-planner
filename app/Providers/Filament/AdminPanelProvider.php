<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditTeamProfile;
use App\Filament\Pages\RegisterTeam;
use App\Filament\Widgets\CalendarWidget;
use App\Models\Team;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->tenant(Team::class)
            ->tenantRegistration(RegisterTeam::class)
            ->tenantProfile(EditTeamProfile::class)
            ->default()
            ->id('admin')
            ->profile()
            ->passwordReset()
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugins([
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
                    ->config([
                        'headerToolbar' => [
                            'left' => 'prev,next today',
                            'center' => 'title',
                            'right' => 'timeGridWeek,dayGridDay',
                        ],
                        'initialView' => 'timeGridWeek',
                        'nowIndicator' => true,
                        'allDaySlot' => false,
                    ])
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->maxContentWidth(MaxWidth::ScreenExtraLarge)
            ->widgets([
                CalendarWidget::class,
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
            ])
            ->spa()
            ->sidebarFullyCollapsibleOnDesktop()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
