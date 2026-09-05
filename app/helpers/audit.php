<?php
declare(strict_types=1);
function audit_log(string $action,string $module,?int $recordId=null,?string $description=null,?int $userId=null):?int{$file=PROJECT_ROOT.'/app/models/AuditLog.php';if(!file_exists($file))return null;require_once $file;try{$logger=new AuditLog();return $logger->record($userId??(function_exists('auth_id')?auth_id():null),$action,$module,$recordId,$description);}catch(Throwable $e){error_log('ATMS audit logging failed: '.$e->getMessage());return null;}}
