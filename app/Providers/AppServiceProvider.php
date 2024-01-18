<?php

namespace App\Providers;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Section::configureUsing(function (Section $section) {
            $section->columns(2)->compact();
        });
        RichEditor::configureUsing(function (RichEditor $richEditor) {
            $richEditor->columnSpanFull();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
