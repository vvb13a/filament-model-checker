<?php

namespace Vvb13a\FilamentModelChecker\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Vvb13a\LaravelModelChecker\Models\Summary;
use Vvb13a\LaravelResponseChecker\Enums\FindingLevel;

class ModelFindingsMetric extends ChartWidget
{
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = null;
    protected static ?string $maxHeight = '200px';

    public ?Summary $summary;
    public int $totalFindings;

    public function getHeading(): string|Htmlable|null
    {
        return 'Findings by Severity: '.$this->totalFindings ?? 0;
    }

    protected function getData(): array
    {
        $this->loadData();

        if (!$this->summary) {
            return $this->getPlaceholdData('Status not available');
        }

        $counts = $this->summary->finding_counts ?? [];
        $this->totalFindings = $this->summary->finding_totals ?? 0;

        $data = collect($counts)->filter();

        if ($data->isEmpty()) {
            $this->totalFindings = 0;
            if ($this->summary->status === FindingLevel::SUCCESS) {
                return $this->getPlaceholdData('All Checks Passed', FindingLevel::SUCCESS->value);
            } else {
                return $this->getPlaceholdData('No Findings Data');
            }
        }

        $colors = $data->map(fn($value, $key) => $this->getColorForLevel($key));

        return [
            'datasets' => [
                [
                    'label' => 'Findings by Level',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => $colors->values()->toArray(),
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $data->keys()->map(fn($key) => ucfirst($key))->toArray(),
        ];
    }

    protected function loadData(): void
    {
        $this->record->loadMissing('findingsSummary');
    }

    protected function getPlaceholdData(string $message, string $level = null): array
    {
        $this->totalFindings = 0;
        return [
            'datasets' => [
                [
                    'label' => $message,
                    'data' => [1],
                    'backgroundColor' => [$this->getColorForLevel($level)],
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                ]
            ],
            'labels' => [$message],
        ];
    }

    protected function getColorForLevel(?string $level): string
    {
        $rgb = match ($level) {
            FindingLevel::SUCCESS->value => Color::Green['500'],
            FindingLevel::WARNING->value => Color::Amber['500'],
            FindingLevel::ERROR->value => Color::Red['500'],
            FindingLevel::INFO->value => Color::Blue['500'],
            default => Color::Gray['300'],
        };

        return "rgb({$rgb})";
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
