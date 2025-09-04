    <?php

    use App\Http\Controllers\FavoriteController;
    use Illuminate\Support\Facades\Route;
        
        
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('favorites', [FavoriteController::class, 'index']);
        Route::post('favorites', [FavoriteController::class, 'store']);
        Route::delete('favorites/{product_id}', [FavoriteController::class, 'destroy']);
        Route::get('favorites/checkWishlist', [FavoriteController::class, 'checkWishlist']);
        Route::get('/productWishlist-all', [FavoriteController::class, 'getAllProductsWishlist']);
    });