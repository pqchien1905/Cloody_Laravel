<?php

use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Chuyển hướng gốc tới trang đăng nhập hoặc dashboard
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('cloody.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('cloody.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Avatar serving routes (public access for images)
use App\Http\Controllers\User\AvatarController;
Route::get('/avatars/user/{id}', [AvatarController::class, 'user'])->name('avatar.user');
Route::get('/avatars/group/{id}', [AvatarController::class, 'group'])->name('avatar.group');

// Các route Cloody - User Controllers
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\FileController;
use App\Http\Controllers\User\FileUploadController;
use App\Http\Controllers\User\FolderController;
use App\Http\Controllers\User\FileShareController;
use App\Http\Controllers\User\FolderShareController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\GroupController;
use App\Http\Controllers\User\LocaleController;
use App\Http\Controllers\User\StoragePlansController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Controllers\User\AIChatController;

// Các route Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminCategoriesController;
use App\Http\Controllers\Admin\AdminFilesController;
use App\Http\Controllers\Admin\AdminFoldersController;
use App\Http\Controllers\Admin\AdminGroupsController;
use App\Http\Controllers\Admin\AdminSharesController;
use App\Http\Controllers\Admin\AdminReportsController;
use App\Http\Controllers\Admin\AdminFavoritesController;
use App\Http\Controllers\Admin\AdminStoragePlansController;

// AI Chat route (yêu cầu đăng nhập)
Route::middleware(['auth'])->post('/ai-chat', [AIChatController::class, 'chat'])->name('ai.chat');

// Các route Cloody - Yêu cầu đăng nhập
Route::middleware(['auth'])->prefix('cloody')->group(function () {
    // Dashboard & Tệp
    Route::get('/', [DashboardController::class, 'index'])->name('cloody.dashboard');
    Route::get('/files', [FileController::class, 'index'])->name('cloody.files');
    Route::get('/shared', [FileController::class, 'shared'])->name('cloody.shared');
    Route::get('/recent', [FileController::class, 'recent'])->name('cloody.recent');
    Route::get('/favorites', [FileController::class, 'favorites'])->name('cloody.favorites');
    Route::get('/trash', [FileController::class, 'trash'])->name('cloody.trash');
    Route::post('/trash/cleanup', [FileController::class, 'cleanupTrash'])->name('cloody.trash.cleanup');
    // Hành động hàng loạt cho Thùng rác
    Route::post('/trash/folders/bulk-restore', [FolderController::class, 'bulkRestore'])->name('cloody.trash.folders.bulk-restore');
    Route::post('/trash/folders/bulk-force-delete', [FolderController::class, 'bulkForceDelete'])->name('cloody.trash.folders.bulk-force-delete');
    Route::post('/trash/files/bulk-restore', [FileUploadController::class, 'bulkRestore'])->name('cloody.trash.files.bulk-restore');
    Route::post('/trash/files/bulk-force-delete', [FileUploadController::class, 'bulkForceDelete'])->name('cloody.trash.files.bulk-force-delete');

    // Tải lên & Quản lý tệp
    Route::post('/files/upload', [FileUploadController::class, 'store'])
        ->middleware('rate.limit.upload')
        ->name('cloody.files.upload');
    Route::post('/files/check-duplicates', [FileUploadController::class, 'checkDuplicates'])->name('cloody.files.check-duplicates');
    Route::post('/files/bulk-delete', [FileUploadController::class, 'bulkDelete'])->name('cloody.files.bulk-delete');
    Route::get('/files/{id}/view', [FileUploadController::class, 'view'])->name('cloody.files.view');
    Route::get('/files/{id}/serve', [FileUploadController::class, 'serve'])->name('cloody.files.serve');
    Route::get('/files/{id}/download', [FileUploadController::class, 'download'])->name('cloody.files.download');
    Route::put('/files/{id}', [FileUploadController::class, 'update'])->name('cloody.files.update');
    Route::delete('/files/{id}', [FileUploadController::class, 'destroy'])->name('cloody.files.delete');
    Route::post('/files/{id}/restore', [FileUploadController::class, 'restore'])->name('cloody.files.restore');
    Route::delete('/files/{id}/force', [FileUploadController::class, 'forceDelete'])->name('cloody.files.force-delete');
    Route::post('/files/{id}/favorite', [FileUploadController::class, 'toggleFavorite'])->name('cloody.files.favorite');

    // Thư mục
    Route::get('/folders', [FolderController::class, 'index'])->name('cloody.folders.index');
    Route::post('/folders/upload', [FolderController::class, 'uploadFolder'])
        ->middleware('rate.limit.upload')
        ->name('cloody.folders.upload');
    Route::post('/folders/check-duplicates', [FolderController::class, 'checkDuplicateFolders'])->name('cloody.folders.check-duplicates');
    Route::post('/folders/bulk-delete', [FolderController::class, 'bulkDelete'])->name('cloody.folders.bulk-delete');
    Route::get('/folders/{id}', [FolderController::class, 'show'])->name('cloody.folders.show');
    Route::get('/folders/{id}/download', [FolderController::class, 'download'])->name('cloody.folders.download');
    Route::get('/folders/{id}/files', [FolderController::class, 'getFiles'])->name('cloody.folders.files');
    Route::get('/folders/{id}/edit', [FolderController::class, 'edit'])->name('cloody.folders.edit');
    Route::post('/folders', [FolderController::class, 'store'])->name('cloody.folders.store');
    Route::put('/folders/{id}', [FolderController::class, 'update'])->name('cloody.folders.update');
    Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('cloody.folders.destroy');
    Route::post('/folders/{id}/restore', [FolderController::class, 'restore'])->name('cloody.folders.restore');
    Route::delete('/folders/{id}/force', [FolderController::class, 'forceDelete'])->name('cloody.folders.force-delete');
    Route::post('/folders/{id}/favorite', [FolderController::class, 'toggleFavorite'])->name('cloody.folders.favorite');
    
    // Chia sẻ tệp
    Route::post('/files/{id}/share', [FileShareController::class, 'store'])->name('cloody.files.share');
    Route::get('/files/{id}/shares', [FileShareController::class, 'listShares'])->name('cloody.files.shares.list');
    Route::get('/shares/all', [FileShareController::class, 'getAllShares'])->name('cloody.shares.all');
    Route::delete('/shares/{id}', [FileShareController::class, 'destroy'])->name('cloody.shares.revoke');
    
    // Chia sẻ thư mục
    Route::post('/folders/{id}/share', [FolderShareController::class, 'store'])->name('cloody.folders.share');
    Route::get('/folders/{id}/shares', [FolderShareController::class, 'listShares'])->name('cloody.folders.shares.list');
    Route::get('/user/profile', [UserProfileController::class, 'index'])->name('cloody.user.profile');
    Route::get('/users', function () { return view('pages.user.list'); })->name('cloody.user.list');
    Route::get('/users/add', function () { return view('pages.user.add'); })->name('cloody.user.add');

    // Nhóm (Groups)
    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/discover', [GroupController::class, 'discover'])->name('groups.discover');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    
    // Quản lý thành viên nhóm
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->name('groups.members.add');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('groups.members.remove');
    Route::patch('/groups/{group}/members/{user}/role', [GroupController::class, 'updateMemberRole'])->name('groups.members.update-role');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::post('/groups/{group}/join', [GroupController::class, 'requestJoin'])->name('groups.request-join');
    
    // Files & Folders của nhóm
    Route::get('/groups/{group}/files', [GroupController::class, 'files'])->name('groups.files');
    Route::post('/groups/{group}/files/share-file', [GroupController::class, 'shareFile'])->name('groups.files.share-file');
    Route::post('/groups/{group}/files/share-folder', [GroupController::class, 'shareFolder'])->name('groups.files.share-folder');
    Route::delete('/groups/{group}/files/{file}', [GroupController::class, 'removeFile'])->name('groups.files.remove-file');
    Route::delete('/groups/{group}/folders/{folder}', [GroupController::class, 'removeFolder'])->name('groups.files.remove-folder');

    // Ngôn ngữ (Language switch)
    Route::post('/locale/switch', [LocaleController::class, 'switch'])->name('locale.switch');
    
    // Gói lưu trữ (Storage Plans)
    Route::get('/storage/plans', [StoragePlansController::class, 'index'])->name('cloody.storage.plans');
    Route::post('/storage/upgrade', [StoragePlansController::class, 'upgrade'])->name('cloody.storage.upgrade');
    
    // Thanh toán (Payment) - Create yêu cầu đăng nhập
    Route::post('/payment/create', [PaymentController::class, 'create'])->name('cloody.payment.create');
});

// Payment callback, return và IPN - KHÔNG yêu cầu đăng nhập (VNPay gọi từ bên ngoài)
Route::prefix('cloody')->group(function () {
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('cloody.payment.callback');
    Route::get('/payment/return', [PaymentController::class, 'return'])->name('cloody.payment.return');
    Route::post('/payment/ipn', [PaymentController::class, 'ipn'])->name('cloody.payment.ipn');
});

// Khu vực quản trị (Admin) - sử dụng cùng giao diện, yêu cầu đăng nhập + quyền admin
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    // /admin → Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Quản lý người dùng trong admin
    Route::resource('users', AdminUsersController::class)->except(['show']);
    
    // Quản lý danh mục file trong admin
    Route::resource('categories', AdminCategoriesController::class)->except(['show', 'create', 'edit']);
    
    // Quản lý Files
    Route::get('/files', [AdminFilesController::class, 'index'])->name('files.index');
    Route::get('/files/{file}', [AdminFilesController::class, 'show'])->name('files.show');
    Route::get('/files/{file}/view', [AdminFilesController::class, 'view'])->name('files.view');
    Route::get('/files/{file}/serve', [AdminFilesController::class, 'serve'])->name('files.serve');
    Route::get('/files/{file}/download', [AdminFilesController::class, 'download'])->name('files.download');
    Route::delete('/files/{file}', [AdminFilesController::class, 'destroy'])->name('files.destroy');
    
    // Quản lý Folders
    Route::get('/folders', [AdminFoldersController::class, 'index'])->name('folders.index');
    Route::get('/folders/{folder}', [AdminFoldersController::class, 'show'])->name('folders.show');
    Route::get('/folders/{folder}/view', [AdminFoldersController::class, 'view'])->name('folders.view');
    Route::get('/folders/{folder}/download', [AdminFoldersController::class, 'download'])->name('folders.download');
    Route::delete('/folders/{folder}', [AdminFoldersController::class, 'destroy'])->name('folders.destroy');
    
    // Quản lý Groups
    Route::get('/groups', [AdminGroupsController::class, 'index'])->name('groups.index');
    Route::get('/groups/{group}', [AdminGroupsController::class, 'show'])->name('groups.show');
    Route::get('/groups/{group}/view', [AdminGroupsController::class, 'view'])->name('groups.view');
    Route::delete('/groups/{group}', [AdminGroupsController::class, 'destroy'])->name('groups.destroy');
    
    // Quản lý Shares
    Route::get('/shares', [AdminSharesController::class, 'index'])->name('shares.index');
    Route::delete('/shares/{id}', [AdminSharesController::class, 'destroy'])->name('shares.destroy');
    
    // Quản lý Favorites
    Route::get('/favorites', [AdminFavoritesController::class, 'index'])->name('favorites.index');
    Route::delete('/favorites/files/{file}', [AdminFavoritesController::class, 'unfavoriteFile'])->name('favorites.unfavorite-file');
    Route::delete('/favorites/folders/{folder}', [AdminFavoritesController::class, 'unfavoriteFolder'])->name('favorites.unfavorite-folder');
    Route::post('/favorites/bulk-unfavorite', [AdminFavoritesController::class, 'bulkUnfavorite'])->name('favorites.bulk-unfavorite');
    
    // (Đã gỡ bỏ) Quản lý Trash
    // Route::get('/trash', [AdminTrashController::class, 'index'])->name('trash.index');
    // Route::post('/trash/{id}/restore', [AdminTrashController::class, 'restore'])->name('trash.restore');
    // Route::delete('/trash/{id}', [AdminTrashController::class, 'destroy'])->name('trash.destroy');
    // Route::delete('/trash', [AdminTrashController::class, 'empty'])->name('trash.empty');
    
    // Báo cáo & Thống kê
    Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [AdminReportsController::class, 'export'])->name('reports.export');
    
    // Quản lý gói lưu trữ
    Route::get('/storage-plans', [AdminStoragePlansController::class, 'index'])->name('storage-plans.index');
    Route::post('/storage-plans/store', [AdminStoragePlansController::class, 'store'])->name('storage-plans.store');
    Route::put('/storage-plans/{id}', [AdminStoragePlansController::class, 'update'])->name('storage-plans.update');
    Route::delete('/storage-plans/plan/{id}', [AdminStoragePlansController::class, 'destroy'])->name('storage-plans.destroy');
    Route::post('/storage-plans/{id}/toggle-active', [AdminStoragePlansController::class, 'toggleActive'])->name('storage-plans.toggle-active');
    Route::post('/storage-plans/subscription/{id}/deactivate', [AdminStoragePlansController::class, 'deactivateSubscription'])->name('storage-plans.deactivate');
    Route::post('/storage-plans/subscription/{id}/activate', [AdminStoragePlansController::class, 'activateSubscription'])->name('storage-plans.activate');
    Route::delete('/storage-plans/subscription/{id}', [AdminStoragePlansController::class, 'deleteSubscription'])->name('storage-plans.delete');
});

// Liên kết chia sẻ công khai (không yêu cầu đăng nhập)
// File shares
Route::get('/shared/file/{token}', [FileShareController::class, 'show'])->name('file.shared');
Route::get('/shared/file/{token}/view', [FileShareController::class, 'view'])->name('file.shared.view');
Route::get('/shared/file/{token}/download', [FileShareController::class, 'download'])->name('file.shared.download');

// Folder shares
Route::get('/shared/folder/{token}', [FolderShareController::class, 'show'])->name('folder.shared');
Route::get('/shared/folder/{token}/download', [FolderShareController::class, 'download'])->name('folder.shared.download');

require __DIR__.'/auth.php';
