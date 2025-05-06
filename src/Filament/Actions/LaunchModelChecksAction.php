<?php

namespace Vvb13a\LaravelModelChecker\Filament\Actions;

use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;

class LaunchModelChecksAction extends Action
{
    public static function make(?string $name = 'launch_check'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Launch Check');
        $this->icon('heroicon-o-rocket-launch');
        $this->color('info');
        $this->requiresConfirmation();
        $this->iconPosition(IconPosition::After);
        $this->action(function ($record): void {
            $record->queueChecks();
        });
    }
}




