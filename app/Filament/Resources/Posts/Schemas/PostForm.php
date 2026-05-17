<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('status')
                    ->options([
                        'visible' => 'Visible',
                        'hidden'  => 'Hidden',
                        'removed' => 'Removed',
                    ])
                    ->required(),

                Select::make('category')
                    ->options(Post::CATEGORIES)
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(150),

                Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}