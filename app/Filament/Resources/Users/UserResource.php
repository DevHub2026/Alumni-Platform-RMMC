<?php

namespace App\Filament\Resources\Users;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Users';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): \UnitEnum|string|null
    {
        return 'Users & Profiles';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

                Select::make('role')
                    ->options([
                        'admin'  => 'Admin',
                        'alumni' => 'Alumni',
                    ])
                    ->required(),

                Toggle::make('is_verified')
                    ->label('Verified Alumni')
                    ->helperText('Verified alumni can create posts'),

                Toggle::make('is_suspended')
                    ->label('Suspended')
                    ->helperText('Suspended users cannot log in'),

                Textarea::make('suspension_reason')
                    ->label('Suspension Reason')
                    ->helperText('Visible to the user when they try to log in')
                    ->nullable()
                    ->rows(2),

                TextInput::make('password')
                    ->password()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) =>
                        filled($state) ? bcrypt($state) : null
                    )
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create')
                    ->label('Password (leave blank to keep current)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable(),

                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin'  => 'danger',
                        'alumni' => 'success',
                        default  => 'gray',
                    }),

                IconColumn::make('is_verified')
                    ->label('Verified')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                IconColumn::make('is_suspended')
                    ->label('Suspended')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),

                Action::make('suspend')
                    ->label('Suspend')
                    ->color('danger')
                    ->icon('heroicon-o-no-symbol')
                    ->form([
                        Textarea::make('suspension_reason')
                            ->label('Reason for suspension')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(fn (User $record, array $data) => $record->update([
                        'is_suspended'      => true,
                        'suspension_reason' => $data['suspension_reason'],
                    ]))
                    ->visible(fn (User $record) =>
                        !$record->is_suspended && $record->role === 'alumni'
                    ),

                Action::make('unsuspend')
                    ->label('Unsuspend')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update([
                        'is_suspended'      => false,
                        'suspension_reason' => null,
                    ]))
                    ->visible(fn (User $record) => $record->is_suspended),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}