<?php

namespace App\Filament\Resources\EventRegistrations;

use App\Models\EventRegistration;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\EditAction;

use App\Filament\Resources\EventRegistrations\Pages;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static ?string $navigationLabel = 'Registrations';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Content';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options([
                        'confirmed'  => 'Confirmed',
                        'attended'   => 'Attended',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Alumni')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'attended'  => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                TextColumn::make('event.event_date')
                    ->label('Event Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'attended'  => 'Attended',
                        'cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('event')
                    ->relationship('event', 'title'),
            ])

            ->actions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventRegistrations::route('/'),
            'edit'  => Pages\EditEventRegistration::route('/{record}/edit'),
        ];
    }
}
