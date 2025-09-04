<?php

namespace App\Services;

use App\Models\AttributeValue;
use App\Repositories\AttributeValueEloquentRepository;
use Exception;

class AttributeValueService
{
    protected $attributeValueRepo;

    public function __construct(AttributeValueEloquentRepository $attributeValueRepo)
    {
        $this->attributeValueRepo = $attributeValueRepo;
    }

    public function getValuesByAttributeId($attributeId, $search = null, $perPage = 8)
    {
        $query = AttributeValue::where('attribute_id', $attributeId);
        if ($search) {
            $query->where('value', 'like', '%' . $search . '%');
        }
        $attributeValues = $query->paginate($perPage);

        return [
            'attributeValues' => $attributeValues->items(),
            'current_page' => $attributeValues->currentPage(),
            'per_page' => $attributeValues->perPage(),
            'last_page' => $attributeValues->lastPage(),
            'total' => $attributeValues->total(),
        ];
    }

    public function createValue($attributeId, $data)
    {
        try {
            $attributeValue = $this->attributeValueRepo->create([
                'attribute_id' => $attributeId,
                'value' => $data['value'],
            ]);
            return [
                'success' => true,
                'message' => 'Thêm giá trị thành công',
                'attributeValue' => $attributeValue
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Thêm giá trị thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateValue($id, $data)
    {
        try {
            $attributeValue = $this->attributeValueRepo->find($id);
            if (!$attributeValue) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy giá trị'
                ];
            }
            $attributeValue->update([
                'value' => $data['value'],
            ]);
            return [
                'success' => true,
                'message' => 'Cập nhật giá trị thành công',
                'attributeValue' => $attributeValue
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Cập nhật giá trị thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteValue($id)
    {
        try {
            $attributeValue = $this->attributeValueRepo->find($id);
            if (!$attributeValue) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy giá trị'
                ];
            }
            $attributeValue->delete();
            return [
                'success' => true,
                'message' => 'Xóa giá trị thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Xóa giá trị thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteMultiple($ids)
    {
        try {
            AttributeValue::destroy($ids);
            return [
                'success' => true,
                'message' => 'Xóa giá trị thành công'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Xóa giá trị thất bại',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getAttributesAndValuesByProduct($productId)
    {
        $values = AttributeValue::whereHas('variantAttributes.productVariant', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })
            ->with('attribute')
            ->get();

        $grouped = $values->groupBy('attribute.id')->map(function ($group) {
            return [
                'id' => $group->first()->attribute->id,
                'name' => $group->first()->attribute->name,
                'values' => $group->map(fn($item) => [
                    'id' => $item->id,
                    'value' => $item->value,
                ])->unique('id')->values()
            ];
        })->values();

        return [
            'attributes' => $grouped,
        ];
    }
}