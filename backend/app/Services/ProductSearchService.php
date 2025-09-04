<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Statickidz\GoogleTranslate;

class ProductSearchService
{
    public function getLabelsFromImage($request)
    {
        if (!$request->hasFile('image')) {
            throw new \Exception("Không nhận được ảnh.", 400);
        }

        $image = $request->file('image');

        try {
            $results = $this->analyzeImage($image);
        } catch (Exception $e) {
            throw new Exception("Không đọc được nội dung hình ảnh: " . $e->getMessage(), 500);
        }

        return $this->translateLabels($results);
    }

    private function analyzeImage($image)
    {
        $apiUrl = "";

        $response = Http::withHeaders([])->withBody(
                file_get_contents($image->getRealPath()),
                $image->getMimeType()
            )->post($apiUrl);

        if (!$response->successful()) {
            throw new Exception("Lỗi từ Hugging Face: " . $response->body(), $response->status());
        }

        return $response->json();
    }

    private function translateLabels(array $results)
    {
        $trans = new GoogleTranslate();

        return collect($results)->map(function ($item) use ($trans) {
            $labelEn = $item['label'];
            $score = $item['score'];

            try {
                $labelVi = $trans->translate('en', 'vi', $labelEn);
            } catch (Exception $e) {
                \Log::error("Lỗi dịch: " . $e->getMessage());
                $labelVi = $labelEn;
            }

            return [
                'label_en' => $labelEn,
                'label_vi' => $labelVi,
                'score' => $score,
            ];
        })->toArray();
    }
}