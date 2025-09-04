<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Product::class;
    }

    public function getHomepageProducts($limit = 30)
    {
        return $this->_model::with(['medias', 'variants', 'shop'])
            ->where('status', 1)
            ->whereHas('shop', function ($q) {
                $q->where('status', 1);
            })
            ->withSum('variants as total_sell', 'sell')
            ->withMin('variants as min_price', 'price')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }


    public function getFilteredProducts(array $filters = [], array $keywords = [], $perPage = 10)
    {
        // Subquery: tính trung bình sao cho từng sản phẩm
        $avgRateSub = DB::table(table: 'tbl_rates')
            ->join('tbl_order_details', 'tbl_rates.order_detail_id', '=', 'tbl_order_details.id')
            ->join('tbl_product_variants', 'tbl_order_details.variant_id', '=', 'tbl_product_variants.id')
            ->select('tbl_product_variants.product_id', DB::raw('AVG(tbl_rates.rate) as avg_rate'))
            ->groupBy('tbl_product_variants.product_id');
        // Query chính
        $query = $this->_model::query()
            ->leftJoinSub($avgRateSub, 'avg_rates', function ($join) {
                $join->on('tbl_products.id', '=', 'avg_rates.product_id');
            })
            ->select('tbl_products.*', 'avg_rates.avg_rate')
            ->with(['medias', 'variants', 'shop'])
            ->whereHas('shop', function ($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->withSum('variants as total_sell', 'sell')
            ->withMin('variants as min_price', 'price');

        // Tìm kiếm theo từ khóa
        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere(function ($sub) use ($keyword) {
                            $words = explode(' ', $keyword);
                            foreach ($words as $word) {
                                $sub->where('name', 'LIKE', '%' . $word . '%');
                            }
                        });
                }
            });
        }


        // Lọc theo danh mục
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        } elseif (!empty($filters['category_parent_id'])) {
            $childCategoryIds = Category::where('parent_id', $filters['category_parent_id'])->pluck('id')->toArray();
            $query->whereIn('category_id', $childCategoryIds);
        }

        // Lọc theo shop
        if (!empty($filters['shop_id'])) {
            $query->where('shop_id', $filters['shop_id']);
        }

        // Lọc theo khoảng giá
        if (!empty($filters['min_price'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->where('price', '>=', $filters['min_price']);
            });
        }

        if (!empty($filters['max_price'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                $q->where('price', '<=', $filters['max_price']);
            });
        }

        //Lọc theo số sao đánh giá
        if (!empty($filters['min_rate'])) {
            $query->having('avg_rate', '>=', $filters['min_rate']);
        }

        //Lọc theo tỉnh thành
        if (!empty($filters['province_id'])) {
            $allDistrictIds = collect();

            foreach ($filters['province_id'] as $provinceId) {
                $districts = app(\App\Services\LocationService::class)->getDistricts((int) $provinceId);
                if ($districts) {
                    $allDistrictIds = $allDistrictIds->merge($districts->pluck('DistrictID'));
                }
            }

            if ($allDistrictIds->isNotEmpty()) {
                $query->whereHas('shop', function ($q) use ($allDistrictIds) {
                    $q->whereIn('district_id', $allDistrictIds->unique()->toArray());
                });
            }
        }


        // Sắp xếp
        if (!empty($filters['sort_by'])) {
            switch ($filters['sort_by']) {
                case 'price_asc':
                    $query->orderBy('min_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('min_price', 'desc');
                    break;
                case 'latest':
                    $query->orderBy('tbl_products.created_at', 'desc');
                    break;
                case 'bestseller':
                    $query->orderBy('total_sell', 'desc');
                    break;
                case 'rate_desc':
                    $query->orderBy('avg_rate', 'desc');
                    break;
            }
        }

        return $query->paginate($perPage);
    }

    public function getUsedDistrictIds($categoryId = null)
    {
        $categoryIds = null;

        if (is_array($categoryId)) {
            $categoryIds = $categoryId; // đã là array
        } elseif ($categoryId) {
            $category = Category::with('children')->find($categoryId);
            if ($category) {
                $categoryIds = $this->getCategoryIdsInTree($category);
            }
        }

        return $this->_model::query()
            ->join('tbl_shops', 'tbl_shops.id', '=', 'tbl_products.shop_id')
            ->when($categoryIds, function ($query) use ($categoryIds) {
                $query->whereIn('tbl_products.category_id', $categoryIds);
            })
            ->whereNotNull('tbl_shops.district_id')
            ->distinct()
            ->pluck('tbl_shops.district_id');
    }


    private function getCategoryIdsInTree($category)
    {
        $ids = [$category->id];
        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->getCategoryIdsInTree($child));
        }
        return $ids;
    }


    public function findDetailById($id)
    {
        return $this->_model::with([
            'variants.variantAttribute.attributeValue.attribute',
            'medias',
            'category.tax',
            'shop',
        ])
        ->where('status', 1)
        ->whereHas('shop', function ($q) {
            $q->where('status', 1);
        })
        ->find($id);
    }
    public function getFavoriteProducts($user_id, $perPage = 20)
    {
        return $this->_model::whereHas('favorites', function ($q) use ($user_id) {
            $q->where('user_id', $user_id);
        })
            ->with(['medias', 'variants', 'shop', 'category',])
            ->withSum('variants as total_sell', 'sell')
            ->withMin('variants as min_price', 'price')
            ->paginate($perPage);
    }

    public function getTopViewedProductsByVisit($days = 7, $limit = 10)
    {
        $fromDate = now()->subDays($days);

        return $this->_model::select('tbl_products.id', 'tbl_products.name', 'tbl_products.category_id', DB::raw('COUNT(tbl_visits.id) as view_count'))
            ->join('tbl_visits', 'tbl_products.id', '=', 'tbl_visits.product_id')
            ->join('tbl_shops', 'tbl_products.shop_id', '=', 'tbl_shops.id')
            ->where('tbl_visits.visited_at', '>=', $fromDate)
            ->where('tbl_products.status', 1)
            ->where('tbl_shops.status', 1)
            ->groupBy('tbl_products.id', 'tbl_products.name', 'tbl_products.category_id')
            ->orderByDesc('view_count')
            ->with(['medias'])
            ->take($limit)
            ->get();
    }


    public function getRelatedProductsByCategories(array $categoryIds, array $excludeProductIds = [], $limit = 10)
    {
        $query = $this->_model::with(['medias', 'variants', 'shop'])
            ->whereHas('shop', function ($q) {
                $q->where('status', 1);
            })
            ->where('status', 1)
            ->whereIn('category_id', $categoryIds)
            ->withMin('variants as min_price', 'price');
        if (!empty($excludeProductIds)) {
            $query->whereNotIn('id', $excludeProductIds);
        }

        return $query->inRandomOrder()->take($limit)->get();
    }
}
