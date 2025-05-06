<?php

namespace Vvb13a\FilamentModelChecker\Filament\Actions;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\IconPosition;
use Vvb13a\LaravelModelChecker\Jobs\RunBulkModelChecksJob;
use Vvb13a\LaravelResponseChecker\Enums\FindingLevel;

class LaunchBulkModelChecksAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Launch Checks');
        $this->icon('heroicon-o-rocket-launch');
        $this->color('info');
        $this->requiresConfirmation();
        $this->iconPosition(IconPosition::After);
        $this->form([
            Select::make('checkable_type')
                ->required()
                ->options($this->getDispatchModelArr()),
            Select::make('level')
                ->nullable()
                ->options(FindingLevel::class),
        ]);
        $this->action(function ($data) {
            RunBulkModelChecksJob::dispatch($data['checkable_type'], level: $data['level'] ?? null);
        });
    }

    public static function make(?string $name = 'launch_checks'): static
    {
        return parent::make($name);
    }

    protected function getDispatchModelArr(): array
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
            return [$value => class_basename($value)];
        })->toArray();
    }
}
