<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model Category - Quản lý danh mục file trong hệ thống
 */
class Category extends Model
{
    /**
     * Các thuộc tính có thể gán hàng loạt
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'extensions',
        'is_active',
        'order',
    ];

    /**
     * Các thuộc tính cần ép kiểu
     */
    protected $casts = [
        'extensions' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Phương thức boot để tự động tạo slug từ tên danh mục
     */
    protected static function boot()
    {
        parent::boot();

        // Tự động tạo slug khi tạo mới danh mục
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // Tự động cập nhật slug khi tên danh mục thay đổi
        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
