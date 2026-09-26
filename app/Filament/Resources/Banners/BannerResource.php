<?php

namespace App\Filament\Resources\Banners;

use App\Filament\Resources\Banners\Pages\ManageBanners;
use App\Models\Banner;
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

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'الموقع';

    protected static ?string $navigationLabel = 'بانرات الموقع';

    protected static ?string $modelLabel = 'بانر';

    protected static ?string $pluralModelLabel = 'البانرات';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('اسم البانر (ليك انت بس)')
                ->placeholder('مثال: عرض عيد الأم')
                ->required()
                ->maxLength(100),
            FileUpload::make('image')
                ->label('صورة البانر')
                ->helperText('المقاس المثالي: 2200 × 860 بكسل (أو أي صورة عريضة بنسبة 5:2 تقريبًا)')
                ->image()
                ->disk('public')
                ->directory('banners')
                ->imageEditor()
                ->required(),
            TextInput::make('link')
                ->label('اللينك عند الضغط')
                ->placeholder('مثال: /products?category=3 أو https://...')
                ->helperText('سيبه فاضي لو مش عايز البانر يوَدّي على أي مكان')
                ->maxLength(500),
            TextInput::make('sort_order')
                ->label('الترتيب')
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label('ظاهر على الموقع')
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
                    ->width(160)
                    ->height(64),
                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('link')
                    ->label('اللينك')
                    ->limit(40)
                    ->placeholder('من غير لينك'),
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
            'index' => ManageBanners::route('/'),
        ];
    }
}
