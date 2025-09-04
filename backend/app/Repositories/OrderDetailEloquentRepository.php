<?php

namespace App\Repositories;

use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;

class OrderDetailEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return OrderDetail::class;
    }

    public function getOrderDetailsWithFeeAndTax(array $orderIds)
    {
        return DB::table('tbl_order_details AS od')
            ->join('tbl_product_variants AS v', 'v.id', '=', 'od.variant_id')
            ->join('tbl_products AS p', 'p.id', '=', 'v.product_id')
            ->join('tbl_categories AS c', 'c.id', '=', 'p.category_id')
            ->leftJoin('tbl_fees AS f', 'f.id', '=', 'c.fee_id')
            ->leftJoin('tbl_taxes AS t', 't.id', '=', 'c.tax_id')
            ->whereIn('od.order_id', $orderIds)
            ->select(
                'od.quantity',
                'v.price',
                'f.percentage AS platform_fee_percent',
                't.vat_percent',
                't.tax_percent'
            )
            ->get();
    }
}