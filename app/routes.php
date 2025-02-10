<?php

// Add these routes to your existing routes
$router->addRoute('GET', '/users/fees/initial', 'UserFeeController', 'showInitialFees');
$router->addRoute('POST', '/users/fees/confirm', 'UserFeeController', 'confirmPayment');
$router->addRoute('GET', '/users/fees/success', 'UserFeeController', 'showSuccess');
$router->addRoute('GET', '/auth/setup-password', 'AuthController', 'showSetupPassword');
$router->addRoute('POST', '/auth/setup-password', 'AuthController', 'setupPassword'); 