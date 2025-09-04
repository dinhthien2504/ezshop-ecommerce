<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Services\AttributeService;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function getAttributesWithValues(Request $request)
    {
        $search = $request->input('search');
        $result = $this->attributeService->getAttributesWithValues($search);

        return response()->json($result);
    }

    public function get_attribute_with_value_shop(Request $request)
    {
        $search = $request->input('search');
        $shop_id = $request->input('shop_id');
        $result = $this->attributeService->get_attribute_with_value_shop($shop_id, $search);

        return response()->json($result);
    }
    
    public function getAttributeById($id)
    {
        try {
            $attribute = $this->attributeService->getAttributeById($id);
            return response()->json(['attribute' => $attribute]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function store(StoreAttributeRequest $request)
    {
        $result = $this->attributeService->createAttribute([
            'name' => $request->input('name')
        ]);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result, 201);
        }
        return response()->json($result, 500);
    }

    public function store_for_shop(StoreAttributeRequest $request)
    {
        $result = $this->attributeService->createAttribute([
            'shop_id' => $request->input('shop_id'),
            'name' => $request->input('name')
        ]);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result, 201);
        }
        return response()->json($result, 500);
    }

    public function update(UpdateAttributeRequest $request, string $id)
    {
        $result = $this->attributeService->updateAttribute($id, [
            'name' => $request->input('name')
        ]);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result);
        }
        $status = isset($result['error']) ? 500 : 404;
        return response()->json($result, $status);
    }

    public function destroy(string $id)
    {
        $result = $this->attributeService->deleteAttribute($id);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result);
        }
        $status = isset($result['error']) ? 500 : 404;
        return response()->json($result, $status);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');
        $result = $this->attributeService->deleteMultiple($ids);
        if (isset($result['success']) && $result['success']) {
            return response()->json($result);
        }
        return response()->json($result, 500);
    }
}