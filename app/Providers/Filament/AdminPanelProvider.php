<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color; // Import Warna
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            
            // --- 1. GANTI NAMA & BRANDING (HILANGKAN LARAVEL) ---
            ->brandName('Staycation Manager') // Ganti tulisan "Laravel" jadi ini
            ->brandLogoHeight('3rem')
            
            // --- 2. TEMA MEWAH (QUIET LUXURY) ---
            // Kita ganti warna Amber (Kuning) bawaan menjadi Zinc (Hitam/Abu Mahal)
            ->colors([
                'primary' => Color::Zinc, 
                'gray' => Color::Slate,
            ])
            // Ganti Font biar tidak kaku
            ->font('Poppins')
            
            // --- 3. UI TWEAKS ---
            ->sidebarCollapsibleOnDesktop() // Menu samping bisa dilipat
            ->maxContentWidth('full') // Layar penuh, lebih luas
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            
            // --- 4. BERSIH-BERSIH WIDGET BAWAAN (Tetap kita matikan) ---
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}