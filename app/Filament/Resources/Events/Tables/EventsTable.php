<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Tables;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event_date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slots')
                    ->label('Slots'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('event_date', 'asc');
    }
}