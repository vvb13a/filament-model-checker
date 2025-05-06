<?php

namespace Vvb13a\FilamentModelChecker\Filament\Infolists;

use Closure;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Contracts\Support\Htmlable;

class FindingDetailsSection extends Section
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->columns(2);
        $this->schema([
            TextEntry::make('url')
                ->label('URL')
                ->columnSpanFull(),
            TextEntry::make('check_name')
                ->label('Check Name'),
            TextEntry::make('level')
                ->color(function ($record): string {
                    return match ($record->level->value) {
                        'info' => 'info',
                        'warning' => 'warning',
                        'error' => 'danger',
                        'success' => 'success',
                        default => 'info',
                    };
                })
                ->formatStateUsing(function ($record): string {
                    return str($record->level->value)->headline();
                })
                ->label('Level')
                ->badge(),
            TextEntry::make('message')
                ->label('Message')
                ->columnSpanFull(),
            TextEntry::make('created_at')
                ->label('Detected At')
                ->dateTime(),
            ViewEntry::make('details')
                ->label('Detailed Information')
                ->view('filament-model-checker::infolists.entries.check-details')
                ->columnSpanFull(),
            ViewEntry::make('configuration')
                ->label('Check Configuration')
                ->view('filament-model-checker::infolists.entries.check-configuration')
                ->columnSpanFull(),
        ]);
    }

    public static function make(Htmlable|array|Closure|string|null $heading = 'Finding Details'): static
    {
        return parent::make($heading);
    }
}