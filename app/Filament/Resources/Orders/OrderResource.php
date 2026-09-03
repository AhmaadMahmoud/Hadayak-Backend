<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'الطلبات';

    protected static ?string $navigationLabel = 'الطلبات';

    protected static ?string $modelLabel = 'طلب';

    protected static ?string $pluralModelLabel = 'الطلبات';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $pending = Order::where('status', 'pending')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('حالة الطلب')
                ->columns(2)
                ->components([
                    Select::make('status')
                        ->label('الحالة')
                        ->options(Order::STATUSES)
                        ->required()
                        ->native(false),
                    Select::make('payment_status')
                        ->label('حالة الدفع')
                        ->options([
                            'pending' => 'مستني الدفع',
                            'paid' => 'مدفوع',
                            'failed' => 'فشل',
                            'refunded' => 'مسترد',
                        ])
                        ->required()
                        ->native(false),
                ]),

            Section::make('تفاصيل الطلب')
                ->columns(2)
                ->components([
                    Placeholder::make('customer')
                        ->label('العميل')
                        ->content(fn (Order $record): string => $record->user?->name . ' — ' . ($record->user?->phone ?? $record->user?->email ?? '')),
                    Placeholder::make('delivery')
                        ->label('نوع التوصيل')
                        ->content(fn (Order $record): string => $record->delivery_type === 'gift'
                            ? '🎁 هدية إلى: ' . $record->recipient_name . ' (' . $record->recipient_phone . ') — من غير فاتورة سعر'
                            : 'توصيل للعميل نفسه'),
                    Placeholder::make('address')
                        ->label('العنوان')
                        ->content(function (Order $record): HtmlString {
                            if (! $record->address) {
                                return new HtmlString('—');
                            }

                            $a = $record->address;
                            $text = e(collect([$a->area, $a->street, $a->building ? 'مبنى '.$a->building : null, $a->floor ? 'دور '.$a->floor : null, $a->apartment ? 'شقة '.$a->apartment : null, $a->landmark])->filter()->implode('، '));

                            if ($a->lat && $a->lng) {
                                $url = "https://www.google.com/maps?q={$a->lat},{$a->lng}";
                                $text .= ' &nbsp; <a href="'.$url.'" target="_blank" style="color:#D81D35;font-weight:bold;text-decoration:underline;">📍 افتح على الخريطة</a>';
                            }

                            return new HtmlString($text);
                        })
                        ->columnSpanFull(),
                    Placeholder::make('items_list')
                        ->label('المنتجات')
                        ->content(fn (Order $record): HtmlString => new HtmlString(
                            $record->items->map(fn ($i) => e($i->product_name) . ' × ' . $i->qty . ' — ' . number_format($i->price * $i->qty) . ' ج.م')->implode('<br>')
                        ))
                        ->columnSpanFull(),
                    Placeholder::make('extras')
                        ->label('الإضافات')
                        ->content(function (Order $record): HtmlString {
                            $lines = [];
                            if ($record->wrapOption) {
                                $lines[] = '🎀 تغليف: ' . e($record->wrapOption->name) . ' — ' . number_format((float) $record->wrap_price) . ' ج.م';
                            }
                            if ($record->cardDesign) {
                                $lines[] = '💌 كارت: ' . e($record->cardDesign->name) . ' — ' . number_format((float) $record->card_price) . ' ج.م';
                            }
                            if ($record->card_message) {
                                $lines[] = '«' . e($record->card_message) . '»';
                            }

                            return new HtmlString($lines ? implode('<br>', $lines) : 'من غير إضافات');
                        })
                        ->columnSpanFull(),
                    Placeholder::make('totals')
                        ->label('الحساب')
                        ->content(fn (Order $record): HtmlString => new HtmlString(
                            'المنتجات: ' . number_format((float) $record->items_total) . ' ج.م<br>' .
                            'التوصيل: ' . number_format((float) $record->delivery_fee) . ' ج.م<br>' .
                            '<strong>الإجمالي: ' . number_format((float) $record->total) . ' ج.م</strong> — ' .
                            (Order::PAYMENT_METHODS[$record->payment_method] ?? $record->payment_method)
                        )),
                ]),

            Section::make('ملاحظات')
                ->components([
                    Textarea::make('notes')
                        ->label('ملاحظات داخلية')
                        ->rows(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('number')
                    ->label('رقم الطلب')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->label('العميل')
                    ->searchable(),
                TextColumn::make('delivery_type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'gift' ? '🎁 هدية' : 'توصيل لي')
                    ->color(fn (string $state): string => $state === 'gift' ? 'warning' : 'gray'),
                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format((float) $state) . ' ج.م'),
                TextColumn::make('payment_method')
                    ->label('الدفع')
                    ->formatStateUsing(fn (string $state): string => Order::PAYMENT_METHODS[$state] ?? $state),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Order::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed', 'preparing' => 'info',
                        'delivering' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(Order::STATUSES),
                SelectFilter::make('delivery_type')
                    ->label('النوع')
                    ->options(['me' => 'توصيل لي', 'gift' => 'هدية']),
            ])
            ->recordActions([
                EditAction::make()->label('عرض وتعديل'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
