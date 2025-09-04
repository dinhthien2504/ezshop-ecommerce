<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAttributeValueRequest;
use App\Http\Requests\UpdateAttributeValueRequest;
use App\Services\AttributeValueService;

class AttributeValueController extends Controller
{
    protected $attributeValueService;

    public function __construct(AttributeValueService $attributeValueService)
    {
        $this->attributeValueService = $attributeValueService;
    }

    public function getValuesByAttributeId(Request $request, $attributeId)
    {
        $search = $request->input('search');
        $result = $this->attributeValueService->getValuesByAttributeId($attributeId, $search);
        return response()->json($result);
    }

    public function store(StoreAttributeValueRequest $request, $attributeId)
    {
        $result = $this->attributeValueService->createValue($attributeId, [
            'value' => $request->input('value')
        ]);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result, 201);
        }
        return response()->json($result, 500);
    }

    public function update(UpdateAttributeValueRequest $request, $id)
    {
        $result = $this->attributeValueService->updateValue($id, [
            'value' => $request->input('value')
        ]);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result);
        }
        $status = isset($result['error']) ? 500 : 404;
        return response()->json($result, $status);
    }

    public function destroy($id)
    {
        $result = $this->attributeValueService->deleteValue($id);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result);
        }
        $status = isset($result['error']) ? 500 : 404;
        return response()->json($result, $status);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');
        $result = $this->attributeValueService->deleteMultiple($ids);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result);
        }
        return response()->json($result, 500);
    }

    public function getAttributesAndValuesByProduct($productId)
    {
        $result = $this->attributeValueService->getAttributesAndValuesByProduct($productId);
        return response()->json($result);
    }
}
