<?php

namespace Vvb13a\FilamentModelChecker\Filament\Resources;

use Filament\Resources\Resource;
use Vvb13a\FilamentModelChecker\Filament\Resources\SummaryResource\Pages\ListSummaries;
use Vvb13a\LaravelModelChecker\Models\Summary;

class SummaryResource extends Resource
{
    protected static ?string $model = Summary::class;
    protected static ?string $navigationLabel = 'Summaries';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getPages(): array
    {
        return [
            'index' => ListSummaries::route('/'),
        ];
    }
}
