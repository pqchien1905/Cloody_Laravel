<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Documents',
                'slug' => 'documents',
                'icon' => 'ri-file-text-line',
                'color' => '#667eea',
                'description' => 'Word documents, PDFs, text files, and other document formats',
                'extensions' => ['doc', 'docx', 'pdf', 'txt', 'rtf', 'odt'],
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'Images',
                'slug' => 'images',
                'icon' => 'ri-image-line',
                'color' => '#f093fb',
                'description' => 'Photos, graphics, and image files',
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'Videos',
                'slug' => 'videos',
                'icon' => 'ri-video-line',
                'color' => '#fa709a',
                'description' => 'Video files and multimedia content',
                'extensions' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
                'is_active' => true,
                'order' => 3,
            ],
            [
                'name' => 'Audio',
                'slug' => 'audio',
                'icon' => 'ri-music-line',
                'color' => '#4facfe',
                'description' => 'Music, podcasts, and audio files',
                'extensions' => ['mp3', 'wav', 'flac', 'm4a', 'aac', 'ogg'],
                'is_active' => true,
                'order' => 4,
            ],
            [
                'name' => 'Archives',
                'slug' => 'archives',
                'icon' => 'ri-folder-zip-line',
                'color' => '#fbc02d',
                'description' => 'Compressed files and archives',
                'extensions' => ['zip', 'rar', '7z', 'tar', 'gz'],
                'is_active' => true,
                'order' => 5,
            ],
            [
                'name' => 'Spreadsheets',
                'slug' => 'spreadsheets',
                'icon' => 'ri-file-excel-line',
                'color' => '#43a047',
                'description' => 'Excel files and spreadsheet documents',
                'extensions' => ['xls', 'xlsx', 'csv', 'ods'],
                'is_active' => true,
                'order' => 6,
            ],
            [
                'name' => 'Presentations',
                'slug' => 'presentations',
                'icon' => 'ri-slideshow-line',
                'color' => '#ff6b6b',
                'description' => 'PowerPoint and presentation files',
                'extensions' => ['ppt', 'pptx', 'odp'],
                'is_active' => true,
                'order' => 7,
            ],
            [
                'name' => 'Code',
                'slug' => 'code',
                'icon' => 'ri-code-line',
                'color' => '#5c6bc0',
                'description' => 'Source code and programming files',
                'extensions' => ['php', 'js', 'html', 'css', 'py', 'java', 'cpp', 'c', 'json', 'xml'],
                'is_active' => true,
                'order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
