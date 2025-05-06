<?php

namespace Vvb13a\FilamentModelChecker;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Vvb13a\FilamentModelChecker\Filament\Resources\FindingResource;
use Vvb13a\FilamentModelChecker\Filament\Resources\SummaryResource;

class FilamentModelCheckerPlugin implements Plugin
{
    protected string $summaryResource = SummaryResource::class;
    protected string $findingResource = FindingResource::class;
    protected array $checkableTypes = [];

    public static function make(): static
    {
        return app(static::class);
    }

    public function findingResource(string $resourceClass): static
    {
        $this->findingResource = $resourceClass;
        return $this;
    }

    public function summaryResource(string $resourceClass): static
    {
        $this->summaryResource = $resourceClass;
        return $this;
    }

    public function checkableTypes(array $checkableTypes): static
    {
        $this->checkableTypes = $checkableTypes;
        return $this;
    }

    public function getId(): string
    {
        return 'filament-model-checker';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                $this->getSummaryResourceClass(),
                $this->getFindingResourceClass(),
            ]);
    }

    public function getSummaryResourceClass(): string
    {
        return $this->summaryResource;
    }

    public function getFindingResourceClass(): string
    {
        return $this->findingResource;
    }

    public function getCheckableTypes(): array
    {
        return $this->checkableTypes;
    }

    public function boot(Panel $panel): void
    {
        // Optional: Boot logic for the plugin
    }
}