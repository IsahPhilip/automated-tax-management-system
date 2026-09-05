<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\Taxpayer;

class TaxpayerController extends BaseController
{
    public function index(): void
    {
        $this->requireRole([
            1,
            2,
            'REVENUE_OFFICER',
            'REVENUE OFFICER',
            'REVENUE/TAX OFFICER',
            'TAX_OFFICER',
            'TAX OFFICER',
            'SYSTEM_ADMINISTRATOR',
            'ADMIN',
            'SYSTEM ADMINISTRATOR',
        ]);

        $model = new Taxpayer();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $search = trim((string) ($_GET['search'] ?? ''));
        $type = trim((string) ($_GET['type'] ?? ''));

        $this->view('taxpayers/index', [
            'title' => 'Taxpayers',
            'taxpayers' => $model->paginate($page, 20, $search, $type !== '' ? $type : null),
            'search' => $search,
            'type' => $type,
        ]);
    }

    public function show(int $id): void
    {
        $this->requireAuth();

        $taxpayer = (new Taxpayer())->find($id);

        if (!$taxpayer) {
            $this->abort(404, 'Taxpayer not found.');
        }

        if (function_exists('is_taxpayer') && is_taxpayer()) {
            $own = (new Taxpayer())->findByUserId((int) auth_id());

            if (!$own || (int) $own['taxpayer_id'] !== $id) {
                $this->abort(403, 'You are not authorized to view this taxpayer.');
            }
        }

        $this->view('taxpayers/show', ['taxpayer' => $taxpayer]);
    }

    public function create(): void
    {
        $this->requireRole([
            1,
            2,
            'REVENUE_OFFICER',
            'REVENUE OFFICER',
            'REVENUE/TAX OFFICER',
            'TAX_OFFICER',
            'TAX OFFICER',
            'SYSTEM_ADMINISTRATOR',
            'ADMIN',
            'SYSTEM ADMINISTRATOR',
        ]);

        $this->view('taxpayers/create', ['title' => 'Register Taxpayer']);
    }

