<?php
declare(strict_types=1);
function url(string $path=''):string{
    $base = defined('APP_URL') ? rtrim(APP_URL, '/') : (defined('APP_BASE_URL') ? rtrim(APP_BASE_URL, '/') : '');
    if ($path === '' || preg_match('#^https?://#i', $path)) {
        return $path ?: $base;
    }
    return $base === '' ? '/' . ltrim($path, '/') : $base . '/' . ltrim($path, '/');
}
function asset(string $path):string{return url('/assets/' . ltrim($path, '/'));}
function route_url(string $route,array $params=[]):string{$routes=['login'=>'/login','logout'=>'/logout','dashboard'=>'/dashboard','taxpayer.dashboard'=>'/taxpayer/dashboard','staff.dashboard'=>'/staff/dashboard','admin.dashboard'=>'/admin/dashboard','taxpayers'=>'/taxpayers','declarations'=>'/declarations','assessments'=>'/assessments','payments'=>'/payments','receipts'=>'/receipts','reports'=>'/reports'];$path=$routes[$route]??$route;foreach($params as $k=>$v)$path=str_replace('{'.$k.'}',rawurlencode((string)$v),$path);return url($path);}
