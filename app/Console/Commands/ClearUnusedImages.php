<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class ClearUnusedImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:product-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет файлы изображений из директории product_images, которые не связаны с записями в базе данных.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Получить список всех файлов в директории product_images
        $files = Storage::disk('public')->files('product_images');
        $this->info("Всего файлов: " . count($files));

        // Получить список всех изображений, которые есть в базе данных
        $dbImages = DB::table('product_images')->pluck('image_url')->toArray();
        $this->info("Всего изображений в базе данных: " . count($dbImages));

        // Преобразовать пути файлов к относительным путям
        $dbImages = array_map(function($value) {
            return 'product_images/' . basename($value);
        }, $dbImages);

        // Вывести пути файлов для отладки
        $this->info("Пути файлов в базе данных:");
        foreach ($dbImages as $image) {
            $this->info($image);
        }

        $deletedFiles = 0;

        // Пройтись по всем файлам и удалить те, которых нет в базе данных
        foreach ($files as $file) {
            if (!in_array($file, $dbImages)) {
                Storage::disk('public')->delete($file);
                $deletedFiles++;
                $this->info("Удален файл: " . $file);
            }
        }

        $this->info("Удалено файлов: $deletedFiles");
    }

}
