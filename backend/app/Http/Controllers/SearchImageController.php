<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProductSearchImageRequest;
use App\Services\ProductSearchService;

class SearchImageController extends Controller
{
    protected $productSearchService;

    public function __construct(ProductSearchService $productSearchService)
    {
        $this->productSearchService = $productSearchService;
    }

    public function searchByImage(ProductSearchImageRequest $request)
    {
        try {
            $labels = $this->productSearchService->getLabelsFromImage($request);

            return response()->json($labels, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

}