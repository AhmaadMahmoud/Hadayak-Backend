<?php

namespace App\Filament\Resources\Governorates;

use App\Filament\Resources\Governorates\Pages\ManageGovernorates;
use App\Models\Governorate;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'الموقع';

    protected static ?string $navigationLabel = 'الشحن والمحافظات';

    protected static ?string $modelLabel = 'محافظة';

    protected static ?string $pluralModelLabel = 'الشحن والمحافظات';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('المحافظة')->required(),
            TextInput::make('shipping_fee')->label('رسوم الشحن')->numeric()->required()->suffix('ج.م'),
            TextInput::make('delivery_days')->label('مدة التوصيل')->required()->placeholder('مثال: ١–٣ أيام عمل'),
            Toggle::make('is_active')->label('التوصيل متاح')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                TextColumn::make('name')->label('المحافظة')->searchable()->weight('bold'),
                TextInputColumn::make('shipping_fee')->label('رسوم الشحن (ج.م)')->rules(['numeric', 'min:0']),
                TextInputColumn::make('delivery_days')->label('مدة التوصيل'),
                ToggleColumn::make('is_active')->label('متاح'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGovernorates::route('/'),
        ];
    }
}
