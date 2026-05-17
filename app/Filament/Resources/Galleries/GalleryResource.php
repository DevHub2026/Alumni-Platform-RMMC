<?php

namespace App\Filament\Resources\Galleries;

use App\Models\Gallery;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->options(
                        \App\Models\Event::where('is_published', true)
                            ->pluck('title', 'id')
                    )
                    ->required()
                    ->searchable(),

                FileUpload::make('image_path')
                    ->label('Photo')
                    ->image()
                    ->disk('public')
                    ->directory('gallery')
                    ->required(),

                TextInput::make('caption')
                    ->label('Caption (optional)')
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->disk('public')
                    ->label('Photo')
                    ->square(),

                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable(),

                TextColumn::make('caption')
                    ->limit(40),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit'   => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}