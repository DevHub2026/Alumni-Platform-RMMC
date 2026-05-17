<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms;
use Filament\Forms\Form;

class AnnouncementForm
{
    public static function configure(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->disk('public')
                    ->directory('announcements')
                    ->nullable(),

                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->user()?->id),
            ]);
    }
}