<?php
declare(strict_types=1);

/** Web routes for the Automated Tax Management System. */
require_once PROJECT_ROOT . '/app/helpers/auth.php';

$GLOBALS['web_routes'] = [];

function web_route(string $method, string $uri, string $handler, array $options = []): void
{
    $GLOBALS['web_routes'][] = [
        'method' => strtoupper($method),
        'uri' => '/' . trim($uri, '/'),
        'handler' => $handler,
        'middleware' => $options['middleware'] ?? [],
        'name' => $options['name'] ?? null,
    ];
}

// Public
web_route('GET', '/', 'HomeController@index', ['name' => 'home']);
web_route('GET', '/login', 'AuthController@showLogin', ['name' => 'login']);
web_route('POST', '/login', 'AuthController@login', ['name' => 'login.submit']);
web_route('POST', '/logout', 'AuthController@logout', ['middleware' => ['auth','csrf'], 'name' => 'logout']);

// Authenticated
web_route('GET', '/dashboard', 'DashboardController@index', ['middleware' => ['auth'], 'name' => 'dashboard']);

// Taxpayer portal
web_route('GET', '/taxpayer/dashboard', 'TaxpayerDashboardController@index', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.dashboard']);
web_route('GET', '/taxpayer/profile', 'TaxpayerController@profile', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.profile']);
web_route('POST', '/taxpayer/profile', 'TaxpayerController@updateProfile', ['middleware' => ['auth','role:3','csrf'], 'name' => 'taxpayer.profile.update']);
web_route('GET', '/taxpayer/declarations', 'DeclarationController@index', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.declarations']);
web_route('GET', '/taxpayer/declarations/create', 'DeclarationController@create', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.declarations.create']);
web_route('POST', '/taxpayer/declarations', 'DeclarationController@store', ['middleware' => ['auth','role:3','csrf'], 'name' => 'taxpayer.declarations.store']);
web_route('GET', '/taxpayer/declarations/{id}', 'DeclarationController@show', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.declarations.show']);
web_route('POST', '/taxpayer/declarations/{id}/submit', 'DeclarationController@submit', ['middleware' => ['auth','role:3','csrf'], 'name' => 'taxpayer.declarations.submit']);
web_route('GET', '/taxpayer/assessments', 'AssessmentController@taxpayerIndex', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.assessments']);
web_route('GET', '/taxpayer/assessments/{id}', 'AssessmentController@taxpayerShow', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.assessments.show']);
web_route('GET', '/taxpayer/payments', 'PaymentController@taxpayerIndex', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.payments']);
web_route('GET', '/taxpayer/receipts/{id}', 'ReceiptController@show', ['middleware' => ['auth','role:3'], 'name' => 'taxpayer.receipt']);

// Staff: role 2 Revenue Officer and role 1 Administrator
web_route('GET', '/staff/dashboard', 'StaffDashboardController@index', ['middleware' => ['auth','staff'], 'name' => 'staff.dashboard']);
web_route('GET', '/taxpayers', 'TaxpayerController@index', ['middleware' => ['auth','staff'], 'name' => 'taxpayers.index']);
web_route('GET', '/taxpayers/create', 'TaxpayerController@create', ['middleware' => ['auth','staff'], 'name' => 'taxpayers.create']);
web_route('POST', '/taxpayers', 'TaxpayerController@store', ['middleware' => ['auth','staff','csrf'], 'name' => 'taxpayers.store']);
web_route('GET', '/taxpayers/{id}', 'TaxpayerController@show', ['middleware' => ['auth','staff'], 'name' => 'taxpayers.show']);
web_route('GET', '/taxpayers/{id}/edit', 'TaxpayerController@edit', ['middleware' => ['auth','staff'], 'name' => 'taxpayers.edit']);
web_route('POST', '/taxpayers/{id}/update', 'TaxpayerController@update', ['middleware' => ['auth','staff','csrf'], 'name' => 'taxpayers.update']);

web_route('GET', '/declarations', 'DeclarationController@staffIndex', ['middleware' => ['auth','staff'], 'name' => 'declarations.index']);
web_route('GET', '/declarations/create', 'DeclarationController@create', ['middleware' => ['auth','staff'], 'name' => 'declarations.create']);
web_route('POST', '/declarations/store', 'DeclarationController@store', ['middleware' => ['auth','staff','csrf'], 'name' => 'declarations.store']);
web_route('GET', '/declarations/{id}', 'DeclarationController@staffShow', ['middleware' => ['auth','staff'], 'name' => 'declarations.show']);
web_route('POST', '/declarations/{id}/approve', 'DeclarationController@approve', ['middleware' => ['auth','staff','csrf'], 'name' => 'declarations.approve']);
web_route('POST', '/declarations/{id}/reject', 'DeclarationController@reject', ['middleware' => ['auth','staff','csrf'], 'name' => 'declarations.reject']);

web_route('GET', '/assessments', 'AssessmentController@index', ['middleware' => ['auth','staff'], 'name' => 'assessments.index']);
web_route('GET', '/assessments/create', 'AssessmentController@create', ['middleware' => ['auth','staff'], 'name' => 'assessments.create']);
web_route('POST', '/assessments/calculate', 'AssessmentController@calculate', ['middleware' => ['auth','staff','csrf'], 'name' => 'assessments.calculate']);
web_route('POST', '/assessments', 'AssessmentController@store', ['middleware' => ['auth','staff','csrf'], 'name' => 'assessments.store']);
web_route('GET', '/assessments/{id}', 'AssessmentController@show', ['middleware' => ['auth','staff'], 'name' => 'assessments.show']);
web_route('POST', '/assessments/{id}/approve', 'AssessmentController@approve', ['middleware' => ['auth','staff','csrf'], 'name' => 'assessments.approve']);
web_route('POST', '/assessments/{id}/issue', 'AssessmentController@issue', ['middleware' => ['auth','staff','csrf'], 'name' => 'assessments.issue']);
web_route('POST', '/assessments/{id}/cancel', 'AssessmentController@cancel', ['middleware' => ['auth','role:1','csrf'], 'name' => 'assessments.cancel']);

web_route('GET', '/payments', 'PaymentController@index', ['middleware' => ['auth','staff'], 'name' => 'payments.index']);
web_route('GET', '/payments/create', 'PaymentController@create', ['middleware' => ['auth'], 'name' => 'payments.create']);
web_route('POST', '/payments', 'PaymentController@store', ['middleware' => ['auth','csrf'], 'name' => 'payments.store']);
web_route('POST', '/payments/{id}/verify', 'PaymentController@verify', ['middleware' => ['auth','staff','csrf'], 'name' => 'payments.verify']);
web_route('GET', '/payments/{id}', 'PaymentController@show', ['middleware' => ['auth'], 'name' => 'payments.show']);
web_route('GET', '/receipts/{id}', 'ReceiptController@staffShow', ['middleware' => ['auth','staff'], 'name' => 'receipts.show']);

web_route('GET', '/declarations/create', 'DeclarationController@create', ['middleware' => ['auth','staff'], 'name' => 'declarations.create']);
web_route('POST', '/declarations/store', 'DeclarationController@store', ['middleware' => ['auth','staff','csrf'], 'name' => 'declarations.store']);

// Taxpayer self service receipts list (index distinguishes roles internally)
web_route('GET', '/receipts', 'ReceiptController@index', ['middleware' => ['auth'], 'name' => 'receipts.list']);

// Reports
web_route('GET', '/reports', 'ReportController@index', ['middleware' => ['auth','staff'], 'name' => 'reports.index']);
web_route('GET', '/reports/revenue', 'ReportController@revenue', ['middleware' => ['auth','staff'], 'name' => 'reports.revenue']);
web_route('GET', '/reports/taxpayers', 'ReportController@taxpayers', ['middleware' => ['auth','staff'], 'name' => 'reports.taxpayers']);
web_route('GET', '/reports/assessments', 'ReportController@assessments', ['middleware' => ['auth','staff'], 'name' => 'reports.assessments']);
web_route('GET', '/reports/payments', 'ReportController@payments', ['middleware' => ['auth','staff'], 'name' => 'reports.payments']);
web_route('GET', '/reports/payments/export', 'ReportController@exportPayments', ['middleware' => ['auth','staff'], 'name' => 'reports.payments.export']);

// Administrator quick links (routes used by redirects / sidebar)
web_route('GET', '/users', 'UserController@index', ['middleware' => ['auth','role:1'], 'name' => 'users.index']);
web_route('GET', '/users/create', 'UserController@create', ['middleware' => ['auth','role:1'], 'name' => 'users.create']);
web_route('POST', '/users', 'UserController@store', ['middleware' => ['auth','role:1','csrf'], 'name' => 'users.store']);
web_route('GET', '/users/{id}', 'UserController@show', ['middleware' => ['auth','role:1'], 'name' => 'users.show']);
web_route('GET', '/users/{id}/edit', 'UserController@edit', ['middleware' => ['auth','role:1'], 'name' => 'users.edit']);
web_route('POST', '/users/{id}/update', 'UserController@update', ['middleware' => ['auth','role:1','csrf'], 'name' => 'users.update']);
web_route('POST', '/users/{id}/activate', 'UserController@activate', ['middleware' => ['auth','role:1','csrf'], 'name' => 'users.activate']);
web_route('POST', '/users/{id}/deactivate', 'UserController@deactivate', ['middleware' => ['auth','role:1','csrf'], 'name' => 'users.deactivate']);

web_route('GET', '/tax-rules', 'TaxRuleController@index', ['middleware' => ['auth','role:1'], 'name' => 'taxrules.index']);
web_route('GET', '/tax-rules/create', 'TaxRuleController@create', ['middleware' => ['auth','role:1'], 'name' => 'taxrules.create']);
web_route('POST', '/tax-rules', 'TaxRuleController@store', ['middleware' => ['auth','role:1','csrf'], 'name' => 'taxrules.store']);
web_route('GET', '/tax-rules/{id}', 'TaxRuleController@show', ['middleware' => ['auth','role:1'], 'name' => 'taxrules.show']);
web_route('GET', '/tax-rules/{id}/edit', 'TaxRuleController@edit', ['middleware' => ['auth','role:1'], 'name' => 'taxrules.edit']);
web_route('POST', '/tax-rules/{id}/update', 'TaxRuleController@update', ['middleware' => ['auth','role:1','csrf'], 'name' => 'taxrules.update']);
web_route('POST', '/tax-rules/{id}/deactivate', 'TaxRuleController@deactivate', ['middleware' => ['auth','role:1','csrf'], 'name' => 'taxrules.deactivate']);

web_route('GET', '/tax-types', 'TaxTypeController@index', ['middleware' => ['auth','role:1'], 'name' => 'taxtypes.index']);
web_route('GET', '/tax-types/create', 'TaxTypeController@create', ['middleware' => ['auth','role:1'], 'name' => 'taxtypes.create']);
web_route('POST', '/tax-types', 'TaxTypeController@store', ['middleware' => ['auth','role:1','csrf'], 'name' => 'taxtypes.store']);
web_route('GET', '/tax-types/{id}', 'TaxTypeController@show', ['middleware' => ['auth','role:1'], 'name' => 'taxtypes.show']);
web_route('GET', '/tax-types/{id}/edit', 'TaxTypeController@edit', ['middleware' => ['auth','role:1'], 'name' => 'taxtypes.edit']);
web_route('POST', '/tax-types/{id}/update', 'TaxTypeController@update', ['middleware' => ['auth','role:1','csrf'], 'name' => 'taxtypes.update']);
web_route('POST', '/tax-types/{id}/deactivate', 'TaxTypeController@deactivate', ['middleware' => ['auth','role:1','csrf'], 'name' => 'taxtypes.deactivate']);

web_route('GET', '/notifications', 'NotificationController@index', ['middleware' => ['auth'], 'name' => 'notifications.index']);
web_route('GET', '/notifications/send', 'NotificationController@sendForm', ['middleware' => ['auth','staff'], 'name' => 'notifications.send.form']);
web_route('POST', '/notifications/send', 'NotificationController@send', ['middleware' => ['auth','staff','csrf'], 'name' => 'notifications.send']);
web_route('POST', '/notifications/read-all', 'NotificationController@readAll', ['middleware' => ['auth','csrf'], 'name' => 'notifications.readall']);
web_route('POST', '/notifications/{id}/read', 'NotificationController@read', ['middleware' => ['auth','csrf'], 'name' => 'notifications.read']);
web_route('GET', '/notifications/{id}', 'NotificationController@show', ['middleware' => ['auth'], 'name' => 'notifications.show']);

web_route('GET', '/admin/audit-logs/export', 'AuditLogController@export', ['middleware' => ['auth','role:1'], 'name' => 'admin.audit-logs.export']);

// Administrator
web_route('GET', '/admin', 'AdminController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.dashboard']);
web_route('GET', '/admin/users', 'AdminUserController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.users']);
web_route('GET', '/admin/users/create', 'AdminUserController@create', ['middleware' => ['auth','role:1'], 'name' => 'admin.users.create']);
web_route('POST', '/admin/users', 'AdminUserController@store', ['middleware' => ['auth','role:1','csrf'], 'name' => 'admin.users.store']);
web_route('POST', '/admin/users/{id}/deactivate', 'AdminUserController@deactivate', ['middleware' => ['auth','role:1','csrf'], 'name' => 'admin.users.deactivate']);
web_route('GET', '/admin/tax-rules', 'TaxRuleController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.tax-rules']);
web_route('GET', '/admin/tax-rules/create', 'TaxRuleController@create', ['middleware' => ['auth','role:1'], 'name' => 'admin.tax-rules.create']);
web_route('POST', '/admin/tax-rules', 'TaxRuleController@store', ['middleware' => ['auth','role:1','csrf'], 'name' => 'admin.tax-rules.store']);
web_route('GET', '/admin/penalties', 'PenaltyRuleController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.penalties']);
web_route('GET', '/admin/interest-rates', 'InterestRateController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.interest-rates']);
web_route('POST', '/admin/interest-rates', 'InterestRateController@store', ['middleware' => ['auth','role:1','csrf'], 'name' => 'admin.interest-rates.store']);
web_route('GET', '/admin/settings', 'AdminSettingsController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.settings']);
web_route('POST', '/admin/settings', 'AdminSettingsController@update', ['middleware' => ['auth','role:1','csrf'], 'name' => 'admin.settings.update']);
web_route('GET', '/admin/audit-logs', 'AuditLogController@index', ['middleware' => ['auth','role:1'], 'name' => 'admin.audit-logs']);

function compile_route_pattern(string $uri): string
{
    $pattern = preg_replace_callback('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', static fn(array $m): string => '(?P<' . $m[1] . '>[^/]+)', $uri);
    return '#^' . $pattern . '$#';
}

function run_web_middleware(array $middleware): void
{
    foreach ($middleware as $item) {
        if ($item === 'csrf') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!verify_csrf_token($token)) {
                http_response_code(419);
                exit('Invalid or expired security token.');
            }
        } elseif ($item === 'auth') {
            if (!is_authenticated()) {
                header('Location: ' . app_url('/login'));
                exit;
            }
        } elseif ($item === 'staff') {
            if (!is_staff()) {
                http_response_code(403);
                exit('Access denied.');
            }
        } elseif (str_starts_with($item, 'role:')) {
            $role = substr($item, 5);
            if (!has_role(is_numeric($role) ? (int) $role : $role)) {
                http_response_code(403);
                exit('Access denied.');
            }
        }
    }
}

