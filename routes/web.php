<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliveryMethodController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PostageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesCampaignController;
use App\Http\Controllers\SellerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login');
    }
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    /**
     * User Profile
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /**
     * Inventory
     */
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/edit-product/{product}', [ProductController::class, 'edit'])->name('product.edit');
    Route::get('/add-product', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{product}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{product}', [ProductController::class, 'destroy'])->name('product.destroy');

    /**
     * Inventory Logs
     */
    Route::get('/inventory-logs/{product}', [InventoryLogController::class, 'index'])->name('inventoryLogs.index');
    Route::get('/add-inventory-logs/{product}', [InventoryLogController::class, 'create'])->name('inventoryLogs.create');
    Route::post('/inventory-logs/{product}', [InventoryLogController::class, 'store'])->name('inventoryLogs.store');

    /**
     * Packages
     */
    Route::get('/packages/{product}', [PackageController::class, 'index'])->name('package.index');
    Route::get('/add-package/{product}', [PackageController::class, 'create'])->name('package.create');
    Route::post('/package', [PackageController::class, 'store'])->name('package.store');

    /**
     * Orders
     */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/order/{order_id}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/add-order', [OrderController::class, 'create'])->name('order.create');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::put('/approve-order/{order}', [OrderController::class, 'approveOrder'])->name('order.approve');
    Route::put('/cancel-order/{order}', [OrderController::class, 'cancelOrder'])->name('order.cancel');
    Route::put('/reject-order/{order}', [OrderController::class, 'rejectOrder'])->name('order.reject');
    Route::put('/complete-order/{order}', [OrderController::class, 'completeOrder'])->name('order.complete');
    Route::put('/return-order/{order}', [OrderController::class, 'returnOrder'])->name('order.return');
    Route::put('/in-transit-order/{order}', [OrderController::class, 'inTransitOrder'])->name('order.inTransit');

    /**
     * Delivery Methods
     */
    Route::get('/delivery', [DeliveryMethodController::class, 'index'])->name('delivery.index');
    Route::post('/delivery', [DeliveryMethodController::class, 'store'])->name('delivery.store');
    Route::put('/delivery/{deliveryMethod}', [DeliveryMethodController::class, 'update'])->name('delivery.update');
    Route::put('/deactivate-delivery/{deliveryMethod}', [DeliveryMethodController::class, 'deactivate'])->name('delivery.deactivate');
    Route::put('/activate-delivery/{deliveryMethod}', [DeliveryMethodController::class, 'activate'])->name('delivery.activate');

    /**
     * Campaigns
     */
    Route::get('/campaigns', [SalesCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/add-campaign', [SalesCampaignController::class, 'create'])->name('campaign.create');
    Route::post('/campaign', [SalesCampaignController::class, 'store'])->name('campaign.store');

    /**
     * Customers
     */
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/add-customer', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');

    /**
     * Sellers
     */
    Route::get('/sellers', [SellerController::class, 'index'])->name('sellers.index');
    Route::get('/add-seller', [SellerController::class, 'create'])->name('seller.create');
    Route::post('/seller', [SellerController::class, 'store'])->name('seller.store');

    /**
     * Postage
     */
    Route::get('/postages', [PostageController::class, 'index'])->name('postages.index');

    /**
     * Announcement
     */
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::put('/activate-announcement/{announcement}', [AnnouncementController::class, 'activateAnnouncement'])->name('announcement.activate');
    Route::put('/deactivate-announcement/{announcement}', [AnnouncementController::class, 'deactivateAnnouncement'])->name('announcement.deactivate');
    Route::delete('/announcement/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');
});

Route::get('/inventory', function () {
    return Inertia::render('Inventory/Index');
})->middleware('auth')->name('inventory.index');


require __DIR__ . '/auth.php';
