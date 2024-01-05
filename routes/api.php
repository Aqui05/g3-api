<?php

use App\Http\Controllers\API\QuantityContoller;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\CategoryContoller;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Auth\ProviderController;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware'=>'api','prefix'=>'auth'],
function(){
    //add this to the end of all route who need verification (dashboard for example) ->middleware('verified')
    Route::post('/register_Buyer',[AuthController::class,'registerBuyer']);
    Route::post('/register_Seller',[AuthController::class,'registerSeller']);
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/refresh', [AuthController::class,'refresh']);
    Route::post('/become_seller', [UserController::class,'becomeSeller']);
    Route::post('/send_Reset_Link_Email', [UserController::class,'sendResetLinkEmail']);
    Route::post('/modify_profile',[UserController::class,'modifyProfile']);

    Route::post('resetpassword/{token}', [UserController::class,'resetPassword'])/*->name('password.reset')*/;

});

Route::get('/auth/{provider}/redirect', [ProviderController::class, 'redirect']);

Route::get('/auth/{provider}/callback', [ProviderController::class , 'callback']);

//API PRODUCTS

Route::get('/products',[ProductController::class,'getProducts']);
Route::get('/product/{id}',[ProductController::class,'getProductById']);
Route::get('/product_all',[ProductController::class,'getAllProducts']);
Route::post('/addProduct',[ProductController::class,'addProduct']);
Route::post('/updateProduct/{id}',[ProductController::class,'updateProduct']);
Route::delete('/deleteProduct/{id}',[ProductController::class,'deleteProduct']);
Route::get('/searchProducts',[ProductController::class,'searchProduct']);

Route::get('/products/{id}/quantite-disponible',[QuantityContoller::class, 'getQuantityDispo']);


//FAVORITE PRODUCT
Route::post('/favorites/toggle/{productId}', [FavoriteController::class, 'toggleFavorite']);
Route::get('/favorites', [FavoriteController::class, 'getFavorites']);


//filter de product:

Route::get('/products-filter/{categorie}', [CategoryContoller::class, 'filter_category']);
Route::get('/products-sous_filter/{subcategory}', [CategoryContoller::class, 'filter_subcategory']);
Route::get('/products/filter', [CategoryContoller::class, 'filterProducts']);



//API PANIER

Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add/{productId}', [CartController::class, 'addItem']);
Route::post('/cart/update/{itemId}', [CartController::class, 'updateItem']);
Route::get('/cart/total-price', [CartController::class, 'getTotalPrice']);
Route::delete('/cart/remove_item_to_cart/{productId}', [CartController::class, 'removeItem']);


//API ORDER

Route::post('/orders/create/{productId}', [OrderController::class, 'createOrder']);
Route::get('/orders', [OrderController::class, 'getUserOrders']);
Route::post('/orders/{orderId}/update-status', [OrderController::class, 'updateOrderStatus']);
Route::post('/orders/create-from-cart', [OrderController::class, 'createOrderFromCart']);
Route::get('/orders/seller', [OrderController::class, 'getOrdersForSeller']);
Route::get('/orders/{orderId}/products', [OrderController::class, 'getOrderProducts']);
Route::post('/passerCommandePanier/{cartId}', [OrderController::class, 'passerCommandePanier']);
Route::get('/orders/monthly', [OrderController::class, 'calculateMonthlySales']);



//COMMENT AND EVALUATION

Route::post('/add_comment/{productId}',[CommentController::class, 'addComment']);
Route::delete('/delete_comment/{commentId}',[CommentController::class, 'deleteComment']);
Route::post('/update_comment/{commentId}',[CommentController::class, 'updateComment']);
Route::post('/rate_product/{productId}',[CommentController::class, 'rateProduct']);
Route::get('/view_comments/{productId}',[CommentController::class, 'viewComment']);
Route::post('/comments/{comment}/reply', [CommentController::class, 'addReply']);




//PROMOTION AND CODE PROMO

Route::post('/add_promo/{product}', [PromotionController::class, 'addPromotion']);
Route::post('/add_code_promo/{product}', [PromotionController::class, 'createDiscountCode']);
Route::get('/products-with-valid-promotion', [PromotionController::class, 'getProductsWithValidPromotion']);



//PAYEMENT

/*

****Route::post('/paypal/create-payment', [PaymentController::class, 'createPayment'])->name('paypal.create');
****Route::get('/paypal/success', [PaymentController::class, 'success'])->name('paypal.success');
****Route::get('/paypal/cancel', [PaymentController::class, 'cancel'])->name('paypal.cancel');

*/



//MOMO API
Route::get('/payer/{orderId}', [PaymentController::class, 'payer'])/*->name('payement')*/; //page de payement
Route::post('/payer', [PaymentController::class, 'payerPost'])/*->name('payement')*/;





Route::get('/SeeNotification',[UserController::class, 'SeeNotification']);
