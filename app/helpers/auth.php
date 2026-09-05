<?php
declare(strict_types=1);
require_once __DIR__.'/session.php';
function auth_user():?array{$u=session_get('auth_user');return is_array($u)?$u:null;}
function auth_id():?int{$v=session_get('auth_user_id');return $v===null?null:(int)$v;}
function auth_role_id():?int{$v=session_get('auth_role_id');return $v===null?null:(int)$v;}
function auth_role():?string{$v=session_get('auth_role');return $v===null?null:(string)$v;}
function is_authenticated():bool{return auth_id()!==null&&auth_user()!==null;}
function login_user(array $user):void{start_secure_session();session_regenerate_id(true);session_set('auth_user_id',(int)($user['user_id']??0));session_set('auth_role_id',(int)($user['role_id']??0));session_set('auth_role',(string)($user['role_name']??$user['role']??''));session_set('auth_user',$user);session_set('auth_login_at',time());session_set('auth_last_activity',time());}
function logout_user():void{destroy_session();}
function has_role(string|int|array $roles):bool{if(!is_authenticated())return false;$current=strtolower((string)auth_role());$currentId=auth_role_id();foreach((array)$roles as $role){if(is_numeric($role)&&$currentId===(int)$role)return true;if($current===strtolower((string)$role))return true;}return false;}
function is_admin():bool{return has_role([1,'SYSTEM_ADMINISTRATOR','SYSTEM ADMINISTRATOR','ADMIN']);}
function is_revenue_officer():bool{return has_role([1,2,'REVENUE_OFFICER','REVENUE OFFICER','REVENUE/TAX OFFICER','TAX_OFFICER','TAX OFFICER','SYSTEM_ADMINISTRATOR','ADMIN']);}
function is_staff():bool{return is_admin()||is_revenue_officer();}
function is_taxpayer():bool{return has_role([3,'TAXPAYER']);}
function require_auth():void{if(!is_authenticated()){require_once __DIR__.'/response.php';redirect(app_url('/login'));}}
function require_role(string|int|array $roles):void{require_auth();if(!has_role($roles)){require_once __DIR__.'/response.php';abort(403,'You are not authorized to access this resource.');}}
function touch_auth_session():void{if(is_authenticated())session_set('auth_last_activity',time());}
