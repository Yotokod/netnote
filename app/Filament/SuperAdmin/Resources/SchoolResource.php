<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SchoolResource\Pages;
use App\Filament\SuperAdmin\Resources\SchoolResource\RelationManagers;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Écoles';
    protected static ?string $modelLabel = 'École';
    protected static ?string $pluralModelLabel = 'Écoles';
    protected static ?string $navigationGroup = 'Gestion des Établissements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom de l\'école')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('founder')
                            ->label('Fondateur')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('year_founded')
                            ->label('Année de fondation')
                            ->required()
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(date('Y')),
                        Forms\Components\TextInput::make('subdomain')
                            ->label('Sous-domaine')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('custom_domain')
                            ->label('Domaine personnalisé')
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Localisation')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->label('Pays')
                            ->relationship('country', 'name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('city_id', null)),
                        Forms\Components\Select::make('city_id')
                            ->label('Ville')
                            ->relationship('city', 'name', fn (Builder $query, Forms\Get $get) => 
                                $query->where('country_id', $get('country_id'))
                            )
                            ->required(),
                        Forms\Components\TextInput::make('quartier')
                            ->label('Quartier')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Contact')
                    ->schema([
                        Forms\Components\Repeater::make('phones')
                            ->label('Téléphones')
                            ->simple(
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                            )
                            ->defaultItems(1)
                            ->maxItems(3),
                        Forms\Components\TextInput::make('email_pro')
                            ->label('Email professionnel')
                            ->email()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('about')
                            ->label('À propos')
                            ->rows(3),
                        Forms\Components\Textarea::make('bibliography')
                            ->label('Bibliographie')
                            ->rows(3),
                    ])->columns(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\Select::make('template_id')
                            ->label('Template de page d\'accueil')
                            ->relationship('template', 'name', fn (Builder $query) => 
                                $query->where('kind', 'homepage')->where('is_active', true)
                            ),
                        Forms\Components\Select::make('bulletin_template_id')
                            ->label('Template de bulletin')
                            ->relationship('bulletinTemplate', 'name', fn (Builder $query) => 
                                $query->where('kind', 'bulletin')->where('is_active', true)
                            ),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subdomain')
                    ->label('Sous-domaine')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Sous-domaine copié!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Ville')
                    ->sortable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Pays')
                    ->sortable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Élèves')
                    ->counts('students')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Statut')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->boolean()
                    ->trueLabel('Actives seulement')
                    ->falseLabel('Inactives seulement')
                    ->native(false),
                Tables\Filters\SelectFilter::make('country_id')
                    ->label('Pays')
                    ->relationship('country', 'name')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('visit')
                    ->label('Visiter')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (School $record): string => 'http://' . $record->subdomain . '.localhost:8000')
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }
}
