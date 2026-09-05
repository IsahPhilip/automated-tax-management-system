<?php
declare(strict_types=1);
function start_secure_session(): void { if (session_status()===PHP_SESSION_ACTIVE) return; $https=!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'; session_name(defined('SESSION_NAME')?SESSION_NAME:'ATMS_SESSION'); session_set_cookie_params(['lifetime'=>defined('SESSION_LIFETIME')?(int)SESSION_LIFETIME:3600,'path'=>'/','secure'=>$https,'httponly'=>true,'samesite'=>'Lax']); ini_set('session.use_only_cookies','1'); ini_set('session.use_strict_mode','1'); session_start(); }
function session_get(string $key,mixed $default=null): mixed{return $_SESSION[$key]??$default;}
function session_set(string $key,mixed $value):void{$_SESSION[$key]=$value;}
function session_has(string $key):bool{return array_key_exists($key,$_SESSION);}
function session_forget(string $key):void{unset($_SESSION[$key]);}
function session_flash(string $key,mixed $value):void{$_SESSION['_flash'][$key]=$value;}
function session_pull_flash(string $key,mixed $default=null):mixed{$v=$_SESSION['_flash'][$key]??$default;unset($_SESSION['_flash'][$key]);return $v;}
function destroy_session():void{if(session_status()!==PHP_SESSION_ACTIVE)return;$_SESSION=[];session_destroy();}
