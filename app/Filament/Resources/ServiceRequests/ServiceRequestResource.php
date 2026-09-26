<?php

namespace App\Filament\Resources\ServiceRequests;

use App\Filament\Resources\ServiceRequests\Pages\ManageServiceRequests;
use App\Models\ServiceRequest;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ServiceRequestResource extends Resource
{
    protected static ?string $model = ServiceRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'الطلبات';

    protected static ?string $navigationLabel = 'طلبات الخدمات';

    protected static ?string $modelLabel = 'طلب خدمة';

    protected static ?string $pluralModelLabel = 'طلبات الخدمات';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $new = static::getModel()::where('status', 'new')->count();

        return $new > 0 ? (string) $new : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('الاسم')->disabled(),
            TextInput::make('phone')->label('الموبايل')->disabled(),
            TextInput::make('email')->label('الإيميل')->disabled(),
            Select::make('status')
                ->label('الحالة')
                ->options(ServiceRequest::STATUSES)
                ->required(),
            Textarea::make('notes')
                ->label('ملاحظات العميل')
                ->disabled()
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('التصميم')
                    ->disk('public')
                    ->square()
                    ->size(56),
                TextColumn::make('service.name')
                    ->label('الخدمة')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('name')
                    ->label('العميل')
                    ->description(fn (ServiceRequest $record): string => $record->phone)
                    ->searchable(['name', 'phone']),
                TextColumn::make('notes')
                    ->label('الملاحظات')
                    ->limit(40)
                    ->placeholder('من غير ملاحظات')
                    ->wrap(),
                SelectColumn::make('status')
                    ->label('الحالة')
                    ->options(ServiceRequest::STATUSES),
                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->since(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServiceRequests::route('/'),
        ];
    }
}
