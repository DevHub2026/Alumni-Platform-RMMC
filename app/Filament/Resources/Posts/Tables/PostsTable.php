<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;

class PostsTable
{
    public static function configure(Table $table): Table
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
                Action::make('approve')
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

                Action::make('hide')
                    ->label('Hide')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Post $record) =>
                        $record->update(['status' => 'hidden'])
                    )
                    ->visible(fn (Post $record) =>
                        $record->status === 'visible'
                    ),

                Action::make('remove')
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

                EditAction::make(),
            ]);
    }
}