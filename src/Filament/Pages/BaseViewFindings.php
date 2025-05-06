<?php

namespace Vvb13a\LaravelModelChecker\Filament\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vvb13a\LaravelModelChecker\Filament\Actions\LaunchModelChecksAction;
use Vvb13a\LaravelModelChecker\Filament\Infolists\FindingDetailsSection;
use Vvb13a\LaravelModelChecker\Filament\Tables\Columns\FindingLevelColumn;
use Vvb13a\LaravelModelChecker\Filament\Tables\Filters\CheckNameFilter;
use Vvb13a\LaravelModelChecker\Filament\Tables\Filters\FindingLevelFilter;
use Vvb13a\LaravelModelChecker\Filament\Widgets\ModelChecksMetric;
use Vvb13a\LaravelModelChecker\Filament\Widgets\ModelFindingsMetric;
use Vvb13a\LaravelModelChecker\Models\Finding;

class BaseViewFindings extends ManageRelatedRecords
{
    protected static string $relationship = 'findings';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Findings';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('check_name')
            ->columns([
                TextColumn::make('check_name')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                FindingLevelColumn::make(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable()
                    ->limit(50)
                    ->formatStateUsing(function ($record): string {
                        return str($record->url)->after(50);
                    })
                    ->alignCenter()
                    ->tooltip(fn($state): string => $state),
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
            ->filters([
                FindingLevelFilter::make(),
                CheckNameFilter::make(),
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
            ->defaultSort('level', 'desc')
            ->paginationPageOptions([10, 25]);
    }

    public static function getModalInfolist(): array
    {
        return [
            FindingDetailsSection::make()
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ModelChecksMetric::make(['summary' => $this->record->findingsSummary]),
            ModelFindingsMetric::make(['summary' => $this->record->findingsSummary]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            LaunchModelChecksAction::make()
        ];
    }
}
