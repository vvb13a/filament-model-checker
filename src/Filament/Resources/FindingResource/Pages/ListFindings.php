<?php

namespace Vvb13a\FilamentModelChecker\Filament\Resources\FindingResource\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;
use Vvb13a\FilamentModelChecker\Filament\Actions\LaunchBulkModelChecksAction;
use Vvb13a\FilamentModelChecker\Filament\Infolists\FindingDetailsSection;
use Vvb13a\FilamentModelChecker\Filament\Resources\FindingResource;
use Vvb13a\FilamentModelChecker\Filament\Tables\Columns\FindingLevelColumn;
use Vvb13a\FilamentModelChecker\Filament\Tables\Filters\CheckableTypesFilter;
use Vvb13a\FilamentModelChecker\Filament\Tables\Filters\CheckNameFilter;
use Vvb13a\FilamentModelChecker\Filament\Tables\Filters\FindingLevelFilter;
use Vvb13a\FilamentModelChecker\Filament\Widgets\FindingStats;
use Vvb13a\LaravelModelChecker\Models\Finding;

class ListFindings extends ListRecords
{
    use ExposesTableToWidgets;

    public static function getResource(): string
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('filament-model-checker')) {
            $plugin = $currentPanel->getPlugin('filament-model-checker');
        }

        return $plugin?->getFindingResourceClass() ?? FindingResource::class;
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
                    }),
                TextColumn::make('checkable.id')
                    ->label('Title')
                    ->wrap()
                    ->formatStateUsing(function ($record): string {
                        return $this->getRecordTitle($record->checkable);
                    }),
                TextColumn::make('check_name')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                FindingLevelColumn::make(),
                Tables\Columns\TextColumn::make('message')
                    ->searchable()
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
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
                FindingLevelFilter::make(),
                CheckNameFilter::make(),
                CheckableTypesFilter::make()
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->modalHeading(false)
                        ->infolist(static::getModalInfolist()),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('open_link')
                        ->icon('heroicon-o-link')
                        ->color('info')
                        ->label('Open Link')
                        ->url(function (Finding $record): string {
                            return $record->url;
                        }, shouldOpenInNewTab: true)
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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

    public static function getModalInfolist(): array
    {
        return [
            FindingDetailsSection::make()
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            LaunchBulkModelChecksAction::make()
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [FindingStats::class];
    }
}
