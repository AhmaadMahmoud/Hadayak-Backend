<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use UnitEnum;

class SiteSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'الموقع';

    protected static ?string $navigationLabel = 'إعدادات المتجر';

    protected static ?string $title = 'إعدادات المتجر';

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    /** مفاتيح الإعدادات وقيمها الافتراضية */
    private const KEYS = [
        'delivery_fee' => '50',
        'cod_enabled' => '0',
        'delivery_summary' => '١–٣ أيام عمل للقاهرة والجيزة، و٣–٥ أيام لباقي المحافظات',
        'support_phone' => '',
        'support_whatsapp' => '',
        'support_email' => 'info@hdayak.com',
        'social_facebook' => '',
        'social_instagram' => '',
        'social_tiktok' => '',
    ];

    public function mount(): void
    {
        $data = [];
        foreach (self::KEYS as $key => $default) {
            $data[$key] = Setting::get($key, $default);
        }
        $data['cod_enabled'] = (bool) (int) $data['cod_enabled'];
        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('التوصيل')
                    ->schema([
                        TextInput::make('delivery_fee')->label('رسوم التوصيل الافتراضية (لو العنوان من غير محافظة)')->numeric()->suffix('ج.م'),
                        TextInput::make('delivery_summary')->label('جملة مدة التوصيل (بتظهر في صفحة المنتج)'),
                        Toggle::make('cod_enabled')->label('الدفع عند الاستلام متاح'),
                    ])->columns(2),
                Section::make('التواصل والدعم')
                    ->schema([
                        TextInput::make('support_phone')->label('رقم الاتصال')->tel(),
                        TextInput::make('support_whatsapp')->label('رقم الواتساب (بكود الدولة، مثال 2010xxxxxxx)'),
                        TextInput::make('support_email')->label('بريد الدعم')->email(),
                    ])->columns(3),
                Section::make('السوشيال ميديا (سيب الفاضي والأيقونة مش هتظهر)')
                    ->schema([
                        TextInput::make('social_facebook')->label('فيسبوك')->url()->placeholder('https://facebook.com/...'),
                        TextInput::make('social_instagram')->label('انستجرام')->url()->placeholder('https://instagram.com/...'),
                        TextInput::make('social_tiktok')->label('تيك توك')->url()->placeholder('https://tiktok.com/@...'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['cod_enabled'] = $data['cod_enabled'] ? '1' : '0';

        foreach (self::KEYS as $key => $default) {
            Setting::set($key, (string) ($data[$key] ?? $default));
        }

        Notification::make()->title('تم حفظ الإعدادات ✅')->success()->send();
    }
}
