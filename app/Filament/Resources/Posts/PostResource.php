<?php

namespace App\Filament\Resources\Posts;

use App\Models\Post;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

use Filament\Tables\Filters\SelectFilter;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action as TableAction;

use App\Filament\Resources\Posts\Pages;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationLabel = 'Posts';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Community';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['comments', 'flags'])
            ->with(['user', 'flags']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('user.name')
                    ->label('Author')
                    ->disabled(),

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

                TextInput::make('comments_count')
                    ->label('Comments')
                    ->disabled(),

                TextInput::make('flags_count')
                    ->label('Flags')
                    ->disabled(),

                Textarea::make('flag_reasons')
    ->label('Flag Reasons')
    ->formatStateUsing(function ($record) {

        return $record->flags
            ->map(function ($flag) {

                return \App\Models\PostFlag::REASONS[$flag->reason]
                    ?? $flag->reason;

            })
            ->unique()
            ->implode(', ');

    })
    ->disabled()
    ->columnSpanFull(),

                Toggle::make('is_flagged')
                    ->label('Flagged')
                    ->disabled(),

                TextInput::make('created_at')
                    ->label('Created At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) =>
                        Post::CATEGORIES[$state] ?? $state
                    )
                    ->color(fn ($state): string => match ($state) {
                        'career_update' => 'info',
                        'achievement'   => 'warning',
                        'opportunity'   => 'success',
                        'reunion'       => 'primary',
                        default         => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'visible' => 'success',
                        'hidden'  => 'warning',
                        'removed' => 'danger',
                        default   => 'gray',
                    }),

                IconColumn::make('is_flagged')
                    ->label('Flagged')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('flags.reason')
                    ->label('Flag Reasons')
                    ->badge()
                    ->formatStateUsing(fn ($state) =>
                        \App\Models\PostFlag::REASONS[$state] ?? $state
                    ),

                TextColumn::make('flags_count')
                    ->counts('flags')
                    ->label('Flags')
                    ->sortable(),

                TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->defaultSort('is_flagged', 'desc')

            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'visible' => 'Visible',
                        'hidden'  => 'Hidden',
                        'removed' => 'Removed',
                    ]),

                SelectFilter::make('category')
                    ->options(Post::CATEGORIES),
            ])

            ->actions([

                ViewAction::make(),

                EditAction::make(),

                TableAction::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Post $record) => $record->update([
                        'status'     => 'visible',
                        'is_flagged' => false,
                    ]))
                    ->visible(fn (Post $record) =>
                        $record->is_flagged || $record->status !== 'visible'
                    ),

                TableAction::make('hide')
                    ->label('Hide')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Post $record) =>
                        $record->update(['status' => 'hidden'])
                    )
                    ->visible(fn (Post $record) =>
                        $record->status === 'visible'
                    ),

                TableAction::make('remove')
                    ->label('Remove')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Post $record) => $record->update([
                        'status'     => 'removed',
                        'is_flagged' => false,
                    ]))
                    ->visible(fn (Post $record) =>
                        $record->status !== 'removed'
                    ),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'view'  => Pages\ViewPost::route('/{record}'),
            'edit'  => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}