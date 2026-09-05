<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\Payment;
use RuntimeException;


class ReceiptService {
    public function generate(int $paymentId): array {
        $p = (new Payment())->find($paymentId);
        if (!$p) throw new RuntimeException('Payment not found.');
        return [
            'receipt_number' => $p['receipt_number'] ?? ('RCT-' . date('Y') . '-' . str_pad((string)$paymentId, 8, '0', STR_PAD_LEFT)),
            'payment_id' => $paymentId,
            'reference' => $p['reference'] ?? null,
            'amount' => (float)($p['amount'] ?? 0),
            'issued_at' => $p['paid_at'] ?? date('Y-m-d H:i:s'),
        ];
    }
}