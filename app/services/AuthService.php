<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\User;

class AuthService {
    public function attempt(string $email, string $password): bool {
        $user=(new User())->findByEmail(strtolower(trim($email)));
        if(!$user || empty($user['password_hash']) || !password_verify($password,$user['password_hash']) || ($user['status']??'ACTIVE')!=='ACTIVE') return false;
        if(session_status()!==PHP_SESSION_ACTIVE) session_start();
        session_regenerate_id(true);
        $_SESSION['user_id']=(int)$user['user_id']; $_SESSION['role_id']=isset($user['role_id'])?(int)$user['role_id']:null; $_SESSION['user']=$user;
        if(function_exists('audit_log')) audit_log('LOGIN','AUTH',(int)$user['user_id'],'Successful login.');
        return true;
    }
    public function logout(): void {
        $id=function_exists('auth_id')?auth_id():null;
        if(function_exists('audit_log') && $id) audit_log('LOGOUT','AUTH',(int)$id,'User logged out.');
        if(session_status()===PHP_SESSION_ACTIVE){ $_SESSION=[]; session_destroy(); }
    }
    public function user(): ?array {
        if(!function_exists('auth_id') || !auth_id()) return null;
        return (new User())->find((int)auth_id()) ?: null;
    }
    public function changePassword(int $userId,string $current,string $new): bool {
        $m=new User(); $u=$m->find($userId);
        if(!$u || !password_verify($current,(string)$u['password_hash']) || strlen($new)<8) return false;
        if(!$m->update($userId,['password_hash'=>password_hash($new,PASSWORD_DEFAULT)])) return false;
        if(function_exists('audit_log')) audit_log('PASSWORD_CHANGE','AUTH',$userId,'Password changed.');
        return true;
    }
}