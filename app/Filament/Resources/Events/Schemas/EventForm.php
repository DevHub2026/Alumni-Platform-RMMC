<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms;
use Filament\Forms\Form;

class EventForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('event_date')
                    ->required(),

                Forms\Components\TextInput::make('slots')
                    ->numeric()
                    ->default(0)
                    ->helperText('Set to 0 for unlimited slots'),

                Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->disk('public')
                    ->directory('events')
                    ->nullable(),

                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->user()?->id),
            ]);
    }
}