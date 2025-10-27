<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\File;
use App\Models\User;

echo "=== Storage Check ===" . PHP_EOL;
echo "Total files in DB: " . File::count() . PHP_EOL . PHP_EOL;

$users = User::all();
foreach ($users as $user) {
    $fileCount = File::where('user_id', $user->id)->count();
    $totalSize = File::where('user_id', $user->id)->sum('size');
    $sizeGB = $totalSize / (1024 * 1024 * 1024);
    
    echo "User: {$user->name} (ID: {$user->id})" . PHP_EOL;
    echo "  Files: {$fileCount}" . PHP_EOL;
    echo "  Total Size: " . number_format($sizeGB, 4) . " GB" . PHP_EOL;
    echo "  Not Trash: " . File::where('user_id', $user->id)->where('is_trash', false)->count() . PHP_EOL . PHP_EOL;
}
