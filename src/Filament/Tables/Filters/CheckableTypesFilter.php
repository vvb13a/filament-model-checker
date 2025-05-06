<?php

namespace Vvb13a\FilamentModelChecker\Filament\Tables\Filters;

use Filament\Facades\Filament;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Relations\Relation;

class CheckableTypesFilter extends SelectFilter
{
    public static function make(?string $name = 'checkable_type'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options($this->getCheckableTypesArr());
    }

    protected function getCheckableTypesArr(): array
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('filament-model-checker')) {
            $plugin = $currentPanel->getPlugin('filament-model-checker');
        }

        $checkableTypes = $plugin?->getCheckableTypes() ?? [];

        if (empty($checkableTypes)) {
            return [];
        }

        return collect($checkableTypes)->mapWithKeys(function ($value, $key) {
            return [Relation::getMorphAlias($value) => class_basename($value)];
        })->toArray();
    }
}