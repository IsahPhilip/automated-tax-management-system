<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Notification;
use RuntimeException;


class NotificationService {
    public function send(int $userId, string $title, string $message, string $type = 'INFO'): int {
        $id = (new Notification())->create([
            'user_id' => $userId,
            'title' => trim($title),
            'message' => trim($message),
            'type' => strtoupper($type),
            'created_by' => function_exists('auth_id') ? auth_id() : null,
        ]);
        if (!$id) throw new RuntimeException('Unable to create notification.');
        return (int)$id;
    }

    public function taxAssessmentReady(int $userId, string $assessmentNo, float $amount): int {
        return $this->send($userId, 'Tax Assessment Available', "Assessment $assessmentNo has been generated. Amount due: ₦" . number_format($amount, 2) . '.', 'ASSESSMENT');
    }

    public function paymentConfirmed(int $userId, string $reference, float $amount): int {
        return $this->send($userId, 'Payment Confirmed', "Payment $reference of ₦" . number_format($amount, 2) . ' has been confirmed.', 'PAYMENT');
    }
}