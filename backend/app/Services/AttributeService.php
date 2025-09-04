<?php

namespace App\Services;

use App\Models\Attribute;
use App\Repositories\AttributeEloquentRepository;
use Exception;

class AttributeService
{
    protected $attributeRepo;

    public function __construct(AttributeEloquentRepository $attributeRepo)
    {
        $this->attributeRepo = $attributeRepo;
    }

    public function getAttributesWithValues($search = null, $perPage = 8)
    {
        $query = Attribute::with('attributeValues');
        if ($search) { 
            $query->where('name', 'like', '%' . $search . '%');
        }
        $attributes = $query->paginate($perPage);

        return [
            'attributes' => $attributes->items(),
            'current_page' => $attributes->currentPage(),
            'per_page' => $attributes->perPage(),
            'last_page' => $attributes->lastPage(),
            'total' => $attributes->total(),
        ];
    }

    public function get_attribute_with_value_shop($shop_id, $search = null, $perPage = 8)
    {
        $query = Attribute::with('attributeValues')->where('shop_id', $shop_id);
        if ($search) { 
            $query->where('name', 'like', '%' . $search . '%');
        }
        $attributes = $query->paginate($perPage);

        return [
            'attributes' => $attributes->items(),
            'current_page' => $attributes->currentPage(),
            'per_page' => $attributes->perPage(),
            'last_page' => $attributes->lastPage(),
            'total' => $attributes->total(),
        ];
    }

    public function getAttributeById($id)
    {
        $attribute = Attribute::with('attributeValues')->find($id);
        if (!$attribute) {
            throw new Exception('Không tìm thấy thuộc tính', 404);
        }
        return $attribute;
    }

    public function createAttribute($data)
    {
        try {
            $attribute = $this->attributeRepo->create($data);
            return [
                'success' => true,
                'message' => 'Thêm thuộc tính thành công',
                'attribute' => $attribute
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Thêm thuộc tính thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateAttribute($id, $data)
    {
        try {
            $attribute = $this->attributeRepo->find($id);
            if (!$attribute) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thuộc tính'
                ];
            }
            $attribute->update($data);
            return [
                'success' => true,
                'message' => 'Cập nhật thuộc tính thành công',
                'attribute' => $attribute
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Cập nhật thuộc tính thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteAttribute($id)
    {
        try {
            $attribute = $this->attributeRepo->find($id);
            if (!$attribute) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thuộc tính'
                ];
            }
            $attribute->delete();
            return [
                'success' => true,
                'message' => 'Xóa thuộc tính thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Xóa thuộc tính thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteMultiple($ids)
    {
        $deletedIds = [];
        $notDeletedNames = [];

        $attributes = Attribute::whereIn('id', $ids)->get();

        foreach ($attributes as $attribute) {
            // Nếu có logic kiểm tra ràng buộc, thêm tại đây
            $attribute->delete();
            $deletedIds[] = $attribute->id;
        }

        return [
            'success' => true,
            'deleted_ids' => $deletedIds,
            'not_deleted_names' => implode(', ', $notDeletedNames)
        ];
    }
}