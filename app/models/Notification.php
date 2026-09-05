<?php
declare(strict_types=1);
namespace App\Models;

class Notification extends BaseModel
{
    protected string $table = 'notifications';
    protected string $primaryKey = 'notification_id';

    public function forUser(int $userId,bool $unreadOnly=false): array { return $this->where(['user_id'=>$userId]+($unreadOnly?['is_read'=>0]:[]),'created_at'); }
    public function unreadCount(int $userId): int { return $this->count(['user_id'=>$userId,'is_read'=>0]); }
    public function markRead(int $id): bool { return $this->update($id,['is_read'=>1]); }
    public function markAllRead(int $userId): bool { return $this->execute("UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0",'i',[$userId]); }
}
