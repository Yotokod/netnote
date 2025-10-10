<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\TemplateResource\Pages;
use App\Filament\SuperAdmin\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Templates';
    protected static ?string $modelLabel = 'Template';
    protected static ?string $pluralModelLabel = 'Templates';
    protected static ?string $navigationGroup = 'Gestion du Contenu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du template')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('kind')
                            ->label('Type de template')
                            ->required()
                            ->options([
                                'homepage' => 'Page d\'accueil',
                                'bulletin' => 'Bulletin',
                            ]),
                        Forms\Components\Select::make('author_id')
                            ->label('Auteur')
                            ->relationship('author', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('version')
                            ->label('Version')
                            ->default('1.0')
                            ->maxLength(10),
                    ])->columns(2),

                Forms\Components\Section::make('Contenu')
                    ->schema([
                        Forms\Components\Textarea::make('html_content')
                            ->label('Contenu HTML')
                            ->required()
                            ->rows(10)
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('shortcodes')
                            ->label('Shortcodes disponibles')
                            ->keyLabel('Shortcode')
                            ->valueLabel('Description')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\FileUpload::make('preview_path')
                            ->label('Image de prévisualisation')
                            ->image()
                            ->directory('templates/previews'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('kind')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => $state === 'homepage' ? 'Page d\'accueil' : 'Bulletin')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'homepage' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Auteur')
                    ->sortable(),
                Tables\Columns\TextColumn::make('version')
                    ->label('Version')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('preview_path')
                    ->label('Aperçu')
                    ->size(50),
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
                Tables\Filters\SelectFilter::make('kind')
                    ->label('Type de template')
                    ->options([
                        'homepage' => 'Page d\'accueil',
                        'bulletin' => 'Bulletin',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->boolean()
                    ->trueLabel('Actifs seulement')
                    ->falseLabel('Inactifs seulement')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('Aperçu')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Template $record): string => '#')
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
