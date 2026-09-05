<?php
declare(strict_types=1);
function request_method():string{return strtoupper($_SERVER['REQUEST_METHOD']??'GET');}
function is_method(string $method):bool{return request_method()===strtoupper($method);}
function request_input(?string $key=null,mixed $default=null):mixed{$input=$_POST;if(str_contains($_SERVER['CONTENT_TYPE']??'','application/json')){$json=json_decode(file_get_contents('php://input')?:'',true);if(is_array($json))$input=$json;}return $key===null?$input:($input[$key]??$default);}
function query(?string $key=null,mixed $default=null):mixed{return $key===null?$_GET:($_GET[$key]??$default);}
function input_string(string $key,string $default=''):string{$v=request_input($key,$default);return is_scalar($v)?trim((string)$v):$default;}
function input_int(string $key,?int $default=null):?int{$v=request_input($key,$default);return ($v!==null&&$v!==''&&filter_var($v,FILTER_VALIDATE_INT)!==false)?(int)$v:$default;}
function input_float(string $key,?float $default=null):?float{$v=request_input($key,$default);return ($v!==null&&$v!==''&&is_numeric($v))?(float)$v:$default;}
