<?php

namespace App\Filament\Resources\CardDesigns;

use App\Filament\Resources\CardDesigns\Pages\ManageCardDesigns;
use App\Models\CardDesign;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class CardDesignResource extends Resource
{
    protected static ?string $model = CardDesign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'الهدية';

    protected static ?string $navigationLabel = 'كروت المعايدة';

    protected static ?string $modelLabel = 'كارت';

    protected static ?string $pluralModelLabel = 'كروت المعايدة';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(100),
            FileUpload::make('image')
                ->label('الصورة')
                ->image()
                ->disk('public')
                ->directory('cards')
                ->imageEditor(),
            TextInput::make('price')
                ->label('السعر')
                ->numeric()
                ->required()
                ->suffix('ج.م'),
            TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('ظاهر في التطبيق')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('الصورة')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('price')
                    ->label('السعر')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state) . ' ج.م'),
                ToggleColumn::make('is_active')
                    ->label('ظاهر'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCardDesigns::route('/'),
        ];
    }
}
