<?php

namespace Vvb13a\FilamentModelChecker\Filament\Widgets;

use Filament\Support\Colors\Color;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Vvb13a\LaravelModelChecker\Models\Summary;
use Vvb13a\LaravelResponseChecker\Enums\FindingLevel;

class AggregateCheckStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected static ?string $maxHeight = '200px';
    public ?string $filter = 'all';
    protected int $totalModelsWithStatus = 0;

    public function getHeading(): string|Htmlable|null
    {
        return "Status Distribution: ".$this->totalModelsWithStatus ?? 0;
    }

    protected function getData(): array
    {
        $checkableType = $this->filters['checkable_type'] ?? null;

        if (!$checkableType) {
            return $this->getPlaceholdData('Select a model type');
        }

        $statusCounts = Summary::query()
            ->where('checkable_type', $checkableType)
            ->select('overall_status', DB::raw('COUNT(*) as count'))
            ->groupBy('overall_status')
            ->pluck('count', 'overall_status');

        $initializedCounts = collect(FindingLevel::cases())
            ->mapWithKeys(fn(FindingLevel $level) => [$level->value => 0]);

        $data = $initializedCounts->merge($statusCounts);

        $this->totalModelsWithStatus = $data->sum();

        $chartData = $data->filter();

        if ($chartData->isEmpty()) {
            $this->totalModelsWithStatus = 0;
            return $this->getPlaceholdData('No status records found');
        }

        $colors = $chartData->map(fn($value, $key) => $this->getColorForLevel($key));

        return [
            'datasets' => [
                [
                    'label' => 'Models by Status',
                    'data' => $chartData->values()->toArray(),
                    'backgroundColor' => $colors->values()->toArray(),
                    'borderColor' => 'transparent',
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $chartData->keys()->map(fn($key) => ucfirst($key))->toArray(),
        ];
    }

    /**
     * Generate placeholder data for empty or loading states.
     */
    protected function getPlaceholdData(string $message, string $bgColor = null): array
    {
        $this->totalModelsWithStatus = 0;
        return [
            'datasets' => [['label' => $message, 'data' => [1], 'backgroundColor' => [$this->getColorForLevel(null)]]],
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

    protected function getType(): string
    {
        return 'doughnut';
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
}
