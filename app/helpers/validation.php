<?php
declare(strict_types=1);

function validate_or_throw(array $data, array $rules): void
{
    $errors = [];

    foreach ($rules as $field => $fieldRules) {
        $value = $data[$field] ?? null;
        $label = str_replace('_', ' ', $field);

        foreach ((array) $fieldRules as $rule) {
            [$name, $argument] = array_pad(explode(':', (string) $rule, 2), 2, null);

            if ($name === 'nullable' && ($value === null || $value === '')) {
                break;
            }

            if ($name === 'required' && ($value === null || $value === '')) {
                $errors[$field][] = ucfirst($label) . ' is required.';
            } elseif ($name === 'email' && $value !== null && $value !== '' && !filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field][] = ucfirst($label) . ' must be a valid email address.';
            } elseif ($name === 'integer' && $value !== null && $value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                $errors[$field][] = ucfirst($label) . ' must be an integer.';
            } elseif ($name === 'numeric' && $value !== null && $value !== '' && !is_numeric($value)) {
                $errors[$field][] = ucfirst($label) . ' must be numeric.';
            } elseif ($name === 'min' && $argument !== null && $value !== null && $value !== '' && (float) $value < (float) $argument) {
                $errors[$field][] = ucfirst($label) . " must be at least {$argument}.";
            } elseif ($name === 'max' && $argument !== null && is_string($value) && mb_strlen($value) > (int) $argument) {
                $errors[$field][] = ucfirst($label) . " must not exceed {$argument} characters.";
            } elseif ($name === 'confirmed' && (string) $value !== (string) ($data[$field . '_confirmation'] ?? '')) {
                $errors[$field][] = ucfirst($label) . ' confirmation does not match.';
            }
        }
    }

    if ($errors !== []) {
        $message = reset($errors)[0] ?? 'Validation failed.';
        throw new InvalidArgumentException($message);
    }
}
