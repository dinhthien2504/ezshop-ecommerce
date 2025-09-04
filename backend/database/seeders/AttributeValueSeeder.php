<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả attributes
        $attributes = DB::table('tbl_attributes')->get()->keyBy('name');
        
        $attributeValues = [
            'Kích thước' => ['S', 'M', 'L', 'XL', 'XXL'],
            'Màu sắc' => ['Đen', 'Trắng', 'Xanh', 'Đỏ', 'Hồng', 'Vàng', 'Nâu', 'Xám', 'Tím', 'Navy', 'Natural Titanium', 'Blue Titanium', 'Hoa', 'Tropical', 'Trong suốt', 'Bạc', 'Eclipse Gray', 'Space Gray', 'Silver', 'Platinum', 'Midnight', 'Starlight'],
            'Size' => ['30', '32', '34', '36', '38', '40', '41', '42', '43', '44', '45', '5'],
            'Chất liệu' => ['Cotton', 'Polyester', 'Da bò', 'Inox 304', 'Bạc 925', 'Nhôm', 'TPU'],
            'Dung lượng' => ['64GB', '128GB', '256GB', '512GB', '1TB'],
            'Dung tích' => ['30ml', '50ml', '200ml', '250ml', '1.8L'],
            'SPF' => ['15', '30', '50+'],
            'Trọng lượng' => ['5kg', '10kg'],
            'Hình dạng' => ['Tròn', 'Vuông', 'Chữ nhật'],
            'Switch' => ['Blue', 'Red', 'Brown'],
            'Motor' => ['2.5HP', '3HP'],
            'Số lượng' => ['1 quả', '4 quả', '6 quả'],
            'Thành phần' => ['Aloe', 'Cucumber', 'Green Tea', 'Vitamin C'],
            'Trọng lượng tối đa' => ['20kg', '50kg', '100kg'],
            'Kích thước laptop' => ['13 inch', '14 inch', '15.6 inch', '17 inch']
        ];

        foreach ($attributeValues as $attributeName => $values) {
            $attribute = $attributes[$attributeName] ?? null;
            if ($attribute) {
                foreach ($values as $value) {
                    DB::table('tbl_attribute_values')->insert([
                        'value' => $value,
                        'attribute_id' => $attribute->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
