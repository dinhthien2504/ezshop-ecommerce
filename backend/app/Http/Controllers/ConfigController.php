<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Http\Requests\UpdateConfigRequest;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{

    public function getConfig()
    {
        // Lấy bản ghi đầu tiên
        $config = Config::first();

        // Nếu chưa có, tạo bản ghi rỗng
        if (!$config) {
            $config = Config::create(); // dùng create để tiện mở rộng sau này
        }

        return response()->json([
            'config' => $config,
        ]);
    }

    public function update(UpdateConfigRequest $request)
    {
        $config = Config::first();
        if (!$config) {
            $config = new Config();
        }

        $config->title = $request->input('title');
        $config->description = $request->input('description');
        $config->phone = $request->input('phone');
        $config->email = $request->input('email');
        $config->address = $request->input('address');
        $config->facebook = $request->input('facebook');
        $config->youtube = $request->input('youtube');
        $config->tiktok = $request->input('tiktok');
        $config->twitter = $request->input('twitter');
        $config->instagram = $request->input('instagram');
        $config->main_color = $request->input('main_color');

        $uploadPath = base_path('../frontend/public/imgs');
        Log::info('Upload path: ' . $uploadPath);

        if (!file_exists($uploadPath)) {
            Log::info('Thư mục chưa tồn tại. Đang tạo...');
            mkdir($uploadPath, 0777, true);
        } else {
            Log::info('Thư mục đã tồn tại.');
        }

        if ($request->hasFile('logo_header')) {
            Log::info('Nhận được file logo_header');

            // Xóa ảnh cũ nếu tồn tại
            if (!empty($config->logo_header)) {
                $oldPath = $uploadPath . '/' . $config->logo_header;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                    Log::info('Đã xóa logo_header cũ: ' . $oldPath);
                }
            }

            $file = $request->file('logo_header');
            $filename = 'logo_header_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            Log::info('Đã lưu logo_header thành: ' . $filename);
            $config->logo_header = $filename;
        } else {
            Log::warning('Không nhận được file logo_header');
        }

        if ($request->hasFile('logo_footer')) {
            Log::info('Nhận được file logo_footer');

            // Xóa ảnh cũ nếu tồn tại
            if (!empty($config->logo_footer)) {
                $oldPath = $uploadPath . '/' . $config->logo_footer;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                    Log::info('Đã xóa logo_footer cũ: ' . $oldPath);
                }
            }

            $file = $request->file('logo_footer');
            $filename = 'logo_footer_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $filename);
            Log::info('Đã lưu logo_footer thành: ' . $filename);
            $config->logo_footer = $filename;
        } else {
            Log::warning('Không nhận được file logo_footer');
        }


        $config->save();

        return response()->json(['message' => 'Cập nhật thành công']);
    }
}
