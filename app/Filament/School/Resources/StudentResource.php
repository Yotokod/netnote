<?php

namespace App\Filament\School\Resources;

use App\Filament\School\Resources\StudentResource\Pages;
use App\Filament\School\Resources\StudentResource\RelationManagers;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Élèves';
    protected static ?string $modelLabel = 'Élève';
    protected static ?string $pluralModelLabel = 'Élèves';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations personnelles')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('Prénom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('matricule')
                            ->label('Matricule')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date de naissance')
                            ->required(),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Lieu de naissance')
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label('Genre')
                            ->required()
                            ->options([
                                'male' => 'Masculin',
                                'female' => 'Féminin',
                            ]),
                    ])->columns(3),

                Forms\Components\Section::make('Informations complémentaires')
                    ->schema([
                        Forms\Components\Select::make('nationality_id')
                            ->label('Nationalité')
                            ->relationship('nationality', 'name'),
                        Forms\Components\Select::make('religion_id')
                            ->label('Religion')
                            ->relationship('religion', 'name'),
                        Forms\Components\TextInput::make('blood_group')
                            ->label('Groupe sanguin')
                            ->maxLength(10),
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Photo')
                            ->image()
                            ->directory('students/photos'),
                    ])->columns(2),

                Forms\Components\Section::make('Informations médicales')
                    ->schema([
                        Forms\Components\KeyValue::make('medical_info')
                            ->label('Informations médicales')
                            ->keyLabel('Type')
                            ->valueLabel('Détails'),
                    ])->collapsible(),

                Forms\Components\Section::make('Documents')
                    ->schema([
                        Forms\Components\FileUpload::make('documents')
                            ->label('Documents')
                            ->multiple()
                            ->directory('students/documents'),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('matricule')
                    ->label('Matricule')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Date de naissance')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Genre')
                    ->formatStateUsing(fn (string $state): string => $state === 'male' ? 'M' : 'F')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'male' ? 'primary' : 'pink'),
                Tables\Columns\TextColumn::make('nationality.name')
                    ->label('Nationalité')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('Genre')
                    ->options([
                        'male' => 'Masculin',
                        'female' => 'Féminin',
                    ]),
                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label('Nationalité')
                    ->relationship('nationality', 'name')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('bulletin')
                    ->label('Bulletin')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Student $record): string => '#'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
