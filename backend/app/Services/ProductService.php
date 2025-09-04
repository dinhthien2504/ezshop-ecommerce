<?php

namespace App\Services;

use App\Repositories\ProductEloquentRepository;
use App\Repositories\VisitEloquentRepository;

class ProductService
{
    protected $pro_rep;
    protected $locationService;
    protected $visitRepository;

    public function __construct(
        ProductEloquentRepository $pro_rep,
        LocationService $locationService,
        VisitEloquentRepository $visitRepository
    ) {
        $this->pro_rep = $pro_rep;
        $this->locationService = $locationService;
        $this->visitRepository = $visitRepository;
    }

    public function getHomepageProducts($resquest)
    {
        $limit = 30;
        if ($resquest->has('limit')) {
            $limit = $resquest->input('limit');
        }
        $products = $this->pro_rep->getHomepageProducts($limit);

        $products = $products->map(function ($product) {
            $product->avg_rate = $product->getAverageRatingAttribute();
            return $this->formatProduct($product);
        });
        return $products;
    }

    public function getTopViewedProducts($request)
    {
        $days = $request->input('days', 7);
        $limit = $request->input('limit', 10);

        $products = $this->pro_rep->getTopViewedProductsByVisit($days, $limit);

        $products->transform(function ($product) {
            $product->main_image = optional($product->medias->firstWhere('is_main', true))->url;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'main_image' => $product->main_image,
                'view_count' => $product->view_count ?? 0,
            ];
        });

        return $products;
    }

    public function getFilteredProducts($request)
    {
        $filters = [];
        if ($request->has('shop_id')) {
            $filters['shop_id'] = $request->input('shop_id');
        }
        if ($request->has('category_id')) {
            $filters['category_id'] = $request->input('category_id');
        }
        if ($request->has('category_parent_id')) {
            $filters['category_parent_id'] = $request->input('category_parent_id');
        }

        if ($request->has('min_price')) {
            $filters['min_price'] = $request->input('min_price');
        }

        if ($request->has('max_price')) {
            $filters['max_price'] = $request->input('max_price');
        }

        if ($request->has('sort_by')) {
            $filters['sort_by'] = $request->input('sort_by');
        }

        if ($request->has('sort_by')) {
            $filters['sort_by'] = $request->input('sort_by');
        }

        if ($request->has('min_rate')) {
            $filters['min_rate'] = $request->input('min_rate');
        }

        $provinceIds = $request->input('province_id');
        if (!empty($provinceIds)) {
            $filters['province_id'] = $provinceIds;
        }
        $page_per = 10;
        if ($request->has('page_per')) {
            $page_per = $request->input('page_per');
        }

        $keywords = [];
        if ($request->has('keywords')) {
            $keywords = $request->input('keywords');
        }

        $products = $this->pro_rep->getFilteredProducts($filters, $keywords, $page_per);

        $products->getCollection()->transform(function ($product) {
            return $this->formatProduct($product);
        });

        return $products;
    }

    private function formatProduct($product)
    {
        $provinceName = $this->locationService->getProvinceNameByDistrictId($product->shop->district_id);
        $product->avg_rate = number_format($product->avg_rate ?? 0, 1);
        $product->province_name = $provinceName;
        $product->main_image = $product->medias->firstWhere('is_main', true)->url;
        return $product;
    }

    public function getAvailableProvinces($request)
    {
        $category_id = $request->input('category_id') ?? null;
        $districtIds = $this->pro_rep->getUsedDistrictIds($category_id)->toArray();
        $provinceMap = $this->locationService->mapDistrictsToProvinces($districtIds);

        return collect($provinceMap)->map(function ($name, $id) {
            return [
                'ProvinceID' => $id,
                'ProvinceName' => $name,
            ];
        })->values();
    }

    public function formatProductDetail($id)
    {
        $product = $this->pro_rep->findDetailById($id);
        if (!$product) {
            throw new \Exception("Sản phẩm không tồn tại.", 404);
        }
        //Ghi nhận lại lượt truy cập
        $this->visitRepository->recordVisit($product->shop_id, 'product', $id);

        // Tính giá có VAT
        $product->min_price = $product->variants->min('price');
        $product->max_price = $product->variants->max('price');
        // Sell
        $product->total_sell = $product->variants->sum('sell');
        // Medias
        $medias = $product->medias->toArray();
        foreach ($product->variants as $variant) {
            if ($variant->image) {
                $medias[] = [
                    'url' => $variant->image,
                    'is_main' => 0,
                    'type' => 'image',
                ];
            }
        }

        // Attributes
        $attributes = [];
        foreach ($product->variants as $variant) {
            foreach ($variant->variantAttribute as $variantAttribute) {
                $attr = $variantAttribute->attributeValue->attribute;
                if (!isset($attributes[$attr->id])) {
                    $attributes[$attr->id] = [
                        'id' => $attr->id,
                        'name' => $attr->name,
                        'values' => [],
                    ];
                }
                $attributes[$attr->id]['values'][] = $variantAttribute->attributeValue;
            }
        }

        // Unique values
        foreach ($attributes as $key => $attr) {
            $attributes[$key]['values'] = array_unique($attr['values'], SORT_REGULAR);
        }

        // Total stock
        $totalStock = $product->variants->sum('stock');
        return [
            'product' => $product,
            'attributes' => $attributes,
            'medias' => $medias,
            'total_stock' => $totalStock,
        ];
    }

    public static function getVatIncludedPrice($variant)
    {
        $vatPercent = 0;

        if (
            $variant->product &&
            $variant->product->category &&
            $variant->product->category->tax &&
            isset($variant->product->category->tax->vat_percent)
        ) {
            $vatPercent = $variant->product->category->tax->vat_percent;
        }

        return round($variant->price * (1 + $vatPercent / 100));
    }

    public function getAllProductsWishlist($userId, $perPage = 20)
    {
        $products = $this->pro_rep->getFavoriteProducts($userId, $perPage);

        // Format lại từng sản phẩm để có main_image, slug, ...
        $products->getCollection()->transform(function ($product) {
            return $this->formatProduct($product);
        });

        return $products;
    }

    public function getRelatedProducts($request)
    {
        $limit = 10;
        $categoryIds = $request->input('category_ids');
        $excludeProductId = $request->input('product_ids');
        $products = $this->pro_rep->getRelatedProductsByCategories($categoryIds, $excludeProductId, $limit);

        return $products->map(function ($product) {
            $product->total_sell = $product->variants->sum('sell');
            return $this->formatProduct($product);
        });
    }
}