function dispatch(string $method, string $uri): void
{
    foreach ($GLOBALS['web_routes'] as $route) {
        if ($route['method'] !== strtoupper($method)) continue;
        if (!preg_match(compile_route_pattern($route['uri']), $uri, $matches)) continue;

        $params = [];
        foreach ($matches as $key => $value) if (!is_int($key)) $params[$key] = $value;
        run_web_middleware($route['middleware']);

        [$controllerName, $action] = array_pad(explode('@', $route['handler'], 2), 2, null);

        // Controllers live under the App\Controllers namespace (see composer.json
        // PSR-4/classmap autoload); allow fully qualified handlers as well.
        $controllerClass = str_contains($controllerName, '\\')
            ? $controllerName
            : 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            $controllerFile = PROJECT_ROOT . '/app/controllers/' . str_replace('\\', '/', $controllerName) . '.php';
            if (!is_file($controllerFile)) throw new RuntimeException('Controller not found: ' . $controllerName);
            require_once $controllerFile;
            if (!class_exists($controllerClass)) throw new RuntimeException('Controller class not found: ' . $controllerName);
        }
        $controller = new $controllerClass();
        if (!method_exists($controller, $action)) throw new RuntimeException('Controller action not found: ' . $controllerName . '@' . $action);

        $reflection = new ReflectionMethod($controller, $action);
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $params)) $arguments[] = $params[$name];
            elseif ($parameter->isDefaultValueAvailable()) $arguments[] = $parameter->getDefaultValue();
            else throw new RuntimeException('Missing route parameter: ' . $name);
        }
        $reflection->invokeArgs($controller, $arguments);
        return;
    }

    http_response_code(404);
    $view = PROJECT_ROOT . '/app/views/errors/404.php';
    if (is_file($view)) require $view; else echo '404 - Page Not Found';
}
