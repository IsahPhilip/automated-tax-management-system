<?php
declare(strict_types=1);

/** JSON API routes for the Automated Tax Management System. */
require_once PROJECT_ROOT . '/app/helpers/auth.php';

$GLOBALS['api_routes'] = [];

function api_route(string $method, string $uri, string $handler, array $options = []): void
{
    $GLOBALS['api_routes'][] = [
        'method' => strtoupper($method),
        'uri' => '/' . trim($uri, '/'),
        'handler' => $handler,
        'middleware' => $options['middleware'] ?? [],
        'name' => $options['name'] ?? null,
    ];
}

// Authentication
api_route('POST', '/auth/login', 'ApiAuthController@login', ['name' => 'api.auth.login']);
api_route('POST', '/auth/logout', 'ApiAuthController@logout', ['middleware' => ['api.auth'], 'name' => 'api.auth.logout']);
api_route('GET', '/auth/me', 'ApiAuthController@me', ['middleware' => ['api.auth'], 'name' => 'api.auth.me']);

// Taxpayer
api_route('GET', '/taxpayer/profile', 'ApiTaxpayerController@profile', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.profile']);
api_route('PUT', '/taxpayer/profile', 'ApiTaxpayerController@updateProfile', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.profile.update']);
api_route('GET', '/taxpayer/declarations', 'ApiDeclarationController@index', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.declarations']);
api_route('POST', '/taxpayer/declarations', 'ApiDeclarationController@store', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.declarations.store']);
api_route('GET', '/taxpayer/declarations/{id}', 'ApiDeclarationController@show', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.declarations.show']);
api_route('POST', '/taxpayer/declarations/{id}/submit', 'ApiDeclarationController@submit', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.declarations.submit']);
api_route('GET', '/taxpayer/assessments', 'ApiAssessmentController@index', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.assessments']);
api_route('GET', '/taxpayer/assessments/{id}', 'ApiAssessmentController@show', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.assessments.show']);
api_route('GET', '/taxpayer/payments', 'ApiPaymentController@index', ['middleware' => ['api.auth','api.taxpayer'], 'name' => 'api.taxpayer.payments']);

// Staff
api_route('GET', '/taxpayers', 'ApiTaxpayerController@index', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.taxpayers.index']);
api_route('GET', '/taxpayers/{id}', 'ApiTaxpayerController@show', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.taxpayers.show']);
api_route('POST', '/taxpayers', 'ApiTaxpayerController@store', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.taxpayers.store']);
api_route('PUT', '/taxpayers/{id}', 'ApiTaxpayerController@update', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.taxpayers.update']);
api_route('GET', '/declarations', 'ApiDeclarationController@staffIndex', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.declarations.index']);
api_route('GET', '/declarations/{id}', 'ApiDeclarationController@staffShow', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.declarations.show']);
api_route('POST', '/declarations/{id}/approve', 'ApiDeclarationController@approve', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.declarations.approve']);
api_route('POST', '/declarations/{id}/reject', 'ApiDeclarationController@reject', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.declarations.reject']);
api_route('GET', '/assessments', 'ApiAssessmentController@index', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.assessments.index']);
api_route('POST', '/assessments/calculate', 'ApiAssessmentController@calculate', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.assessments.calculate']);
api_route('POST', '/assessments', 'ApiAssessmentController@store', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.assessments.store']);
api_route('GET', '/assessments/{id}', 'ApiAssessmentController@show', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.assessments.show']);
api_route('POST', '/assessments/{id}/approve', 'ApiAssessmentController@approve', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.assessments.approve']);
api_route('GET', '/payments', 'ApiPaymentController@index', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.payments.index']);
api_route('POST', '/payments', 'ApiPaymentController@store', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.payments.store']);
api_route('POST', '/payments/{id}/verify', 'ApiPaymentController@verify', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.payments.verify']);

// Reports
api_route('GET', '/reports/revenue', 'ApiReportController@revenue', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.reports.revenue']);
api_route('GET', '/reports/taxpayers', 'ApiReportController@taxpayers', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.reports.taxpayers']);
api_route('GET', '/reports/assessments', 'ApiReportController@assessments', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.reports.assessments']);
api_route('GET', '/reports/payments', 'ApiReportController@payments', ['middleware' => ['api.auth','api.staff'], 'name' => 'api.reports.payments']);

// Administrator
api_route('GET', '/admin/users', 'ApiAdminUserController@index', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.users']);
api_route('POST', '/admin/users', 'ApiAdminUserController@store', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.users.store']);
api_route('GET', '/admin/tax-rules', 'ApiTaxRuleController@index', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.tax-rules']);
api_route('POST', '/admin/tax-rules', 'ApiTaxRuleController@store', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.tax-rules.store']);
api_route('GET', '/admin/penalties', 'ApiPenaltyRuleController@index', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.penalties']);
api_route('GET', '/admin/interest-rates', 'ApiInterestRateController@index', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.interest-rates']);
api_route('POST', '/admin/interest-rates', 'ApiInterestRateController@store', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.interest-rates.store']);
api_route('GET', '/admin/audit-logs', 'ApiAuditLogController@index', ['middleware' => ['api.auth','api.admin'], 'name' => 'api.admin.audit-logs']);

function api_response(mixed $data = null, int $status = 200, ?string $message = null): never
{
    http_response_code($status);
    $response = ['success' => $status >= 200 && $status < 300];
    if ($message !== null) $response['message'] = $message;
    if ($data !== null) $response['data'] = $data;
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') return [];
    $data = json_decode($raw, true);
    if (!is_array($data)) api_response(null, 400, 'Invalid JSON request body.');
    return $data;
}

function compile_api_route_pattern(string $uri): string
{
    $pattern = preg_replace_callback('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', static fn(array $m): string => '(?P<' . $m[1] . '>[^/]+)', $uri);
    return '#^' . $pattern . '$#';
}

function run_api_middleware(array $middleware): void
{
    foreach ($middleware as $item) {
        if ($item === 'api.auth' && !is_authenticated()) api_response(null, 401, 'Authentication required.');
        if ($item === 'api.taxpayer' && !has_role(ROLE_TAXPAYER)) api_response(null, 403, 'Taxpayer access required.');
        if ($item === 'api.staff' && !is_staff()) api_response(null, 403, 'Staff access required.');
        if ($item === 'api.admin' && !has_role(ROLE_ADMIN)) api_response(null, 403, 'Administrator access required.');
    }
}

function dispatch_api(string $method, string $uri): void
{
    foreach ($GLOBALS['api_routes'] as $route) {
        if ($route['method'] !== strtoupper($method)) continue;
        if (!preg_match(compile_api_route_pattern($route['uri']), $uri, $matches)) continue;

        $params = [];
        foreach ($matches as $key => $value) if (!is_int($key)) $params[$key] = $value;
        run_api_middleware($route['middleware']);

        [$controllerName, $action] = array_pad(explode('@', $route['handler'], 2), 2, null);
        $controllerFile = PROJECT_ROOT . '/app/controllers/' . $controllerName . '.php';
        if (!is_file($controllerFile)) api_response(null, 500, 'API controller not found.');
        require_once $controllerFile;
        if (!class_exists($controllerName)) api_response(null, 500, 'API controller class not found.');
        $controller = new $controllerName();
        if (!method_exists($controller, $action)) api_response(null, 500, 'API controller action not found.');

        $reflection = new ReflectionMethod($controller, $action);
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $params)) $arguments[] = $params[$name];
            elseif ($parameter->isDefaultValueAvailable()) $arguments[] = $parameter->getDefaultValue();
            else $arguments[] = null;
        }
        $result = $reflection->invokeArgs($controller, $arguments);
        if ($result !== null) api_response($result);
        return;
    }
    api_response(null, 404, 'API endpoint not found.');
}