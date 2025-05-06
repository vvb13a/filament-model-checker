<?php

namespace Vvb13a\FilamentModelChecker\Filament\Resources;

use Filament\Resources\Resource;
use Vvb13a\FilamentModelChecker\Filament\Resources\FindingResource\Pages\ListFindings;
use Vvb13a\LaravelModelChecker\Models\Finding;

class FindingResource extends Resource
{
    protected static ?string $model = Finding::class;
    protected static ?string $navigationLabel = 'Findings';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getPages(): array
    {
        return [
            'index' => ListFindings::route('/'),
        ];
    }
}
