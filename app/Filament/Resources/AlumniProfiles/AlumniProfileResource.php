<?php

namespace App\Filament\Resources\AlumniProfiles;

use App\Models\AlumniProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AlumniProfileResource extends Resource
{
    protected static ?string $model = AlumniProfile::class;

    protected static ?string $navigationLabel = 'Alumni Profiles';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),

                TextInput::make('student_id')
                    ->label('Student ID')
                    ->maxLength(50),

                TextInput::make('course')
                    ->maxLength(100),

                TextInput::make('graduation_year')
                    ->numeric()
                    ->minValue(1990)
                    ->maxValue(date('Y')),

                TextInput::make('phone')
                    ->maxLength(20),

                TextInput::make('current_job')
                    ->maxLength(100),

                TextInput::make('company')
                    ->maxLength(100),

                TextInput::make('linkedin_url')
                    ->url()
                    ->maxLength(255),

                TextInput::make('address')
                    ->maxLength(255),

                Textarea::make('bio')
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Alumni Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('course')
                    ->searchable(),

                TextColumn::make('graduation_year')
                    ->label('Grad Year')
                    ->sortable(),

                TextColumn::make('current_job')
                    ->label('Job Title'),

                TextColumn::make('company'),

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
            'index'  => Pages\ListAlumniProfiles::route('/'),
            'create' => Pages\CreateAlumniProfile::route('/create'),
            'edit'   => Pages\EditAlumniProfile::route('/{record}/edit'),
        ];
    }
}