<?php
/**
 * Web Routes
 * Define all application routes here
 */

require_once __DIR__ . '/../app/Router.php';

$router = new Router();

// ============================================================================
// API ROUTES
// ============================================================================

// Products
$router->get('/api/products', 'ProductController@index');
$router->get('/api/products/search', 'ProductController@search');
$router->get('/api/products/slug/:slug', 'ProductController@getBySlug');
$router->get('/api/products/:id/variant', 'ProductController@getVariant');
$router->get('/api/products/:id', 'ProductController@show');
$router->get('/api/leagues', 'ProductController@getLeagues');

// Cart
$router->get('/api/cart', 'CartController@index');
$router->post('/api/cart/add', 'CartController@add');
$router->put('/api/cart/update/:itemId', 'CartController@update');
$router->delete('/api/cart/remove/:itemId', 'CartController@remove');
$router->delete('/api/cart/clear', 'CartController@clear');

// Language
$router->post('/api/lang', 'LangController@change');

// Authentication
$router->post('/api/auth/register', 'AuthController@register');
$router->post('/api/auth/login', 'AuthController@login');
$router->post('/api/auth/logout', 'AuthController@logout');
$router->get('/api/auth/logout', 'AuthController@logout');
$router->get('/api/auth/me', 'AuthController@me');
$router->post('/api/auth/social/telegram', 'AuthController@loginTelegram');
$router->post('/api/auth/social/whatsapp', 'AuthController@loginWhatsApp');

// Email Verification
$router->get('/auth/verify-email/:token', 'EmailVerificationController@verify');
$router->post('/api/auth/resend-verification', 'AuthController@resendVerification');

// Orders
$router->get('/api/orders', 'OrderController@index');
$router->get('/api/orders/:id', 'OrderController@show');
$router->post('/api/orders/create', 'OrderController@create');
$router->post('/api/orders/:id/cancel', 'OrderController@cancel');
$router->post('/api/orders/validate-coupon', 'OrderController@validateCoupon');

// Account Orders API
$router->get('/api/account/orders', 'OrderController@index');
$router->get('/api/account/orders/:id', 'OrderController@show');

// Customer
$router->get('/api/customer/profile', 'CustomerController@profile');
$router->put('/api/customer/profile', 'CustomerController@updateProfile');
$router->get('/api/customer/addresses', 'CustomerController@addresses');
$router->post('/api/customer/addresses', 'CustomerController@addAddress');
$router->put('/api/customer/addresses/:id', 'CustomerController@updateAddress');
$router->delete('/api/customer/addresses/:id', 'CustomerController@deleteAddress');
$router->get('/api/customer/preferences', 'CustomerController@preferences');
$router->put('/api/customer/preferences', 'CustomerController@updatePreferences');
$router->get('/api/customer/loyalty', 'CustomerController@loyalty');

// Account - Profile Management
$router->post('/api/account/update-profile', 'CustomerController@updateProfileAPI');
$router->post('/api/account/update-password', 'CustomerController@updatePasswordAPI');

// Account - Address Management
$router->post('/mi-cuenta/perfil/direccion/agregar', 'AccountPageController@addAddress');
$router->post('/mi-cuenta/perfil/direccion/editar/:id', 'AccountPageController@updateAddressData');
$router->post('/mi-cuenta/perfil/direccion/eliminar/:id', 'AccountPageController@deleteAddress');
$router->post('/mi-cuenta/perfil/direccion/predeterminada/:id', 'AccountPageController@setDefaultAddress');

// Account - Address Management API
$router->get('/api/account/addresses', 'CustomerController@addresses');
$router->get('/api/account/addresses/:id', 'CustomerController@getAddress');
$router->post('/api/account/addresses', 'CustomerController@addAddress');
$router->put('/api/account/addresses/:id', 'CustomerController@updateAddress');
$router->delete('/api/account/addresses/:id', 'CustomerController@deleteAddress');
$router->post('/api/account/addresses/:id/set-default', 'CustomerController@setDefaultAddress');

// Payment
$router->post('/api/payment/create', 'PaymentController@create');
$router->post('/api/payment/callback', 'PaymentController@callback');
$router->get('/api/payment/status/:orderId', 'PaymentController@status');

// ============================================================================
// FRONTEND WEB ROUTES
// ============================================================================

// Home
$router->get('/', 'HomeController@index');

// Product pages
$router->get('/productos', 'ProductPageController@index');
$router->get('/productos/:slug', 'ProductPageController@show');

// Mystery Box
$router->get('/mystery-box', 'MysteryBoxController@index');

// League pages
$router->get('/ligas', 'LeaguePageController@index');
$router->get('/ligas/:slug', 'LeaguePageController@show');

// Static pages
$router->get('/como-funciona', 'PageController@howItWorks');
$router->get('/preguntas-frecuentes', 'PageController@faq');
$router->get('/contacto', 'PageController@contact');
$router->get('/nosotros', 'PageController@about');

