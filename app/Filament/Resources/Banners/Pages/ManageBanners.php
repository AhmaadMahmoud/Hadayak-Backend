<?php

namespace App\Filament\Resources\Banners\Pages;

use App\Filament\Resources\Banners\BannerResource;
use App\Models\Banner;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageBanners extends ManageRecords
{
    protected static string $resource = BannerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulkUpload')
                ->label('رفع عدة بانرات مرة واحدة')
                ->icon('heroicon-o-photo')
                ->color('gray')
                ->schema([
                    FileUpload::make('images')
                        ->label('صور الويب (العريضة)')
                        ->helperText('ارفع كذا صورة مرة واحدة — كل صورة هتبقى بانر مستقل بترتيب الرفع. المقاس: 2200×860')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('banners')
                        ->required(),
                    FileUpload::make('mobile_images')
                        ->label('صور الموبايل (اختياري)')
                        ->helperText('بنفس الترتيب: أول صورة موبايل بتتربط بأول بانر، التانية بالتاني… المقاس: 1080×900')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->disk('public')
                        ->directory('banners'),
                ])
                ->action(function (array $data): void {
                    $startOrder = (int) Banner::max('sort_order') + 1;
                    $mobiles = array_values($data['mobile_images'] ?? []);

                    foreach (array_values($data['images']) as $i => $image) {
                        Banner::create([
                            'name' => 'بانر ' . now()->format('d/m') . ' — ' . ($i + 1),
                            'image' => $image,
                            'mobile_image' => $mobiles[$i] ?? null,
                            'sort_order' => $startOrder + $i,
                            'is_active' => true,
                        ]);
                    }

                    Notification::make()
                        ->title('اتعمل ' . count($data['images']) . ' بانر ✅')
                        ->body('تقدر تعدل الاسم واللينك بتاع كل بانر من الجدول')
                        ->success()
                        ->send();
                }),
            CreateAction::make()->label('إضافة بانر'),
        ];
    }
}
