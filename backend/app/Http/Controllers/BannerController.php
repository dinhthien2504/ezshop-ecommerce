<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;

class BannerController extends Controller
{
    public function getBanners(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 5;
        $query = Banner::query();
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        };
        $banners = $query->paginate($perPage);

        return response()->json([
            'banners' => $banners->items(),
            'current_page' => $banners->currentPage(),
            'per_page' => $banners->perPage(),
            'last_page' => $banners->lastPage(),
            'total' => $banners->total(),
        ]);
    }

    public function store(StoreBannerRequest $request)
    {
        try {
            $uploadPath = base_path('../frontend/public/imgs/banners/');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
                Log::info('Đã tạo thư mục banner: ' . $uploadPath);
            }

            $filename = null;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'banner_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                Log::info('Đã lưu file banner: ' . $filename);
            } else {
                Log::warning('Không nhận được file banner');
            }

            $banner = Banner::create([
                'title' => $request->input('title'),
                'image' => $filename,
                'link' => $request->input('link'),
                'position' => $request->input('position'),
                'priority' => $request->input('priority'),
                'start_at' => $request->input('start_at'),
                'end_at' => $request->input('end_at'),
            ]);

            return response()->json(['message' => 'Banner created successfully', 'banner' => $banner], 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo banner: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi tạo banner'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->delete();
            $imagePath = base_path('../frontend/public/imgs/banners/' . $banner->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
                Log::info('Đã xóa file banner: ' . $banner->image);
            } else {
                Log::warning('Không tìm thấy file banner để xóa: ' . $banner->image);
            }
            Log::info('Đã xóa banner với ID: ' . $id);
            return response()->json(['message' => 'Banner deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa banner: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa banner'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $banners = Banner::whereIn('id', $ids)->get();
            foreach ($banners as $banner) {
                $banner->delete();
                $imagePath = base_path('../frontend/public/imgs/banners/' . $banner->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Đã xóa file banner: ' . $banner->image);
                } else {
                    Log::warning('Không tìm thấy file banner để xóa: ' . $banner->image);
                }
            }
            Log::info('Đã xóa nhiều banner với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã xóa các banner thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa nhiều banner: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa các banner'], 500);
        }
    }

    public function update(UpdateBannerRequest $request, $id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $uploadPath = base_path('../frontend/public/imgs/banners/');

            if ($request->hasFile('image')) {
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                    Log::info('Đã tạo thư mục banner: ' . $uploadPath);
                }

                // Xóa file cũ nếu có
                if ($banner->image && file_exists($uploadPath . $banner->image)) {
                    unlink($uploadPath . $banner->image);
                    Log::info('Đã xóa file banner cũ: ' . $banner->image);
                }

                $file = $request->file('image');
                $filename = 'banner_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                Log::info('Đã lưu file banner mới: ' . $filename);
                $banner->image = $filename;
            }

            // Cập nhật các trường khác
            $banner->title = $request->input('title');
            $banner->link = $request->input('link');
            $banner->position = $request->input('position');
            $banner->priority = $request->input('priority');
            $banner->start_at = $request->input('start_at');
            $banner->end_at = $request->input('end_at');

            $banner->save();

            return response()->json(['message' => 'Banner updated successfully', 'banner' => $banner], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật banner: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật banner'], 500);
        }
    }

    public function getHomeBanners()
    {
        $today = now();

        // Lấy banner từng vị trí (ưu tiên cao nhất, trong thời gian hiển thị)
        $positions = ['home_1', 'home_2', 'home_3', 'mobile_1', 'mobile_2', 'mobile_3'];
        $result = [];

        foreach ($positions as $pos) {
            $banner = Banner::where('position', $pos)
                ->where(function ($q) use ($today) {
                    $q->whereNull('start_at')->orWhere('start_at', '<=', $today);
                })
                ->where(function ($q) use ($today) {
                    $q->whereNull('end_at')->orWhere('end_at', '>=', $today);
                })
                ->orderBy('priority', 'asc') // Ưu tiên nhỏ nhất là cao nhất
                ->orderBy('id', 'desc')
                ->first();
            $result[$pos] = $banner;
        }

        // Lấy tất cả banner slider (có thể nhiều)
        $sliders = Banner::where('position', 'slider')
            ->where(function ($q) use ($today) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $today);
            })
            ->orderByDesc('priority')
            ->orderBy('id', 'desc')
            ->get();

        $result['sliders'] = $sliders->values();

        return response()->json($result);
    }
}
