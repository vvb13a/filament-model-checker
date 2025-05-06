<?php

namespace Vvb13a\FilamentModelChecker\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    public static function getEloquentQuery(): Builder
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('filament-model-checker')) {
            $plugin = $currentPanel->getPlugin('filament-model-checker');
        }

        $checkableTypes = $plugin?->getCheckableTypes() ?? [];

        if (empty($checkableTypes)) {
            return parent::getEloquentQuery();
        }

        $aliases = collect($checkableTypes)->map(function ($value) {
            return Relation::getMorphAlias($value);
        })->toArray();

        return parent::getEloquentQuery()->whereIn('checkable_type', $aliases);
    }
}
