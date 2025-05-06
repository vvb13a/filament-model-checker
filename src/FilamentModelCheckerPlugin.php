<?php

namespace Vvb13a\FilamentModelChecker;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Vvb13a\FilamentModelChecker\Filament\Resources\FindingResource;
use Vvb13a\FilamentModelChecker\Filament\Resources\SummaryResource;
use Vvb13a\FilamentModelChecker\Filament\Widgets;

class FilamentModelCheckerPlugin implements Plugin
{
    protected string $summaryResource = SummaryResource::class;
    protected string $findingResource = FindingResource::class;
    protected array $checkableTypes = [];

    protected ?Closure $recordTitleMapping = null;
    protected ?Closure $recordUrlMapping = null;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getRecordTitle($record): string
    {
        if ($this->recordTitleMapping instanceof Closure) {
            $mapping = $this->recordTitleMapping;
            return $mapping($record);
        }

        return '';
    }

    public function recordTitle(Closure $mapping): static
    {
        $this->recordTitleMapping = $mapping;
        return $this;
    }

    public function recordUrl(Closure $mapping): static
    {
        $this->recordUrlMapping = $mapping;
        return $this;
    }

    public function getRecordUrl($record): string
    {
        if ($this->recordUrlMapping instanceof Closure) {
            $mapping = $this->recordUrlMapping;
            return $mapping($record);
        }

        return '';
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
            ->widgets([
                Widgets\FindingStats::class,
                Widgets\ModelChecksMetric::class,
                Widgets\ModelFindingsMetric::class,
                Widgets\SummaryStats::class,
            ])
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