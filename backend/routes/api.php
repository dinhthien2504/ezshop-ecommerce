<?php
use App\Http\Controllers\ShippingController;
use Illuminate\Support\Facades\Route;

require_once(__DIR__ . '/fee.php');
require_once(__DIR__ . '/tax.php');
require_once(__DIR__ . '/category.php');
require_once(__DIR__ . '/attribute.php');
require_once(__DIR__ . '/user.php');
require_once(__DIR__ . '/product.php');
require_once(__DIR__ . '/address.php');
require_once(__DIR__ . '/location.php');
require_once(__DIR__ . '/cart_item.php');
require_once(__DIR__ . '/order.php');
require_once(__DIR__ . '/payment.php');
require_once(__DIR__ . '/voucher.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/banner.php');
require_once(__DIR__ . '/role.php');
require_once(__DIR__ . '/mail.php');
require_once(__DIR__ . '/overview.php');
require_once(__DIR__ . '/rank.php');
require_once(__DIR__ . '/chat.php');
require_once(__DIR__ . '/ai.php');
require_once(__DIR__ . '/shop.php');
require_once(__DIR__ . '/rate.php');
require_once(__DIR__ . '/shop.php');
require_once(__DIR__ . '/payout.php');
require_once(__DIR__ . '/violation.php');
require_once(__DIR__ . '/favorites.php');
require_once(__DIR__ . '/wallet.php');
require_once(__DIR__ . '/wallet_transaction.php');
require_once(__DIR__ . '/refund.php');
require_once(__DIR__ . '/refund_item.php');
require_once(__DIR__ . '/notification.php');

//API GHN
Route::post('/calculate-shipping', [ShippingController::class, 'calculateShippingAll']);