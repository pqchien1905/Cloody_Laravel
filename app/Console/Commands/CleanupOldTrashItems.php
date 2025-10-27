<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupOldTrashItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trash:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete files and folders that have been in trash for more than 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting trash cleanup...');
        
        $thirtyDaysAgo = now()->subDays(30);
        
    // Xóa các file cũ
        $oldFiles = File::where('is_trash', true)
            ->where('trashed_at', '<=', $thirtyDaysAgo)
            ->get();
        
        $fileCount = 0;
        foreach ($oldFiles as $file) {
            // Xóa file vật lý khỏi storage
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            
            // Xóa bản ghi khỏi cơ sở dữ liệu
            $file->delete();
            $fileCount++;
        }
        
        // Xóa các thư mục cũ
        $oldFolders = Folder::where('is_trash', true)
            ->where('trashed_at', '<=', $thirtyDaysAgo)
            ->get();
        
        $folderCount = 0;
        foreach ($oldFolders as $folder) {
            $folder->delete();
            $folderCount++;
        }
        
        $this->info("Cleanup completed!");
        $this->info("Deleted {$fileCount} files and {$folderCount} folders.");
        
        return Command::SUCCESS;
    }
}
