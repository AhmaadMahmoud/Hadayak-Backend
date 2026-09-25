<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * نقل كل الداتا من ملف SQLite القديم لداتابيز MySQL الجديدة.
 * شغّل الأول: php artisan migrate  (عشان الجداول تتعمل في MySQL)
 * وبعدين:     php artisan hadayak:copy-sqlite
 */
class CopySqliteToMysql extends Command
{
    protected $signature = 'hadayak:copy-sqlite {--fresh : امسح داتا MySQL الحالية قبل النقل}';

    protected $description = 'نقل داتا هداياك من SQLite إلى MySQL';

    /** الجداول بالترتيب الصحيح عشان الـ foreign keys */
    private const TABLES = [
        'users',
        'categories',
        'products',
        'product_images',
        'wrap_options',
        'card_designs',
        'services',
        'addresses',
        'orders',
        'order_items',
        'settings',
        'personal_access_tokens',
        'visitor_events',
    ];

    public function handle(): int
    {
        // وجّه اتصال sqlite لملف الداتابيز القديم مهما كان .env بيقول إيه
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);

        if (! file_exists(database_path('database.sqlite'))) {
            $this->error('ملف database/database.sqlite مش موجود!');

            return self::FAILURE;
        }

        $source = DB::connection('sqlite');
        $target = DB::connection('mysql');

        $target->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach (self::TABLES as $table) {
            if (! $source->getSchemaBuilder()->hasTable($table)) {
                $this->warn("⏭️  {$table}: مش موجود في SQLite — اتخطى");

                continue;
            }

            if ($this->option('fresh')) {
                $target->table($table)->truncate();
            }

            if ($target->table($table)->exists()) {
                $this->warn("⏭️  {$table}: فيه داتا في MySQL بالفعل — اتخطى (استخدم --fresh لو عايز تستبدلها)");

                continue;
            }

            $count = 0;

            $source->table($table)->orderBy('id')->chunk(200, function ($rows) use ($target, $table, &$count) {
                $target->table($table)->insert(
                    $rows->map(fn ($row) => (array) $row)->all()
                );
                $count += $rows->count();
            });

            $this->info("✅ {$table}: اتنقل {$count} صف");
        }

        $target->statement('SET FOREIGN_KEY_CHECKS=1');

        $this->newLine();
        $this->info('🎉 تمام! كل الداتا بقت في MySQL. ملف SQLite القديم لسه موجود كباك اب.');

        return self::SUCCESS;
    }
}