    public function store(): void
    {
        $this->requireRole([
            1,
            2,
            'REVENUE_OFFICER',
            'REVENUE OFFICER',
            'REVENUE/TAX OFFICER',
            'TAX_OFFICER',
            'TAX OFFICER',
            'SYSTEM_ADMINISTRATOR',
            'ADMIN',
            'SYSTEM ADMINISTRATOR',
        ]);

        require_once PROJECT_ROOT . '/app/helpers/validation.php';
        require_once PROJECT_ROOT . '/app/helpers/security.php';

        $data = $this->input();

        validate_or_throw($data, [
            'taxpayer_type' => ['required'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $taxpayerType = strtoupper((string) ($data['taxpayer_type'] ?? 'INDIVIDUAL'));
        if (!in_array($taxpayerType, ['INDIVIDUAL', 'BUSINESS', 'CORPORATE'], true)) {
            $this->abort(422, 'Invalid taxpayer type.');
        }

        if ($taxpayerType === 'INDIVIDUAL' && (empty($data['first_name']) || empty($data['last_name']))) {
            $this->abort(422, 'First name and last name are required for individual taxpayers.');
        }

        if ($taxpayerType !== 'INDIVIDUAL' && empty($data['business_name'])) {
            $this->abort(422, 'Business name is required for business and corporate taxpayers.');
        }

        $model = new Taxpayer();
        $payload = [
            'taxpayer_number' => $model->generateTaxpayerNumber(),
            'taxpayer_type' => $taxpayerType,
            'first_name' => $this->nullableText($data['first_name'] ?? null),
            'last_name' => $this->nullableText($data['last_name'] ?? null),
            'business_name' => $this->nullableText($data['business_name'] ?? null),
            'email' => sanitize_email((string) $data['email']),
            'phone' => sanitize_text((string) $data['phone'], 30),
            'date_of_birth' => $this->nullableText($data['date_of_birth'] ?? null),
            'identification_type' => $this->nullableText($data['identification_type'] ?? null),
            'identification_number' => $this->nullableText($data['identification_number'] ?? null),
            'address' => $this->nullableText($data['address'] ?? null, 1000),
            'city' => $this->nullableText($data['city'] ?? null),
            'state' => $this->nullableText($data['state'] ?? null),
            'country' => $this->nullableText($data['country'] ?? null) ?: 'Nigeria',
            'annual_turnover' => decimal_amount($data['annual_turnover'] ?? 0),
            'fixed_assets_value' => decimal_amount($data['fixed_assets_value'] ?? 0),
            'status' => 'ACTIVE',
            'registration_date' => date('Y-m-d'),
        ];

        $id = $model->create($payload);

        if (!$id) {
            $this->abort(500, 'Unable to create taxpayer.');
        }

        if (function_exists('audit_log')) {
            audit_log('CREATE', 'TAXPAYER', (int) $id, 'Taxpayer record created.');
        }

        if (function_exists('flash_success')) {
            flash_success('Taxpayer created successfully.');
        }

        $this->redirectTo('/taxpayers/' . $id);
    }

    public function edit(int $id): void
    {
        $this->requireRole([
            1, 2,
            'REVENUE_OFFICER', 'REVENUE OFFICER', 'REVENUE/TAX OFFICER',
            'TAX_OFFICER', 'TAX OFFICER',
            'SYSTEM_ADMINISTRATOR', 'ADMIN', 'SYSTEM ADMINISTRATOR',
        ]);

        $taxpayer = (new Taxpayer())->find($id);
        if (!$taxpayer) {
            $this->abort(404, 'Taxpayer not found.');
        }

        $this->view('taxpayers/edit', ['title' => 'Edit Taxpayer', 'taxpayer' => $taxpayer]);
    }

    public function update(int $id): void
    {
        $this->requireRole([
            1, 2,
            'REVENUE_OFFICER', 'REVENUE OFFICER', 'REVENUE/TAX OFFICER',
            'TAX_OFFICER', 'TAX OFFICER',
            'SYSTEM_ADMINISTRATOR', 'ADMIN', 'SYSTEM ADMINISTRATOR',
        ]);

        require_once PROJECT_ROOT . '/app/helpers/validation.php';
        require_once PROJECT_ROOT . '/app/helpers/security.php';

        $model = new Taxpayer();
        if (!$model->find($id)) {
            $this->abort(404, 'Taxpayer not found.');
        }

        $data = $this->input();
        validate_or_throw($data, [
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        $payload = [
            'first_name' => $this->nullableText($data['first_name'] ?? null),
            'last_name' => $this->nullableText($data['last_name'] ?? null),
            'business_name' => $this->nullableText($data['business_name'] ?? null),
            'email' => sanitize_email((string) $data['email']),
            'phone' => sanitize_text((string) $data['phone'], 30),
            'identification_type' => $this->nullableText($data['identification_type'] ?? null),
            'identification_number' => $this->nullableText($data['identification_number'] ?? null),
            'address' => $this->nullableText($data['address'] ?? null, 1000),
            'city' => $this->nullableText($data['city'] ?? null),
            'state' => $this->nullableText($data['state'] ?? null),
            'annual_turnover' => decimal_amount($data['annual_turnover'] ?? 0),
            'fixed_assets_value' => decimal_amount($data['fixed_assets_value'] ?? 0),
            'status' => strtoupper((string) ($data['status'] ?? 'ACTIVE')) === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE',
        ];

        if (!$model->update($id, $payload)) {
            $this->abort(500, 'Unable to update taxpayer.');
        }

        if (function_exists('audit_log')) {
            audit_log('UPDATE', 'TAXPAYER', $id, 'Taxpayer record updated.');
        }
        if (function_exists('flash_success')) {
            flash_success('Taxpayer updated successfully.');
        }

        $this->redirectTo('/taxpayers/' . $id);
    }

    /** Taxpayer self-service profile screen (README §3). */
    public function profile(): void
    {
        $this->requireRole([3, 'TAXPAYER']);

        $taxpayer = (new Taxpayer())->findByUserId((int) auth_id());

        $this->view('taxpayers/profile', [
            'title' => 'My Profile',
            'taxpayer' => $taxpayer,
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireRole([3, 'TAXPAYER']);
        require_once PROJECT_ROOT . '/app/helpers/validation.php';
        require_once PROJECT_ROOT . '/app/helpers/security.php';

        $own = (new Taxpayer())->findByUserId((int) auth_id());
        if (!$own) {
            $this->abort(422, 'Your taxpayer profile has not been created yet.');
        }

        $data = $this->input();
        validate_or_throw($data, [
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:30'],
        ]);

        (new Taxpayer())->update((int) $own['taxpayer_id'], [
            'first_name' => $this->nullableText($data['first_name'] ?? null),
            'last_name' => $this->nullableText($data['last_name'] ?? null),
            'business_name' => $this->nullableText($data['business_name'] ?? null),
            'email' => sanitize_email((string) $data['email']),
            'phone' => sanitize_text((string) $data['phone'], 30),
            'address' => $this->nullableText($data['address'] ?? null, 1000),
            'city' => $this->nullableText($data['city'] ?? null),
            'state' => $this->nullableText($data['state'] ?? null),
        ]);

        if (function_exists('audit_log')) {
            audit_log('UPDATE', 'TAXPAYER', (int) $own['taxpayer_id'], 'Taxpayer self-service profile updated.');
        }
        if (function_exists('flash_success')) {
            flash_success('Profile updated successfully.');
        }

        $this->redirectTo('/taxpayer/profile');
    }

    private function nullableText(mixed $value, int $maxLength = 255): ?string
    {
        $text = sanitize_text(is_scalar($value) ? (string) $value : '', $maxLength);
        return $text === '' ? null : $text;
    }
}
