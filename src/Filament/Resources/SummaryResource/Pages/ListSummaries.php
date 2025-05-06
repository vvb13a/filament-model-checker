<?php

namespace Vvb13a\FilamentModelChecker\Filament\Resources\SummaryResource\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Vvb13a\FilamentModelChecker\Filament\Actions\LaunchBulkModelChecksAction;
use Vvb13a\FilamentModelChecker\Filament\Resources\SummaryResource;
use Vvb13a\FilamentModelChecker\Filament\Tables\Columns\FindingLevelColumn;
use Vvb13a\FilamentModelChecker\Filament\Tables\Filters\CheckableTypesFilter;
use Vvb13a\FilamentModelChecker\Filament\Tables\Filters\FindingLevelFilter;
use Vvb13a\FilamentModelChecker\Filament\Widgets\SummaryStats;

class ListSummaries extends ListRecords
{
    use ExposesTableToWidgets;

    public static function getResource(): string
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('filament-model-checker')) {
            $plugin = $currentPanel->getPlugin('filament-model-checker');
        }

        return $plugin?->getSummaryResourceClass() ?? SummaryResource::class;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('checkable_type')
                    ->label('Type')
                    ->sortable()
                    ->formatStateUsing(function ($record): string {
                        return class_basename(Relation::getMorphedModel($record->checkable_type));
                    })
                    ->searchable(),
                TextColumn::make('checkable.id')
                    ->label('Title')
                    ->wrap()
                    ->formatStateUsing(function ($record): string {
                        return $this->getRecordTitle($record->checkable);
                    }),
                FindingLevelColumn::make('status'),
                TextColumn::make('finding_totals')
                    ->label('Total'),
                TextColumn::make('finding_counts.success')
                    ->label('Success'),
                TextColumn::make('finding_counts.error')
                    ->label('Error'),
                TextColumn::make('finding_counts.warning')
                    ->label('Warning'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->since()
                    ->dateTimeTooltip()
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),
            ])
            ->recordUrl(function ($record): string {
                return $this->getRecordResourceUrl($record->checkable);
            })
            ->filters([
                FindingLevelFilter::make('status'),
                CheckableTypesFilter::make()
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginationPageOptions([10, 25]);
    }

    protected function getRecordTitle($record): string
    {
        $currentPanel = Filament::getCurrentPanel();

        if ($currentPanel && $currentPanel->hasPlugin('filament-model-checker')) {
            $plugin = $currentPanel->getPlugin('filament-model-checker');
            return $plugin->getRecordTitle($record);
        }

        return '';
    }

    protected function getRecordResourceUrl($record): string
    {
        $currentPanel = Filament::getCurrentPanel();

        if ($currentPanel && $currentPanel->hasPlugin('filament-model-checker')) {
            $plugin = $currentPanel->getPlugin('filament-model-checker');
            return $plugin->getRecordUrl($record);
        }

        return '';
    }

    protected function getHeaderActions(): array
    {
        return [
            LaunchBulkModelChecksAction::make()
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [SummaryStats::class];
    }
}
