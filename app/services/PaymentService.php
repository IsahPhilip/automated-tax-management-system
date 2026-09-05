<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Payment;
use InvalidArgumentException;
use RuntimeException;


class PaymentService {
    public function record(array $data): int {
        $amount = max(0, (float)($data['amount'] ?? 0));
        if ($amount <= 0) throw new InvalidArgumentException('Payment amount must be greater than zero.');
        $data['amount'] = $amount;
        $data['status'] = $data['status'] ?? 'SUCCESS';
        $data['reference'] = $data['reference'] ?? ('ATMS-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(4))));
        $id = (new Payment())->create($data);
        if (!$id) throw new RuntimeException('Unable to record payment.');
        if (function_exists('audit_log')) audit_log('CREATE', 'PAYMENT', (int)$id, 'Payment recorded.');
        return (int)$id;
    }

    public function verify(int $paymentId): bool {
        $p = (new Payment())->find($paymentId);
        return $p && strtoupper((string)($p['status'] ?? '')) === 'SUCCESS';
    }

    public function outstanding(float $assessment, float $paid): float { return round(max(0, $assessment - $paid), 2); }
}