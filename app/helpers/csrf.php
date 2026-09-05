<?php
declare(strict_types=1);
require_once __DIR__.'/session.php';
function csrf_token():string{$t=session_get('_csrf_token');if(!is_string($t)||strlen($t)<32){$t=bin2hex(random_bytes(32));session_set('_csrf_token',$t);}return $t;}
function csrf_field():string{return '<input type="hidden" name="_csrf_token" value="'.htmlspecialchars(csrf_token(),ENT_QUOTES,'UTF-8').'">';}
function verify_csrf_token(?string $token):bool{return is_string($token)&&$token!==''&&hash_equals(csrf_token(),$token);}
function require_csrf():void{$token=$_POST['_csrf_token']??$_SERVER['HTTP_X_CSRF_TOKEN']??null;if(!verify_csrf_token(is_string($token)?$token:null)){require_once __DIR__.'/response.php';abort(419,'Invalid or expired security token.');}}
