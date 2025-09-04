<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\VariantAttribute;
use App\Services\NotificationService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductRejectedMail;

class ProductController extends Controller
{
    protected $pro_service;
    protected $notification_service;

    public function __construct(ProductService $pro_service, NotificationService $notification_service)
    {
        $this->pro_service = $pro_service;
        $this->notification_service = $notification_service;
    }

    public function index(Request $request)
    {
        try {
            $products = $this->pro_service->getFilteredProducts($request);
            return response()->json($products);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm.',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getAvailableProvinces(Request $request)
    {
        try {
            $data = $this->pro_service->getAvailableProvinces($request);

            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'message' => 'Có lỗi xảy ra khi lấy danh sách tỉnh thành có sản phẩm'
            ]);
        }
    }
    public function getHomepageProducts(Request $request)
    {
        try {
            $products = $this->pro_service->getHomepageProducts($request);
            return response()->json($products);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm',
            ]);
        }
    }

    public function getProductDetail($id)
    {
        try {
            $data = $this->pro_service->formatProductDetail($id);
            return response()->json($data);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm',
            ], 500);
        }
    }

    public function getVariantIdByAttributeValues(Request $request, $productId)
    {
        $valueIds = $request->input('value_ids'); // Mảng các attribute_value_id được chọn, ví dụ [1, 3]

        // Lấy tất cả biến thể của sản phẩm
        $variants = \App\Models\ProductVariant::where('product_id', $productId)
            ->with('variantAttribute')
            ->get();

        // Duyệt qua từng biến thể để kiểm tra
        foreach ($variants as $variant) {
            $variantValueIds = $variant->variantAttribute->pluck('attribute_value_id')->sort()->values();
            $selectedValueIds = collect($valueIds)->sort()->values();

            if ($variantValueIds->toArray() === $selectedValueIds->toArray()) {
                return response()->json([
                    'variant' => $variant,
                    'variant_id' => $variant->id,
                    'message' => 'Tìm thấy biến thể',
                ]);
            }
        }

        return response()->json([
            'variant' => null,
            'variant_id' => null,
            'message' => 'Không tìm thấy biến thể phù hợp',
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            // Tạo sản phẩm
            $product = Product::create([
                'category_id' => $request->category_id,
                'shop_id' => $request->shop_id,
                'name' => $request->product_name,
                'slug' => Str::slug($request->product_name) . '-' . time(),
                'description' => $request->description,
                'weight' => $request->weight ?? 0.00,
                'length' => $request->length ?? 0.00,
                'width' => $request->width ?? 0.00,
                'height' => $request->height ?? 0.00,
            ]);

            // Lưu ảnh sản phẩm
            if ($request->hasFile('file_images')) {
                foreach ($request->file('file_images') as $index => $img_file) {
                    $fileName = $img_file->getClientOriginalName();
                    $product->medias()->create([
                        'url' => $fileName,
                        'is_main' => $index === 0 ? 1 : 0,
                        'type' => 'image',
                    ]);
                    $frontendPath = base_path('../frontend/public/imgs/products/' . $fileName);
                    $img_file->move(dirname($frontendPath), basename($frontendPath));
                }
            }

            // Lưu video sản phẩm
            if ($request->hasFile('file_video')) {
                $video = $request->file('file_video');
                $fileName = $video->getClientOriginalName();
                $product->medias()->create([
                    'url' => $fileName,
                    'is_main' => 1,
                    'type' => 'video'
                ]);
                $frontendPath = base_path('../frontend/public/imgs/products/' . $fileName);
                $video->move(dirname($frontendPath), basename($frontendPath));
            }

            // Xử lý biến thể
            $variants = json_decode($request->variants, true);

            foreach ($variants as $index => $variant_data) {
                $variant_image = null;
                $fileName = null;
                if ($request->hasFile("variant_images.$index")) {
                    $variant_image = $request->file("variant_images.$index");
                    $fileName = $variant_image->getClientOriginalName();
                    $frontendPath = base_path('../frontend/public/imgs/products/' . $fileName);
                    $variant_image->move(dirname($frontendPath), basename($frontendPath));
                }

                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'price' => $variant_data['price'],
                    'stock' => $variant_data['stock'],
                    'sku' => $variant_data['sku'],
                    'image' => $variant_image ? $fileName : null,
                ]);

                if (isset($variant_data['attributes']) && is_array($variant_data['attributes'])) {
                    foreach ($variant_data['attributes'] as $attr) {
                        VariantAttribute::create([
                            'variant_id' => $variant->id,
                            'attribute_value_id' => $attr['value_id'],
                        ]);
                    }
                }
            }

            return response()->json(['message' => 'Product created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 500);
        }
    }

    public function getAllProducts(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status'); // Thêm filter status
        $perPage = 8;
        $query = Product::with('shop');
        
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        
        // Filter theo status nếu có
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }
        
        $products = $query->paginate($perPage);

        $productsData = collect($products->items())->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'shop' => $product->shop ? $product->shop->shop_name : null,
                'status' => $product->status,
                'updated_at' => $product->updated_at
            ];
        });

        // Đếm tổng số sản phẩm cho từng trạng thái (luôn lấy tất cả, không filter)
        $allQuery = Product::query();
        // Không filter theo search cho việc đếm để giữ số hiển thị ổn định
        $total = (clone $allQuery)->count();
        $pending = (clone $allQuery)->where('status', 0)->count();
        $active = (clone $allQuery)->where('status', 1)->count();
        $paused = (clone $allQuery)->where('status', 2)->count();
        $rejected = (clone $allQuery)->where('status', 3)->count();

        // Tính tổng số sản phẩm cho pagination (đã filter)
        $filteredTotal = $products->total();

        return response()->json([
            'products' => $productsData,
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'last_page' => $products->lastPage(),
            'total' => $filteredTotal, // Số sản phẩm cho pagination hiện tại
            'all_total' => $total, // Tổng số tất cả sản phẩm
            'pending' => $pending,
            'active' => $active,
            'paused' => $paused,
            'rejected' => $rejected,
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $user = auth()->user();
        $product = Product::find($id);
        $oldStatus = $product->status; // Lưu trạng thái cũ
        $newStatus = $request->input('status');
        $product->status = $newStatus;
        $product->approved_by = $user->id;
        $product->approved_at = now();
        $shop = $product->shop;
        
        if ($newStatus == 1) {
            if ($oldStatus == 0 || $oldStatus == 3) {
                // Duyệt sản phẩm mới hoặc sản phẩm bị từ chối
                $this->notification_service->createNotification([
                    'title' => 'Sản phẩm đã được duyệt',
                    'message' => "Sản phẩm {$product->name} đã được duyệt.",
                    'send_type' => 'to_shop',
                    'receiver_ids' => [$shop->owner_id],
                ]);
                // Gửi cho người theo dõi chỉ khi duyệt sản phẩm mới
                if ($oldStatus == 0) {
                    $this->notification_service->createNotification([
                        'title' => "{$shop->shop_name} vừa đăng sản phẩm mới",
                        'message' => "Khám phá ngay sản phẩm \"{$product->name}\" vừa được ra mắt tại shop!",
                        'send_type' => 'to_user',
                        'is_send_follower' => true,
                        'shop_id' => $shop->id,
                        'link' => env('FRONTEND_URL') . '/' . $product->slug . '-' . $product->id
                    ]);
                }
            } else if ($oldStatus == 2) {
                // Mở khóa sản phẩm
                $this->notification_service->createNotification([
                    'title' => 'Sản phẩm đã được mở khóa',
                    'message' => "Sản phẩm {$product->name} đã được mở khóa và có thể hoạt động trở lại.",
                    'send_type' => 'to_shop',
                    'receiver_ids' => [$shop->owner_id],
                ]);
            }
        } else if ($newStatus == 3) {
            $product->rejected_reason = $request->input('reason');
            // Gửi email lý do từ chối đến shop
            $this->notification_service->createNotification(data: [
                'title' => 'Sản phẩm bị từ chối.',
                'message' => "Sản phẩm {$product->name} đã bị từ chối. Lý do: {$product->rejected_reason}",
                'send_type' => 'to_shop',
                'receiver_ids' => [$shop->owner_id],

            ]);
            if ($shop && $shop->user && $shop->user->email) {
                Mail::to($shop->user->email)->send(new ProductRejectedMail($product, $product->rejected_reason));
            }
        } else if ($product->status == 2) {
            $product->rejected_reason = $request->input('reason');
            $this->notification_service->createNotification(data: [
                'title' => 'Sản phẩm đã bị khóa.',
                'message' => "Sản phẩm {$product->name} đã bị khóa. Lý do: {$product->rejected_reason}",
                'send_type' => 'to_shop',
                'receiver_ids' => [$shop->owner_id],
            ]);
        } else {
            $product->rejected_reason = null;
        }
        $product->save();
        return response()->json([
            'message' => 'Đổi trạng thái thành công'
        ]);
    }

    public function changeMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $products = Product::whereIn('id', $ids)->get();
            $newStatus = $request->input('status');
            $user = auth()->user();
            
            foreach ($products as $product) {
                $oldStatus = $product->status;
                $product->status = $newStatus;
                $product->approved_by = $user->id;
                $product->approved_at = now();
                $shop = $product->shop;
                
                // Gửi notification phù hợp dựa trên trạng thái cũ và mới
                if ($newStatus == 1) {
                    if ($oldStatus == 0 || $oldStatus == 3) {
                        // Duyệt sản phẩm mới hoặc sản phẩm bị từ chối
                        $this->notification_service->createNotification([
                            'title' => 'Sản phẩm đã được duyệt',
                            'message' => "Sản phẩm {$product->name} đã được duyệt.",
                            'send_type' => 'to_shop',
                            'receiver_ids' => [$shop->owner_id],
                        ]);
                        // Gửi cho người theo dõi chỉ khi duyệt sản phẩm mới
                        if ($oldStatus == 0) {
                            $this->notification_service->createNotification([
                                'title' => "{$shop->shop_name} vừa đăng sản phẩm mới",
                                'message' => "Khám phá ngay sản phẩm \"{$product->name}\" vừa được ra mắt tại shop!",
                                'send_type' => 'to_user',
                                'is_send_follower' => true,
                                'shop_id' => $shop->id,
                                'link' => env('FRONTEND_URL') . '/' . $product->slug . '-' . $product->id
                            ]);
                        }
                    } else if ($oldStatus == 2) {
                        // Mở khóa sản phẩm
                        $this->notification_service->createNotification([
                            'title' => 'Sản phẩm đã được mở khóa',
                            'message' => "Sản phẩm {$product->name} đã được mở khóa và có thể hoạt động trở lại.",
                            'send_type' => 'to_shop',
                            'receiver_ids' => [$shop->owner_id],
                        ]);
                    }
                } else if ($newStatus == 2) {
                    // Khóa sản phẩm
                    $this->notification_service->createNotification([
                        'title' => 'Sản phẩm đã bị khóa',
                        'message' => "Sản phẩm {$product->name} đã bị khóa.",
                        'send_type' => 'to_shop',
                        'receiver_ids' => [$shop->owner_id],
                    ]);
                }
                
                $product->save();
            }
            return response()->json(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi'], 500);
        }
    }

    public function get_product_shop(Request $request)
    {
        $user = auth()->user();

        $shop = Shop::where('owner_id', $user->id)->firstOrFail();
        $shop_id = $shop->id;

        try {
            $query = Product::with(['medias', 'category', 'variants.variantAttribute.attributeValue'])
                ->where('shop_id', $shop_id)
                ->withSum('variants as total_sell', 'sell')
                ->withSum('variants as total_stock', 'stock');

            //status
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }


            //phan trang
            $per_page = $request->input('per_page', 5);
            $products = $query->paginate($per_page);

            return response()->json(['Products' => $products]);
        } catch (\Throwable $th) {
            return response()->json([

                'message' => 'Loi lay Product!!',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function get_shop_by_product($product_id)
    {
        try {
            $product = Product::with('shop')->findOrFail($product_id);

            return response()->json([
                'success' => true,
                'shop' => $product->shop,
                'receiver_id' => $product->shop->id ?? null,
                'receiver_type' => 'shop'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm hoặc có lỗi xảy ra.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function get_product(Request $request)
    {
        $product_id = $request->input('product_id');

        $product = Product::with(['medias', 'category', 'variants.variantAttribute.attributeValue'])
            ->find($product_id);

        return response()->json([
            'product' => $product,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:tbl_products,id',
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            // validate ảnh/video nếu cần
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cập nhật thông tin cơ bản
        $product->name = $request->product_name;
        $product->category_id = $request->category_id;
        $product->description = $request->description;
        $product->weight = $request->weight;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->save();

        // Xử lý ảnh chính
        $oldImages = $request->input('old_images', []);
        $newImages = $request->file('file_images', []);
        $mediaImages = [];

        // Giữ lại ảnh cũ
        foreach ($oldImages as $imgName) {
            $media = $product->medias()->where('type', 'image')->where('url', $imgName)->first();
            if ($media)
                $mediaImages[] = $media->id;
        }

        // Thêm ảnh mới
        foreach ($newImages as $imgFile) {
            $fileName = $imgFile->getClientOriginalName();
            $product->medias()->create([
                'type' => 'image',
                'url' => $fileName,
            ]);
            $frontendPath = base_path('../frontend/public/imgs/products/' . $fileName);
            $imgFile->move(dirname($frontendPath), basename($frontendPath));
            $mediaImages[] = $media->id;
        }

        // Xóa ảnh cũ không còn giữ lại
        $product->medias()->where('type', 'image')->whereNotIn('id', $mediaImages)->delete();

        // Xử lý video
        if ($request->hasFile('file_video')) {
            $product->medias()->where('type', 'video')->delete();
            $videoFile = $request->file('file_video');
            $fileName = $videoFile->getClientOriginalName();
            $product->medias()->create([
                'type' => 'video',
                'url' => $fileName,
            ]);
            $frontendPath = base_path('../frontend/public/imgs/products/' . $fileName);
            $videoFile->move(dirname($frontendPath), basename($frontendPath));
        } elseif ($request->has('old_video')) {
            // Giữ lại video cũ, xóa các video khác
            $product->medias()->where('type', 'video')->where('url', '!=', $request->input('old_video'))->delete();
        } else {
            // Không gửi gì, xóa hết video
            $product->medias()->where('type', 'video')->delete();
        }

        // Xử lý biến thể (variants) và ảnh biến thể
        $variants = json_decode($request->input('variants', '[]'), true);
        foreach ($variants as $idx => $variantData) {
            // Tìm hoặc tạo variant
            $variant = $product->variants()->updateOrCreate(
                ['sku' => $variantData['sku']],
                [
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]
            );
            // Ảnh biến thể
            if ($request->hasFile("variant_images.$idx")) {
                $imgFile = $request->file("variant_images.$idx");
                $fileName = $imgFile->getClientOriginalName();
                $frontendPath = base_path('../frontend/public/imgs/products/' . $fileName);
                $imgFile->move(dirname($frontendPath), basename($frontendPath));
                $variant->image = $fileName;
                $variant->save();
            } elseif ($request->has("old_variant_images.$idx")) {
                $variant->image = $request->input("old_variant_images.$idx");
                $variant->save();
            }
        }

        return response()->json(['message' => 'Cập nhật sản phẩm thành công']);
    }

    public function update_shutdown_status(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:tbl_products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->status = 2;
        $product->save();

        return response()->json(['message' => 'Tạm ngưng hoạt động sản phẩm thành công']);
    }

    public function update_pending_status(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:tbl_products,id',
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->status = 0;
        $product->save();

        return response()->json(['message' => 'Chờ duyệt sản phẩm thành công']);
    }

    public function getTopViewedProducts(Request $request)
    {
        try {
            $products = $this->pro_service->getTopViewedProducts($request);
            return response()->json($products);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm',
            ]);
        }
    }

    public function getRelatedProducts(Request $request)
    {
        try {
            $product_related = $this->pro_service->getRelatedProducts($request);
            return response()->json($product_related, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage(),
                'message' => 'Có lỗi xảy ra khi lấy sản phẩm',
            ], 500);
        }
    }
}
