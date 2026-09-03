<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|UnitEnum|null $navigationGroup = 'الكتالوج';

    protected static ?string $navigationLabel = 'المنتجات';

    protected static ?string $modelLabel = 'منتج';

    protected static ?string $pluralModelLabel = 'المنتجات';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المنتج')
                ->columns(2)
                ->components([
                    TextInput::make('name')
                        ->label('اسم المنتج')
                        ->required()
                        ->maxLength(150)
                        ->columnSpanFull(),
                    Select::make('category_id')
                        ->label('القسم')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    TextInput::make('price')
                        ->label('السعر')
                        ->numeric()
                        ->required()
                        ->suffix('ج.م'),
                    Textarea::make('description')
                        ->label('الوصف')
                        ->rows(5)
                        ->columnSpanFull(),
                ]),
            Section::make('الصور')
                ->components([
                    FileUpload::make('images_upload')
                        ->label('صور المنتج (الأولى هي الرئيسية)')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('products')
                        ->imageEditor()
                        ->dehydrated(false)
                        ->afterStateHydrated(function (FileUpload $component, $record) {
                            if ($record) {
                                $component->state($record->images->pluck('path')->all());
                            }
                        }),
                ]),
            Section::make('الإتاحة')
                ->columns(2)
                ->components([
                    TextInput::make('stock')
                        ->label('المخزون')
                        ->numeric()
                        ->placeholder('سيبه فاضي = غير محدود'),
                    Toggle::make('is_active')
                        ->label('ظاهر في التطبيق')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('images.0.path')
                    ->label('الصورة')
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')
                    ->label('المنتج')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->label('القسم')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('price')
                    ->label('السعر')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format((float) $state) . ' ج.م'),
                TextColumn::make('stock')
                    ->label('المخزون')
                    ->placeholder('غير محدود'),
                ToggleColumn::make('is_active')
                    ->label('ظاهر'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('القسم')
                    ->relationship('category', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    /** حفظ الصور المتعددة في جدول product_images */
    public static function syncImages(Product $product, ?array $paths): void
    {
        if ($paths === null) {
            return;
        }

        $product->images()->delete();

        foreach (array_values($paths) as $i => $path) {
            $product->images()->create(['path' => $path, 'sort_order' => $i]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
