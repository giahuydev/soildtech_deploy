<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;           // ← DÒNG NÀY BẮT BUỘC PHẢI CÓ
use Illuminate\Database\Seeder;

class CategoriesAndBrandsSeeder extends Seeder
{
    /**
     * Tạo danh mục và thương hiệu mẫu
     */
    public function run(): void
    {
        $this->command->info('Đang tạo Danh mục & Thương hiệu...');

        // === DANH MỤC ===
        Category::updateOrCreate(
            ['slug' => 'giay-nam'],
            [
                'name'        => 'Giày Nam',
                'description' => 'Thời trang phái mạnh',
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'giay-nu'],
            [
                'name'        => 'Giày Nữ',
                'description' => 'Duyên dáng & Cá tính',
                'is_active'   => true,
            ]
        );

        Category::updateOrCreate(
            ['slug' => 'phu-kien'],
            [
                'name'        => 'Phụ Kiện',
                'description' => 'Vớ, dây giày, vệ sinh giày',
                'is_active'   => true,
            ]
        );

        // === THƯƠNG HIỆU ===
        $brands = ['Nike', 'Adidas', 'Puma', 'Vans', 'Crep Protect'];

        foreach ($brands as $brand) {
            $slug = Str::slug($brand);   // ← Giờ không còn lỗi nữa
            Brand::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'      => $brand,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Tạo Danh mục & Thương hiệu thành công!');
    }
}