<?php

namespace Vvb13a\LaravelModelChecker\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;
use Vvb13a\LaravelResponseChecker\Enums\FindingLevel;

class FindingLevelColumn extends TextColumn
{
    public static function make(string $name = 'level'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->color(function ($record): string {
            return match ($this->getFindingLevel($record)->value) {
                'info' => 'info',
                'warning' => 'warning',
                'error' => 'danger',
                'success' => 'success',
                default => 'info',
            };
        });
        $this->formatStateUsing(function ($record): string {
            return str($this->getFindingLevel($record)->value)->headline();
        });
        $this->sortable();
        $this->badge();
    }

    protected function getFindingLevel($record): FindingLevel
    {
        return $record->{$this->getName()};
    }
}