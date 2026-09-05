<?php
declare(strict_types=1);

// Roles
define('ROLE_ADMIN', 1);
define('ROLE_REVENUE_OFFICER', 2);
define('ROLE_TAXPAYER', 3);

// Tax types
define('TAX_PIT', 'PIT');
define('TAX_CIT', 'CIT');

// Taxpayer types
define('TAXPAYER_INDIVIDUAL', 'INDIVIDUAL');
define('TAXPAYER_COMPANY', 'COMPANY');

// General statuses
define('STATUS_ACTIVE', 'ACTIVE');
define('STATUS_INACTIVE', 'INACTIVE');
define('STATUS_PENDING', 'PENDING');
define('STATUS_APPROVED', 'APPROVED');
define('STATUS_REJECTED', 'REJECTED');
define('STATUS_SUSPENDED', 'SUSPENDED');
define('STATUS_CANCELLED', 'CANCELLED');
define('STATUS_PAID', 'PAID');
define('STATUS_PARTIALLY_PAID', 'PARTIALLY_PAID');
define('STATUS_UNPAID', 'UNPAID');
define('STATUS_OVERDUE', 'OVERDUE');

// Assessments
define('ASSESSMENT_DRAFT', 'DRAFT');
define('ASSESSMENT_PENDING', 'PENDING');
define('ASSESSMENT_APPROVED', 'APPROVED');
define('ASSESSMENT_ISSUED', 'ISSUED');
define('ASSESSMENT_PARTIALLY_PAID', 'PARTIALLY_PAID');
define('ASSESSMENT_PAID', 'PAID');
define('ASSESSMENT_CANCELLED', 'CANCELLED');
define('ASSESSMENT_DISPUTED', 'DISPUTED');

// Declarations
define('DECLARATION_DRAFT', 'DRAFT');
define('DECLARATION_SUBMITTED', 'SUBMITTED');
define('DECLARATION_UNDER_REVIEW', 'UNDER_REVIEW');
define('DECLARATION_APPROVED', 'APPROVED');
define('DECLARATION_REJECTED', 'REJECTED');

// Payments
define('PAYMENT_PENDING', 'PENDING');
define('PAYMENT_SUCCESSFUL', 'SUCCESSFUL');
define('PAYMENT_FAILED', 'FAILED');
define('PAYMENT_REVERSED', 'REVERSED');
define('PAYMENT_CASH', 'CASH');
define('PAYMENT_BANK_TRANSFER', 'BANK_TRANSFER');
define('PAYMENT_CARD', 'CARD');
define('PAYMENT_ONLINE', 'ONLINE');

// Audit actions
define('AUDIT_LOGIN', 'LOGIN');
define('AUDIT_LOGOUT', 'LOGOUT');
define('AUDIT_CREATE', 'CREATE');
define('AUDIT_UPDATE', 'UPDATE');
define('AUDIT_DELETE', 'DELETE');
define('AUDIT_VIEW', 'VIEW');
define('AUDIT_ASSESS', 'ASSESS');
define('AUDIT_PAYMENT', 'PAYMENT');
define('AUDIT_APPROVE', 'APPROVE');
define('AUDIT_REJECT', 'REJECT');

// Currency and formatting
define('CURRENCY_CODE', 'NGN');
define('CURRENCY_SYMBOL', '₦');
define('DECIMAL_PLACES', 2);
define('APP_TIMEZONE', 'Africa/Lagos');
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i:s');

// Pagination and uploads
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('ALLOWED_DOCUMENT_EXTENSIONS', ['pdf', 'jpg', 'jpeg', 'png']);
define('ALLOWED_DOCUMENT_MIME_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// Security
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);

// Document/reference prefixes
define('TAXPAYER_NUMBER_PREFIX', 'TIN-');
define('ASSESSMENT_NUMBER_PREFIX', 'ASS-');
define('PAYMENT_REFERENCE_PREFIX', 'PAY-');
define('RECEIPT_NUMBER_PREFIX', 'RCT-');

// HTTP codes
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_SERVER_ERROR', 500);