<?php
declare(strict_types=1);
require_once __DIR__.'/session.php';
function flash(string $type,string $message):void{session_flash('message',['type'=>$type,'message'=>$message]);}
function flash_success(string $message):void{flash('success',$message);} function flash_error(string $message):void{flash('error',$message);} function flash_warning(string $message):void{flash('warning',$message);} function flash_info(string $message):void{flash('info',$message);}
function get_flash():?array{$v=session_pull_flash('message');return is_array($v)?$v:null;}
