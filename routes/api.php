<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GigController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Access API Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/gigs', [GigController::class, 'index']);
Route::get('/user/profile/{id}', [AuthController::class, 'getPublicProfile']);
Route::get('/orders/{id}/invoice', [PaymentController::class, 'downloadInvoice']);

/*
|--------------------------------------------------------------------------
| Authenticated Session API Routes (Sanctum Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Notification Resources
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Wishlist Resources
    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/toggle/{gigId}', [WishlistController::class, 'toggle']);
    });

    // Profile & Role State Transitions
    Route::patch('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/upgrade-to-seller', [AuthController::class, 'upgradeToSeller']);
    Route::post('/downgrade-to-buyer', [AuthController::class, 'downgradeToBuyer']);
    
    // Seller Portfolio Flyers Assets Management
    Route::prefix('user/profile/portfolio')->group(function () {
        Route::post('/', [AuthController::class, 'uploadPortfolioAsset']);
        Route::patch('/{id}/visibility', [AuthController::class, 'togglePortfolioVisibility']);
        Route::delete('/{id}', [AuthController::class, 'deletePortfolioAsset']);
    });

    // Freelance Service Listings Management
    Route::get('/my-gigs', [GigController::class, 'myGigs']);
    Route::get('/archived-slots', [GigController::class, 'archivedSlots']);
    Route::post('/gigs', [GigController::class, 'store']);
    Route::put('/gigs/{id}', [GigController::class, 'update']);
    Route::delete('/gigs/{id}', [GigController::class, 'destroy']);

    // Order Placement & Execution Workflows
    Route::get('/my-purchases', [OrderController::class, 'myPurchases']);
    Route::get('/incoming-orders', [OrderController::class, 'incomingOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    
    // Settlement Payment Integration
    Route::post('/orders/{id}/pay', [PaymentController::class, 'createBill']);
    Route::get('/orders/{id}/check-payment', [PaymentController::class, 'checkStatus']);

    // Marketplace Feedback Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::patch('/reviews/{id}/flag', [ReviewController::class, 'flag']);

    // Internal Communications Pipeline Management
    Route::prefix('messages')->group(function () {
        Route::get('/{orderId}', [MessageController::class, 'getMessages']);
        Route::post('/', [MessageController::class, 'sendMessage']);
        Route::patch('/{orderId}/read', [MessageController::class, 'markAsRead']);
        Route::patch('/{orderId}/lock', [MessageController::class, 'lockChat']);
    });

    /*
    |--------------------------------------------------------------------------
    | Administrative Gate Oversight Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('can:admin-access')->group(function () {
        Route::get('/metrics', [AdminController::class, 'getMetrics']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/gigs', [AdminController::class, 'getGigs']);
        Route::get('/orders', [AdminController::class, 'getOrders']);
        Route::get('/reviews/flagged', [AdminController::class, 'getFlaggedReviews']);
        
        Route::patch('/users/{id}/toggle-ban', [AdminController::class, 'toggleUserBan']);
        Route::patch('/gigs/{id}/approve', [AdminController::class, 'approveGig']);
        Route::patch('/reviews/{id}/moderate', [AdminController::class, 'moderateReview']);
        Route::delete('/gigs/{id}', [AdminController::class, 'deleteGig']);
    });
});