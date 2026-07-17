<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductImageController extends Controller
{
    /**
     * Получить изображение товара.
     */
    public function show(Product $product)
    {
        foreach (['png', 'jpg', 'jpeg'] as $extension) {

            $path = "products/{$product->id}.{$extension}";

            if (Storage::disk('public')->exists($path)) {
                return response()->file(
                    Storage::disk('public')->path($path)
                );
            }
        }

        return response()->file(
            Storage::disk('public')->path("products/no_product.png")
        );
    }

    /**
     * Загрузить изображение товара.
     */
    public function upload(
        Request $request,
        Product $product
    ) {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg',
                'max:5120',
            ],
        ]);

        // удаляем старое изображение
        foreach (['png', 'jpg', 'jpeg'] as $extension) {

            Storage::disk('public')->delete(
                "products/{$product->id}.{$extension}"
            );
        }

        $file = $request->file('image');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $file->storeAs(
            'products',
            "{$product->id}.{$extension}",
            'public'
        );

        return response()->json([
            'success' => true,
        ]);
    }
}