// User pages
$router->get('/login', 'AuthPageController@login');
$router->get('/register', 'AuthPageController@register');
$router->get('/mi-cuenta', 'AccountPageController@index');
$router->get('/mi-cuenta/perfil', 'AccountPageController@profile');
$router->post('/mi-cuenta/perfil/actualizar', 'AccountPageController@updateProfile');
$router->post('/mi-cuenta/perfil/cambiar-contrasena', 'AccountPageController@updatePassword');
$router->get('/mi-cuenta/suscripciones', 'AccountPageController@subscriptions');
$router->get('/mi-cuenta/suscripciones/:id', 'AccountPageController@subscriptionDetail');
$router->get('/mi-cuenta/direcciones', 'AccountPageController@addresses');
$router->get('/mi-cuenta/pedidos', 'AccountPageController@orders');
$router->get('/mi-cuenta/pedidos/:id', 'AccountPageController@orderDetail');
$router->get('/mis-pedidos', 'AccountPageController@orders');
$router->get('/mis-pedidos/:id', 'AccountPageController@orderDetail');

// Cart & Checkout
$router->get('/carrito', 'CartPageController@index');
$router->get('/checkout', 'CheckoutPageController@index');
$router->get('/checkout/step1', 'CheckoutPageController@step1');
$router->get('/checkout/step2', 'CheckoutPageController@step2');
$router->post('/checkout/process-step2', 'CheckoutPageController@processStep2');
$router->get('/checkout/step3', 'CheckoutPageController@step3');
$router->get('/order-confirmation', 'CheckoutPageController@confirmation');

// Checkout flow
$router->get('/checkout/datos', 'CheckoutPageController@datos');
$router->post('/checkout/procesar-paso-2', 'CheckoutPageController@processStep2');
$router->get('/checkout/resumen', 'CheckoutPageController@resumen');
$router->get('/checkout/pago', 'CheckoutPageController@pago');
$router->get('/checkout/confirmacion', 'CheckoutPageController@confirmacion');
$router->post('/checkout/clear', 'CheckoutPageController@clearCheckout');

// Checkout API - Apply/remove coupon
$router->post('/api/checkout/apply-coupon', 'CheckoutAPIController@applyCoupon');
$router->post('/api/checkout/remove-coupon', 'CheckoutAPIController@removeCoupon');

// Payment API - OxaPay
$router->post('/api/payment/oxapay/create', 'PaymentController@createOxaPayPayment');
$router->post('/api/payment/oxapay/webhook', 'PaymentController@oxaPayWebhook');
$router->get('/api/payment/oxapay/status/:orderId', 'PaymentController@checkOxaPayStatus');

// Payment API - Telegram Manual
$router->post('/api/payment/telegram/create', 'PaymentController@createTelegramPayment');

// ============================================================================
// ADMIN ROUTES (Protected)
// ============================================================================

// Admin Authentication
$router->get('/admin/login', 'AdminAuthController@showLogin');
$router->post('/admin/send-magic-link', 'AdminAuthController@sendMagicLink');
$router->get('/admin/verify/:token', 'AdminAuthController@verifyMagicLink');
$router->get('/admin/logout', 'AdminAuthController@logout');

// Admin Dashboard
$router->get('/admin', 'AdminAuthController@dashboard');
$router->get('/admin/dashboard', 'AdminAuthController@dashboard');

// ============================================================================
// ADMIN CRM - RUTAS EN ESPAÑOL (todas las rutas del CRM usan español)
// ============================================================================

// ---------- CLIENTES ----------
$router->get('/admin/clientes', 'ClientesController@index');
$router->get('/admin/clientes/crear', 'ClientesController@create');
$router->post('/admin/clientes', 'ClientesController@store');
$router->get('/admin/clientes/editar/:id', 'ClientesController@edit');
$router->put('/admin/clientes/:id', 'ClientesController@update');
$router->delete('/admin/clientes/:id', 'ClientesController@delete');
$router->get('/api/admin/clientes', 'api/AdminClientesApiController@getAll');
$router->get('/api/admin/clientes/:id', 'api/AdminClientesApiController@getOne');

// ---------- PEDIDOS ----------
$router->get('/admin/pedidos', 'PedidosController@index');
$router->get('/api/admin/pedidos', 'api/AdminPedidosApiController@getAll');
$router->get('/api/admin/pedidos/:id', 'api/AdminPedidosApiController@getOne');
$router->post('/api/admin/pedidos/:id/status', 'PedidosController@updateStatus');
$router->post('/api/admin/pedidos/:id/payment', 'PedidosController@updatePayment');
$router->post('/api/admin/pedidos/:id/tracking', 'PedidosController@updateTracking');
$router->post('/api/admin/pedidos/:id/cancel', 'PedidosController@cancel');

