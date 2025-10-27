<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Chuyển hướng gốc tới trang đăng nhập hoặc dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('cloudbox.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('cloudbox.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Các route CloudBOX
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileShareController;
use App\Http\Controllers\FolderShareController;

// Các route CloudBOX - Yêu cầu đăng nhập
Route::middleware(['auth'])->prefix('cloudbox')->group(function () {
    // Dashboard & Tệp
    Route::get('/', [DashboardController::class, 'index'])->name('cloudbox.dashboard');
    Route::get('/files', [FileController::class, 'index'])->name('cloudbox.files');
    Route::get('/shared', [FileController::class, 'shared'])->name('cloudbox.shared');
    Route::get('/recent', [FileController::class, 'recent'])->name('cloudbox.recent');
    Route::get('/favorites', [FileController::class, 'favorites'])->name('cloudbox.favorites');
    Route::get('/trash', [FileController::class, 'trash'])->name('cloudbox.trash');
    Route::post('/trash/cleanup', [FileController::class, 'cleanupTrash'])->name('cloudbox.trash.cleanup');
    // Hành động hàng loạt cho Thùng rác
    Route::post('/trash/folders/bulk-restore', [FolderController::class, 'bulkRestore'])->name('cloudbox.trash.folders.bulk-restore');
    Route::post('/trash/folders/bulk-force-delete', [FolderController::class, 'bulkForceDelete'])->name('cloudbox.trash.folders.bulk-force-delete');
    Route::post('/trash/files/bulk-restore', [FileUploadController::class, 'bulkRestore'])->name('cloudbox.trash.files.bulk-restore');
    Route::post('/trash/files/bulk-force-delete', [FileUploadController::class, 'bulkForceDelete'])->name('cloudbox.trash.files.bulk-force-delete');

    // Tải lên & Quản lý tệp
    Route::post('/files/upload', [FileUploadController::class, 'store'])->name('cloudbox.files.upload');
    Route::post('/files/check-duplicates', [FileUploadController::class, 'checkDuplicates'])->name('cloudbox.files.check-duplicates');
    Route::post('/files/bulk-delete', [FileUploadController::class, 'bulkDelete'])->name('cloudbox.files.bulk-delete');
    Route::get('/files/{id}/view', [FileUploadController::class, 'view'])->name('cloudbox.files.view');
    Route::get('/files/{id}/download', [FileUploadController::class, 'download'])->name('cloudbox.files.download');
    Route::put('/files/{id}', [FileUploadController::class, 'update'])->name('cloudbox.files.update');
    Route::delete('/files/{id}', [FileUploadController::class, 'destroy'])->name('cloudbox.files.delete');
    Route::post('/files/{id}/restore', [FileUploadController::class, 'restore'])->name('cloudbox.files.restore');
    Route::delete('/files/{id}/force', [FileUploadController::class, 'forceDelete'])->name('cloudbox.files.force-delete');
    Route::post('/files/{id}/favorite', [FileUploadController::class, 'toggleFavorite'])->name('cloudbox.files.favorite');

    // Thư mục
    Route::get('/folders', [FolderController::class, 'index'])->name('cloudbox.folders.index');
    Route::post('/folders/upload', [FolderController::class, 'uploadFolder'])->name('cloudbox.folders.upload');
    Route::post('/folders/check-duplicates', [FolderController::class, 'checkDuplicateFolders'])->name('cloudbox.folders.check-duplicates');
    Route::post('/folders/bulk-delete', [FolderController::class, 'bulkDelete'])->name('cloudbox.folders.bulk-delete');
    Route::get('/folders/{id}', [FolderController::class, 'show'])->name('cloudbox.folders.show');
    Route::get('/folders/{id}/edit', [FolderController::class, 'edit'])->name('cloudbox.folders.edit');
    Route::post('/folders', [FolderController::class, 'store'])->name('cloudbox.folders.store');
    Route::put('/folders/{id}', [FolderController::class, 'update'])->name('cloudbox.folders.update');
    Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('cloudbox.folders.destroy');
    Route::post('/folders/{id}/restore', [FolderController::class, 'restore'])->name('cloudbox.folders.restore');
    Route::delete('/folders/{id}/force', [FolderController::class, 'forceDelete'])->name('cloudbox.folders.force-delete');
    Route::post('/folders/{id}/favorite', [FolderController::class, 'toggleFavorite'])->name('cloudbox.folders.favorite');
    // Chia sẻ thư mục
    Route::post('/folders/{id}/share', [FolderShareController::class, 'store'])->name('cloudbox.folders.share');

    // Chia sẻ tệp
    Route::post('/files/{id}/share', [FileShareController::class, 'store'])->name('cloudbox.files.share');
    Route::get('/files/{id}/shares', [FileShareController::class, 'listShares'])->name('cloudbox.files.shares.list');
    Route::delete('/shares/{id}', [FileShareController::class, 'destroy'])->name('cloudbox.shares.revoke');
    
    // Trang người dùng
    Route::get('/user/profile', function () { return view('pages.user.profile'); })->name('cloudbox.user.profile');
    Route::get('/users', function () { return view('pages.user.list'); })->name('cloudbox.user.list');
    Route::get('/users/add', function () { return view('pages.user.add'); })->name('cloudbox.user.add');
});

// Liên kết chia sẻ công khai (không yêu cầu đăng nhập)
Route::get('/shared/{token}', [FileShareController::class, 'show'])->name('file.shared');
Route::get('/shared/{token}/download', [FileShareController::class, 'download'])->name('file.shared.download');

require __DIR__.'/auth.php';
