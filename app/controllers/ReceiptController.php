<?php
declare(strict_types=1);
namespace App\Controllers;
use App\Models\Receipt;

class ReceiptController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $model = new Receipt();

        $receipts = function_exists('is_taxpayer') && is_taxpayer()
            ? $model->findByUser((int) auth_id())
            : $model->all();

        $this->view('receipts/index', ['receipts' => $receipts]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $receipt = (new Receipt())->find($id);

        if (!$receipt) {
            $this->abort(404, 'Receipt not found.');
        }

        $this->view('receipts/show', ['receipt' => $receipt]);
    }

    public function staffShow(int $id): void
    {
        $this->show($id);
    }
}
