<?php

namespace Vvb13a\LaravelModelChecker\Filament\Tables\Filters;

use Filament\Tables\Filters\SelectFilter;
use Vvb13a\LaravelResponseChecker\Enums\FindingLevel;

class FindingLevelFilter extends SelectFilter
{
    public static function make(?string $name = 'level'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->options(FindingLevel::class);
    }
}


