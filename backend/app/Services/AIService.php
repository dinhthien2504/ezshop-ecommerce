<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;

class AIService
{
    public function generate_description($product_name){
        $prompt = <<<EOD
        Viết mô tả sản phẩm chuẩn SEO cho sản phẩm có tên là: "{$product_name}". Độ dài từ 300–500 từ.

        Yêu cầu:
        - Bắt đầu trực tiếp với tiêu đề, không cần giới thiệu hoặc lời dẫn.
        - Có tiêu đề cấp 2 (##), mô tả chi tiết, danh sách ưu điểm (* hoặc -).
        - Sử dụng định dạng Markdown.
        - Nội dung hấp dẫn, tập trung vào lợi ích, tính năng, chất liệu, ứng dụng.
        - Không chứa placeholder như [Tên Thương Hiệu] hay [số ngày].
        - Chèn tự nhiên các từ khóa như: áo thun thể thao nam, áo gym nam, thời trang thể thao,...
        - Không dùng lời thoại hoặc văn phong dạng hội thoại như "Tuyệt vời! Dưới đây là..."
        EOD;


        $key = env('GEMINI_API_KEY');

        $response = Http::timeout(20)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$key}",
            [
                'contents' => [[ 'parts' => [[ 'text' => $prompt ]] ]]
            ]
        );

        Log::info('Gemini raw response: ' . $response->body());

        $markdown = $response->json('candidates.0.content.parts.0.text') ?? null;

        if (!$markdown) {
            return response()->json([
                'description' => '<p><em>Không thể tạo mô tả sản phẩm.</em></p>'
            ]);
        }

        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $html = (string) $converter->convert($markdown);

        return $html;
    }

    public function chat($messages){
        $key = env('GEMINI_API_KEY');

         $contents = collect($messages)->map(function($m) {
            return [
                "role" => $m['role'] === 'user' ? "user" : "model",
                "parts" => [["text" => $m['text']]]
            ];
        })->toArray();

        $response = Http::timeout(20)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$key}",
            [ 'contents' => $contents ]
        );

        return $response->json('candidates.0.content.parts.0.text');
    }
}
