<?php

namespace Vvb13a\LaravelModelChecker\Filament\Tables\Filters;

use Filament\Tables\Filters\SelectFilter;

class CheckNameFilter extends SelectFilter
{
    public static function make(?string $name = 'check_name'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options($this->getCheckNamesArr());
    }

    protected function getCheckNamesArr(): array
    {
        return collect(config('model-checker.checks'))->mapWithKeys(function ($value, $key) {
            $basename = class_basename($value);
            return [$basename => $basename];
        })->toArray();
    }
}


