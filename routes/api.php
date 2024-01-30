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
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::get('/profile',[AuthController::class,'profile']);
    Route::post('/logout',[AuthController::class,'logout']);

    //rafraichir la page : Changer de token
    Route::post('/refresh', [AuthController::class,'refresh']);

    //Changer son compte en un compte vendeur
    Route::post('/become_seller', [UserController::class,'becomeSeller']);

    //Envoyer le lien par email pour Changer son password
    Route::post('/send_Reset_Link_Email', [UserController::class,'sendResetLinkEmail']);

    //Changer son password
    Route::post('resetpassword/{token}', [UserController::class,'resetPassword']);

    //Modifier les information de son compte
    Route::post('/modify_profile',[UserController::class,'modifyProfile']);


});

    Route::post('/register_admin',[UserController::class,'registerAdmin']);

Route::post('/addCategory',[ProductController::class,'addCategory']);
Route::post('/addSubcategory',[ProductController::class,'addSubcategory']);


/*
*API PRODUCTS
*/

//List de tous les produits relatif à un vendeur
Route::get('/products',[ProductController::class,'getProducts']);

//Détails d'un seul produit
Route::get('/product/{id}',[ProductController::class,'getProductById']);

//List de tous les produits dans la BD
Route::get('/product_all',[ProductController::class,'getAllProducts']);

//ajouter un produit
Route::post('/addProduct',[ProductController::class,'addProduct']);

//Mettre a jour un produit
Route::post('/updateProduct/{id}',[ProductController::class,'updateProduct']);

//Supprimer un produit
Route::delete('/deleteProduct/{id}',[ProductController::class,'deleteProduct']);

//Rechercher un produit
Route::get('/searchProducts',[ProductController::class,'searchProduct']);

//Obtenir la quantité du produit dans la BD
Route::get('/products/{id}/quantite-disponible',[QuantityContoller::class, 'getQuantityDispo']);


/*
*FAVORITE PRODUCT
*/

//Ajouter aux favoris
Route::post('/favorites/toggle/{productId}', [FavoriteController::class, 'toggleFavorite']);

//Voir la list de ses produits favoris
Route::get('/favorites', [FavoriteController::class, 'getFavorites']);


/*
*filter de product:
*/

//filtrer par category
Route::get('/products-filter/{categorie}', [CategoryContoller::class, 'filter_category']);

//filtrer par sous category
Route::get('/products-sous_filter', [CategoryContoller::class, 'filter_subcategory']);

//filtrer avec d'autres option
Route::get('/products/filter', [CategoryContoller::class, 'filterProducts']);



/*
*API PANIER
*/

//Récupérer la liste des produits du panier
Route::get('/cart', [CartController::class, 'index']);

//Ajouter un produit a son panier
Route::post('/cart/add/{productId}', [CartController::class, 'addItem']);

//Mettre a jour un produit dans son panier: La quantité peut etre
Route::post('/cart/update/{itemId}', [CartController::class, 'updateItem']);

//Récupérer le prix du panier
Route::get('/cart/total-price', [CartController::class, 'getTotalPrice']);

//Enlever un produit de son panier
Route::delete('/cart/remove_item_to_cart/{productId}', [CartController::class, 'removeItem']);


/*
*API ORDER
*/

//Commander un produit
Route::post('/orders/create/{productId}', [OrderController::class, 'createOrder']);

//Voir la liste de ses commandes
Route::get('/orders', [OrderController::class, 'getUserOrders']);

//Changer le statut d'une commande: Si la commande a été bien recu
Route::post('/orders/{orderId}/update-status', [OrderController::class, 'updateOrderStatus']);

//Commander les produits dans son panier
Route::post('/orders/create-from-cart', [OrderController::class, 'createOrderFromCart']);

//Un vendeur qui récupère les commandes relatif a ses produits
Route::get('/orders/seller', [OrderController::class, 'getOrdersForSeller']);

//Produits présents dans la commande
Route::get('/orders/{orderId}/products', [OrderController::class, 'getOrderProducts']);

//Rendu a chaque vendeur sur ses ventes par mois
Route::get('/orders/monthly', [OrderController::class, 'calculateMonthlySales']);



//COMMENT AND EVALUATION

Route::post('/add_comment/{productId}',[CommentController::class, 'addComment']);
Route::delete('/delete_comment/{commentId}',[CommentController::class, 'deleteComment']);
Route::post('/update_comment/{commentId}',[CommentController::class, 'updateComment']);

//Noter un produit
Route::post('/rate_product/{productId}',[CommentController::class, 'rateProduct']);

//Voir les commentaires
Route::get('/view_comments/{productId}',[CommentController::class, 'viewComment']);

//Répondre a un commentaire
Route::post('/comments/{comment}/reply', [CommentController::class, 'addReply']);




//PROMOTION AND CODE PROMO

Route::post('/add_promo/{product}', [PromotionController::class, 'addPromotion']);
Route::post('/add_code_promo/{product}', [PromotionController::class, 'createDiscountCode']);
Route::get('/products-with-valid-promotion', [PromotionController::class, 'getProductsWithValidPromotion']);



//PAYMENT static

Route::post('/payer/{orderId}', [PaymentController::class, 'payer']);



//notifications

Route::get('/SeeNotification',[UserController::class, 'SeeNotification']);





/*
*Connexion avec un service tiers: Le nom du service sera remplacer par $provider.(google/github)
*/

Route::get('/auth/{provider}/redirect', [ProviderController::class, 'redirect']);

Route::get('/auth/{provider}/callback', [ProviderController::class , 'callback']);
