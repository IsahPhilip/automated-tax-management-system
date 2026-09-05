<?php
declare(strict_types=1);
function is_api_request():bool{$p=parse_url($_SERVER['REQUEST_URI']??'',PHP_URL_PATH)?:'';return str_starts_with($p,'/api/')||str_contains($_SERVER['HTTP_ACCEPT']??'','application/json');}
function json_response(array $data,int $status=200):never{http_response_code($status);header('Content-Type: application/json; charset=utf-8');echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function success_response(mixed $data=null,string $message='Success.',int $status=200):never{json_response(['success'=>true,'message'=>$message,'data'=>$data],$status);}
function error_response(string $message,int $status=400,array $errors=[]):never{json_response(['success'=>false,'message'=>$message,'errors'=>$errors],$status);}
function redirect(string $url,int $status=302):never{if(!preg_match('#^https?://#i',$url)){if(function_exists('app_url')){$base=defined('APP_BASE_URL')?APP_BASE_URL:'';$url=$base!==''&&str_starts_with($url,$base)?$url:app_url($url);}else{$url='/'.ltrim($url,'/');}}header('Location: '.$url,true,$status);exit;}
function abort(int $status,string $message=''):never{if(is_api_request())error_response($message?:'Request failed.',$status);http_response_code($status);echo '<h1>'.htmlspecialchars((string)$status,ENT_QUOTES,'UTF-8').'</h1><p>'.htmlspecialchars($message,ENT_QUOTES,'UTF-8').'</p>';exit;}
