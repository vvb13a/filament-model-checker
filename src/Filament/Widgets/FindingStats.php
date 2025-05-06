<?php

namespace Vvb13a\LaravelModelChecker\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Vvb13a\LaravelModelChecker\Filament\Resources\FindingResource\Pages\ListFindings;
use Vvb13a\LaravelResponseChecker\Enums\FindingLevel;

class FindingStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    protected function getTablePage(): string
    {
        return ListFindings::class;
    }

    protected function getStats(): array
    {
        $statusCounts = $this->getPageTableQuery()
            ->select('level', DB::raw('COUNT(*) as count'))
            ->groupBy('level')
            ->pluck('count', 'level');

        $successCount = $statusCounts->get(FindingLevel::SUCCESS->value, 0);
        $warningCount = $statusCounts->get(FindingLevel::WARNING->value, 0);
        $errorCount = $statusCounts->get(FindingLevel::ERROR->value, 0);
        $totalCount = $statusCounts->sum();

        return $this->getStatsArr(
            total: $totalCount,
            warning: $warningCount,
            error: $errorCount,
            success: $successCount
        );
    }

    private function getStatsArr(int $total = 0, int $warning = 0, int $error = 0, int $success = 0): array
    {
        return [
            Stat::make('Total', $total)
                ->description('Total Records')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('gray'),

            Stat::make('Success', $success)
                ->description('Success Records')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Warnings', $warning)
                ->description('Warning Records')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Errors', $error)
                ->description('Error Records')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
