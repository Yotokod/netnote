<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\PlanResource\Pages;
use App\Filament\SuperAdmin\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Plans & Tarifs';
    protected static ?string $modelLabel = 'Plan';
    protected static ?string $pluralModelLabel = 'Plans';
    protected static ?string $navigationGroup = 'Gestion Commerciale';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du plan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                        Forms\Components\TextInput::make('price')
                            ->label('Prix (FCFA)')
                            ->required()
                            ->numeric()
                            ->prefix('FCFA'),
                        Forms\Components\Select::make('billing_cycle')
                            ->label('Cycle de facturation')
                            ->required()
                            ->options([
                                'monthly' => 'Mensuel',
                                'yearly' => 'Annuel',
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make('Limites')
                    ->schema([
                        Forms\Components\TextInput::make('max_students')
                            ->label('Nombre maximum d\'élèves')
                            ->numeric()
                            ->placeholder('Laisser vide pour illimité'),
                        Forms\Components\TextInput::make('max_teachers')
                            ->label('Nombre maximum de professeurs')
                            ->numeric()
                            ->placeholder('Laisser vide pour illimité'),
                        Forms\Components\TextInput::make('max_storage_gb')
                            ->label('Stockage maximum (GB)')
                            ->numeric()
                            ->placeholder('Laisser vide pour illimité'),
                    ])->columns(3),

                Forms\Components\Section::make('Fonctionnalités')
                    ->schema([
                        Forms\Components\Toggle::make('has_custom_domain')
                            ->label('Domaine personnalisé'),
                        Forms\Components\Toggle::make('has_advanced_reports')
                            ->label('Rapports avancés'),
                        Forms\Components\Toggle::make('has_api_access')
                            ->label('Accès API'),
                        Forms\Components\Toggle::make('has_priority_support')
                            ->label('Support prioritaire'),
                    ])->columns(2),

                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Actif')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Ordre d\'affichage')
                            ->numeric()
                            ->default(0),
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
                Tables\Columns\TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF')
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->label('Cycle')
                    ->formatStateUsing(fn (string $state): string => $state === 'monthly' ? 'Mensuel' : 'Annuel')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'monthly' ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('max_students')
                    ->label('Max élèves')
                    ->formatStateUsing(fn (?int $state): string => $state ? number_format($state) : 'Illimité')
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_custom_domain')
                    ->label('Domaine')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_advanced_reports')
                    ->label('Rapports')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Statut')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('Abonnements')
                    ->counts('subscriptions')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->boolean()
                    ->trueLabel('Actifs seulement')
                    ->falseLabel('Inactifs seulement')
                    ->native(false),
                Tables\Filters\SelectFilter::make('billing_cycle')
                    ->label('Cycle de facturation')
                    ->options([
                        'monthly' => 'Mensuel',
                        'yearly' => 'Annuel',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
