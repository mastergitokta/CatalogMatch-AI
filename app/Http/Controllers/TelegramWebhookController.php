<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use App\Services\ProductImageHashService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly ProductImageHashService $imageHashService,
    ) {
    }

    public function __invoke(Request $request, string $token): JsonResponse
    {
        $configuredToken = (string) config('services.telegram.bot_token');

        abort_if(
            blank($configuredToken) || ! hash_equals($configuredToken, $token),
            Response::HTTP_FORBIDDEN
        );

        $message = $request->input('message', $request->input('edited_message'));

        if (! is_array($message)) {
            return response()->json([
                'ok' => true,
                'message' => 'No supported message payload found.',
            ]);
        }

        $chatId = Arr::get($message, 'chat.id');

        if (! $chatId) {
            return response()->json([
                'ok' => false,
                'message' => 'Chat ID is missing.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $photos = collect(Arr::get($message, 'photo', []));

        if ($photos->isEmpty()) {
            $this->sendTelegramMessage(
                $chatId,
                'Silakan kirim gambar produk agar saya bisa mencarikan produk yang paling mendekati.'
            );

            return response()->json([
                'ok' => true,
                'message' => 'No photo provided. Help message sent.',
            ]);
        }

        $fileId = (string) Arr::get(
            $photos->sortByDesc('file_size')->first(),
            'file_id'
        );

        if (blank($fileId)) {
            return response()->json([
                'ok' => false,
                'message' => 'Telegram file_id is missing.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $downloadedImagePath = $this->downloadTelegramImage($fileId);

        if (! $downloadedImagePath) {
            $this->sendTelegramMessage(
                $chatId,
                'Maaf, gambar dari Telegram belum bisa saya proses saat ini.'
            );

            return response()->json([
                'ok' => false,
                'message' => 'Failed to download Telegram image.',
            ], Response::HTTP_BAD_GATEWAY);
        }

        try {
            $incomingHash = $this->imageHashService->hashAbsolutePath($downloadedImagePath);

            if (! $incomingHash) {
                $this->sendTelegramMessage(
                    $chatId,
                    'Maaf, gambar yang dikirim belum bisa saya hash.'
                );

                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to hash Telegram image.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            [$matchedImage, $distance] = $this->findClosestMatch($incomingHash);

            if (! $matchedImage || ! $matchedImage->product) {
                $this->sendTelegramMessage(
                    $chatId,
                    'Maaf, saya belum menemukan produk yang mendekati gambar tersebut.'
                );

                return response()->json([
                    'ok' => true,
                    'message' => 'No matching product image found.',
                ]);
            }

            $this->sendTelegramMessage(
                $chatId,
                $this->buildProductReply($matchedImage->product->nama_produk, $matchedImage->product->deskripsi_produk)
            );

            return response()->json([
                'ok' => true,
                'matched_product_id' => $matchedImage->product_id,
                'matched_image_id' => $matchedImage->id,
                'distance' => $distance,
            ]);
        } finally {
            @unlink($downloadedImagePath);
        }
    }

    /**
     * @return array{0: ?ProductImage, 1: ?int}
     */
    private function findClosestMatch(string $incomingHash): array
    {
        $bestMatch = null;
        $bestDistance = null;

        ProductImage::query()
            ->with('product')
            ->whereNotNull('hash_img')
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->chunkById(100, function ($images) use ($incomingHash, &$bestMatch, &$bestDistance): void {
                foreach ($images as $image) {
                    $distance = $this->imageHashService->distance($incomingHash, $image->hash_img);

                    if ($distance === null) {
                        continue;
                    }

                    if ($bestDistance === null || $distance < $bestDistance) {
                        $bestMatch = $image;
                        $bestDistance = $distance;
                    }
                }
            });

        return [$bestMatch, $bestDistance];
    }

    private function buildProductReply(string $productName, ?string $description): string
    {
        $productName = trim($productName);
        $description = trim((string) ($description ?: 'Deskripsi produk belum tersedia.'));

        return "pilihan mobil yang sangat tepat!\n\n{$productName}\n\n{$description}";
    }

    private function downloadTelegramImage(string $fileId): ?string
    {
        $token = (string) config('services.telegram.bot_token');
        $getFileResponse = Http::timeout(15)
            ->acceptJson()
            ->get("https://api.telegram.org/bot{$token}/getFile", [
                'file_id' => $fileId,
            ]);

        if (! $getFileResponse->successful()) {
            return null;
        }

        $filePath = (string) data_get($getFileResponse->json(), 'result.file_path');

        if (blank($filePath)) {
            return null;
        }

        $imageResponse = Http::timeout(30)
            ->get("https://api.telegram.org/file/bot{$token}/{$filePath}");

        if (! $imageResponse->successful()) {
            return null;
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'jpg';
        $temporaryPath = tempnam(sys_get_temp_dir(), 'tg-image-');

        if ($temporaryPath === false) {
            return null;
        }

        $targetPath = $temporaryPath.'.'.$extension;

        if (! @rename($temporaryPath, $targetPath)) {
            $targetPath = $temporaryPath;
        }

        file_put_contents($targetPath, $imageResponse->body());

        return $targetPath;
    }

    private function sendTelegramMessage(int|string $chatId, string $message): void
    {
        $token = (string) config('services.telegram.bot_token');

        Http::timeout(15)
            ->asForm()
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);
    }
}