// ---------- PRODUCTOS ----------
$router->get('/admin/productos', 'ProductosController@index');
$router->get('/admin/productos/crear', 'ProductosController@create');
$router->post('/admin/productos', 'ProductosController@store');
$router->get('/admin/productos/editar/:id', 'ProductosController@edit');
$router->put('/admin/productos/:id', 'ProductosController@update');
$router->delete('/admin/productos/:id', 'ProductosController@delete');
$router->get('/api/admin/productos', 'api/AdminProductosApiController@getAll');
$router->get('/api/admin/productos/:id', 'api/AdminProductosApiController@getOne');

// ---------- LIGAS ----------
$router->get('/admin/ligas', 'LigasController@index');
$router->get('/admin/ligas/crear', 'LigasController@create');
$router->post('/admin/ligas', 'LigasController@store');
$router->get('/admin/ligas/editar/:id', 'LigasController@edit');
$router->put('/admin/ligas/:id', 'LigasController@update');
$router->delete('/admin/ligas/:id', 'LigasController@delete');
$router->get('/api/admin/ligas', 'api/AdminLigasApiController@getAll');
$router->get('/api/admin/ligas/:id', 'api/AdminLigasApiController@getOne');

// ---------- EQUIPOS ----------
$router->get('/admin/equipos', 'EquiposController@index');
$router->get('/admin/equipos/crear', 'EquiposController@create');
$router->post('/admin/equipos', 'EquiposController@store');
$router->get('/admin/equipos/editar/:id', 'EquiposController@edit');
$router->put('/admin/equipos/:id', 'EquiposController@update');
$router->delete('/admin/equipos/:id', 'EquiposController@delete');
$router->get('/api/admin/equipos', 'api/AdminEquiposApiController@getAll');
$router->get('/api/admin/equipos/:id', 'api/AdminEquiposApiController@getOne');

// ---------- SUSCRIPCIONES ----------
$router->get('/admin/suscripciones', 'SuscripcionesController@index');
$router->get('/api/admin/suscripciones', 'api/AdminSuscripcionesApiController@getAll');
$router->get('/api/admin/suscripciones/:id', 'api/AdminSuscripcionesApiController@getOne');
$router->post('/admin/suscripciones/pause/:id', 'SuscripcionesController@pause');
$router->post('/admin/suscripciones/cancel/:id', 'SuscripcionesController@cancel');
$router->post('/admin/suscripciones/reactivate/:id', 'SuscripcionesController@reactivate');

// ---------- PAGOS ----------
$router->get('/admin/pagos', 'PagosController@index');
$router->get('/api/admin/pagos/:id', 'PagosController@show');
$router->post('/api/admin/pagos/:id/status', 'PagosController@updateStatus');
$router->post('/api/admin/pagos/:id/complete', 'PagosController@markAsCompleted');
$router->post('/api/admin/pagos/:id/fail', 'PagosController@markAsFailed');
$router->post('/api/admin/pagos/:id/refund', 'PagosController@processRefund');

// ---------- MYSTERY BOXES ----------
// TODO: Crear MysteryBoxesController
$router->get('/admin/mystery-boxes', 'MysteryBoxesController@index');
$router->get('/api/admin/mystery-boxes/:id', 'MysteryBoxesController@show');

// ---------- CUPONES ----------
$router->get('/admin/cupones', 'CuponesController@index');
$router->get('/admin/cupones/crear', 'CuponesController@create');
$router->post('/admin/cupones', 'CuponesController@store');
$router->get('/admin/cupones/editar/:id', 'CuponesController@edit');
$router->put('/admin/cupones/:id', 'CuponesController@update');
$router->delete('/admin/cupones/:id', 'CuponesController@delete');
$router->get('/api/admin/cupones', 'api/AdminCuponesApiController@getAll');
$router->get('/api/admin/cupones/:id', 'api/AdminCuponesApiController@getOne');

// ---------- INVENTARIO ----------
// TODO: Crear InventarioController
$router->get('/admin/inventario', 'InventarioController@index');
$router->get('/api/admin/inventario/movimientos', 'InventarioController@movements');
$router->get('/api/admin/inventario/alertas', 'InventarioController@lowStockAlerts');

// ---------- ANALYTICS ----------
// TODO: Crear AnalyticsController
$router->get('/admin/analytics', 'AnalyticsController@index');
$router->get('/api/admin/analytics/ingresos', 'AnalyticsController@revenue');
$router->get('/api/admin/analytics/productos', 'AnalyticsController@products');
$router->get('/api/admin/analytics/clientes', 'AnalyticsController@customers');

// ---------- CONFIGURACIÓN ----------
// TODO: Crear ConfiguracionController
$router->get('/admin/configuracion', 'ConfiguracionController@index');
$router->post('/admin/configuracion/actualizar', 'ConfiguracionController@update');

return $router;
