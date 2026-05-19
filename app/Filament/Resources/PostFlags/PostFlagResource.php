<?php

namespace App\Filament\Resources\PostFlags;

use App\Models\PostFlag;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\SelectFilter;

use Filament\Actions\ViewAction;
use Filament\Actions\Action as TableAction;

use App\Filament\Resources\PostFlags\Pages;

class PostFlagResource extends Resource
{
    protected static ?string $model = PostFlag::class;

    protected static ?string $navigationLabel = 'Flagged Posts';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Moderation';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['post', 'user'])
            ->latest();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('post.title')
                    ->label('Post Title')
                    ->disabled(),

                TextInput::make('user.name')
                    ->label('Reported By')
                    ->disabled(),

                Select::make('reason')
                    ->options(PostFlag::REASONS)
                    ->disabled(),

                Textarea::make('details')
                    ->label('Additional Details')
                    ->disabled()
                    ->columnSpanFull(),

                TextInput::make('created_at')
                    ->label('Reported At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title')
                    ->label('Post')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('post.user.name')
                    ->label('Post Author')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Reported By')
                    ->searchable(),

                TextColumn::make('reason')
                    ->badge()
                    ->formatStateUsing(fn ($state) =>
                        PostFlag::REASONS[$state] ?? $state
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'spam'           => 'danger',
                        'inappropriate'  => 'danger',
                        'misinformation' => 'warning',
                        'harassment'     => 'danger',
                        default          => 'gray',
                    }),

                TextColumn::make('details')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])

            ->defaultSort('created_at', 'desc')

            ->filters([
                SelectFilter::make('reason')
                    ->options(PostFlag::REASONS),
            ])

            ->actions([
                ViewAction::make(),

                TableAction::make('approve')
                    ->label('Approve Post')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (PostFlag $record) =>
                        $record->post->update([
                            'status'     => 'visible',
                            'is_flagged' => false,
                        ]) && $record->delete()
                    ),

                TableAction::make('remove')
                    ->label('Remove Post')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (PostFlag $record) =>
                        $record->post->update([
                            'status'     => 'removed',
                            'is_flagged' => false,
                        ]) && $record->delete()
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostFlags::route('/'),
        ];
    }
}
