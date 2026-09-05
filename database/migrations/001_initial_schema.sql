-- 001_initial_schema.sql
-- Run after creating/selecting automated_tax_management.
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS roles (
    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
    user_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('ACTIVE','INACTIVE','SUSPENDED','PENDING') NOT NULL DEFAULT 'ACTIVE',
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(role_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    INDEX idx_users_role (role_id),
    INDEX idx_users_status (status),
    INDEX idx_users_name (last_name, first_name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS taxpayers (
    taxpayer_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    taxpayer_number VARCHAR(30) NOT NULL UNIQUE,
    taxpayer_type ENUM('INDIVIDUAL','BUSINESS','CORPORATE') NOT NULL DEFAULT 'INDIVIDUAL',
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    business_name VARCHAR(200) NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    date_of_birth DATE NULL,
    identification_type VARCHAR(50) NULL,
    identification_number VARCHAR(100) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    country VARCHAR(100) NOT NULL DEFAULT 'Nigeria',
    annual_turnover DECIMAL(18,2) NULL DEFAULT 0.00,
    fixed_assets_value DECIMAL(18,2) NULL DEFAULT 0.00,
    status ENUM('PENDING','ACTIVE','SUSPENDED','INACTIVE','REJECTED') NOT NULL DEFAULT 'PENDING',
    registration_date DATE NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_taxpayers_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    UNIQUE KEY uq_taxpayer_identification (identification_type, identification_number),
    INDEX idx_taxpayer_user (user_id),
    INDEX idx_taxpayer_type_status (taxpayer_type, status),
    INDEX idx_taxpayer_name (last_name, first_name),
    INDEX idx_taxpayer_business (business_name),
    INDEX idx_taxpayer_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS taxpayer_documents (
    document_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    taxpayer_id BIGINT UNSIGNED NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    verification_status ENUM('PENDING','VERIFIED','REJECTED') NOT NULL DEFAULT 'PENDING',
    uploaded_by INT UNSIGNED NOT NULL,
    verified_by INT UNSIGNED NULL,
    verified_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_documents_taxpayer
        FOREIGN KEY (taxpayer_id) REFERENCES taxpayers(taxpayer_id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_documents_uploaded_by
        FOREIGN KEY (uploaded_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_documents_verified_by
        FOREIGN KEY (verified_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_documents_taxpayer (taxpayer_id),
    INDEX idx_documents_status (verification_status),
    INDEX idx_documents_type (document_type)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_types (
    tax_type_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    calculation_method ENUM(
        'PROGRESSIVE_BANDS',
        'FLAT_RATE',
        'RULE_BASED',
        'MANUAL'
    ) NOT NULL DEFAULT 'RULE_BASED',
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_tax_types_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_periods (
    period_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    period_name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('OPEN','CLOSED','UPCOMING') NOT NULL DEFAULT 'OPEN',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_tax_period_dates (start_date, end_date),
    INDEX idx_tax_period_status (status),
    INDEX idx_tax_period_dates (start_date, end_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_rules (
    rule_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tax_type_id INT UNSIGNED NOT NULL,
    period_id INT UNSIGNED NOT NULL,
    rule_name VARCHAR(150) NOT NULL,
    calculation_method ENUM(
        'PROGRESSIVE_BANDS',
        'FLAT_RATE',
        'RULE_BASED'
    ) NOT NULL,
    base_rate DECIMAL(7,4) NULL,
    base_amount DECIMAL(18,2) NULL DEFAULT 0.00,
    minimum_tax DECIMAL(18,2) NULL DEFAULT 0.00,
    maximum_tax DECIMAL(18,2) NULL,
    small_company_turnover_limit DECIMAL(18,2) NULL,
    small_company_fixed_asset_limit DECIMAL(18,2) NULL,
    development_levy_rate DECIMAL(7,4) NULL DEFAULT 0.0000,
    minimum_effective_tax_rate DECIMAL(7,4) NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    status ENUM('ACTIVE','INACTIVE','DRAFT') NOT NULL DEFAULT 'ACTIVE',
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_tax_rules_type
        FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_tax_rules_period
        FOREIGN KEY (period_id) REFERENCES tax_periods(period_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_tax_rules_creator
        FOREIGN KEY (created_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    UNIQUE KEY uq_tax_rule_period_name (tax_type_id, period_id, rule_name),
    INDEX idx_tax_rules_type_period (tax_type_id, period_id),
    INDEX idx_tax_rules_effective (effective_from, effective_to),
    INDEX idx_tax_rules_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_bands (
    band_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_id BIGINT UNSIGNED NOT NULL,
    lower_limit DECIMAL(18,2) NOT NULL,
    upper_limit DECIMAL(18,2) NULL,
    tax_rate DECIMAL(7,4) NOT NULL,
    fixed_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    band_order INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tax_bands_rule
        FOREIGN KEY (rule_id) REFERENCES tax_rules(rule_id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    UNIQUE KEY uq_tax_band_order (rule_id, band_order),
    INDEX idx_tax_bands_rule (rule_id),
    INDEX idx_tax_bands_limits (lower_limit, upper_limit)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_deductions (
    deduction_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    deduction_type ENUM('FIXED','PERCENTAGE','INPUT_BASED') NOT NULL,
    value DECIMAL(18,4) NOT NULL DEFAULT 0.0000,
    maximum_value DECIMAL(18,2) NULL,
    minimum_value DECIMAL(18,2) NULL,
    requires_document BOOLEAN NOT NULL DEFAULT TRUE,
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tax_deductions_rule
        FOREIGN KEY (rule_id) REFERENCES tax_rules(rule_id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    INDEX idx_deductions_rule (rule_id),
    INDEX idx_deductions_status (status),
    INDEX idx_deductions_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_credits (
    credit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rule_id BIGINT UNSIGNED NOT NULL,
    credit_code VARCHAR(50) NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    credit_type ENUM('FIXED','PERCENTAGE','INPUT_BASED') NOT NULL,
    value DECIMAL(18,4) NOT NULL DEFAULT 0.0000,
    maximum_value DECIMAL(18,2) NULL,
    requires_document BOOLEAN NOT NULL DEFAULT TRUE,
    carry_forward_allowed BOOLEAN NOT NULL DEFAULT FALSE,
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_tax_credits_rule
        FOREIGN KEY (rule_id) REFERENCES tax_rules(rule_id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    UNIQUE KEY uq_tax_credit_code (credit_code),
    INDEX idx_tax_credits_rule (rule_id),
    INDEX idx_tax_credits_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS penalty_rules (
    penalty_rule_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tax_type_id INT UNSIGNED NULL,
    period_id INT UNSIGNED NOT NULL,
    penalty_code VARCHAR(50) NOT NULL,
    penalty_name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    taxpayer_category ENUM('ALL','INDIVIDUAL','COMPANY') NOT NULL DEFAULT 'ALL',
    calculation_method ENUM(
        'FIXED_FIRST_PERIOD_PLUS_SUBSEQUENT',
        'PERCENTAGE_OF_UNPAID_TAX',
        'FIXED_AMOUNT',
        'INPUT_BASED'
    ) NOT NULL,
    first_period_amount DECIMAL(18,2) NULL,
    subsequent_period_amount DECIMAL(18,2) NULL,
    percentage_rate DECIMAL(7,4) NULL,
    period_unit ENUM('DAY','MONTH','YEAR') NOT NULL DEFAULT 'MONTH',
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_penalty_tax_type
        FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    CONSTRAINT fk_penalty_period
        FOREIGN KEY (period_id) REFERENCES tax_periods(period_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_penalty_creator
        FOREIGN KEY (created_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    UNIQUE KEY uq_penalty_code_period (penalty_code, period_id),
    INDEX idx_penalty_type_period (tax_type_id, period_id),
    INDEX idx_penalty_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS interest_rates (
    interest_rate_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tax_type_id INT UNSIGNED NULL,
    period_id INT UNSIGNED NOT NULL,
    rate_name VARCHAR(150) NOT NULL,
    cbr_mpr_rate DECIMAL(7,4) NULL,
    statutory_spread_rate DECIMAL(7,4) NULL,
    annual_interest_rate DECIMAL(7,4) NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,
    status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    created_by INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_interest_tax_type
        FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    CONSTRAINT fk_interest_period
        FOREIGN KEY (period_id) REFERENCES tax_periods(period_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_interest_creator
        FOREIGN KEY (created_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    INDEX idx_interest_type_period (tax_type_id, period_id),
    INDEX idx_interest_effective (effective_from, effective_to),
    INDEX idx_interest_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_declarations (
    declaration_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    taxpayer_id BIGINT UNSIGNED NOT NULL,
    tax_type_id INT UNSIGNED NOT NULL,
    period_id INT UNSIGNED NOT NULL,
    gross_income DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    other_income DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    taxable_benefits DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    allowable_expenses DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    claimed_deductions DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    pension_contribution DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    annual_rent_paid DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    mortgage_interest DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    life_insurance_premium DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    foreign_tax_paid DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    annual_turnover DECIMAL(18,2) NULL,
    fixed_assets_value DECIMAL(18,2) NULL,
    accounting_profit DECIMAL(18,2) NULL,
    taxable_add_backs DECIMAL(18,2) NULL,
    allowable_business_deductions DECIMAL(18,2) NULL,
    tax_loss_relief DECIMAL(18,2) NULL,
    other_information TEXT NULL,
    status ENUM(
        'DRAFT',
        'SUBMITTED',
        'UNDER_REVIEW',
        'APPROVED',
        'REJECTED',
        'AMENDMENT_REQUIRED'
    ) NOT NULL DEFAULT 'DRAFT',
    submitted_at DATETIME NULL,
    reviewed_by INT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_declarations_taxpayer
        FOREIGN KEY (taxpayer_id) REFERENCES taxpayers(taxpayer_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_declarations_tax_type
        FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_declarations_period
        FOREIGN KEY (period_id) REFERENCES tax_periods(period_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_declarations_reviewer
        FOREIGN KEY (reviewed_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_declarations_taxpayer (taxpayer_id),
    INDEX idx_declarations_type_period (tax_type_id, period_id),
    INDEX idx_declarations_status (status),
    INDEX idx_declarations_submitted (submitted_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tax_assessments (
    assessment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assessment_number VARCHAR(50) NOT NULL UNIQUE,
    taxpayer_id BIGINT UNSIGNED NOT NULL,
    declaration_id BIGINT UNSIGNED NOT NULL,
    tax_type_id INT UNSIGNED NOT NULL,
    period_id INT UNSIGNED NOT NULL,
    taxable_income DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    assessable_profit DECIMAL(18,2) NULL,
    gross_tax DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    total_deductions DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    tax_credits DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    penalties DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    interest DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    development_levy DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    total_liability DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    amount_paid DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    balance_due DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    due_date DATE NULL,
    status ENUM(
        'DRAFT',
        'SUBMITTED',
        'UNDER_REVIEW',
        'REJECTED',
        'APPROVED',
        'ISSUED',
        'PARTIALLY_PAID',
        'PAID',
        'OVERDUE',
        'CANCELLED'
    ) NOT NULL DEFAULT 'DRAFT',
    assessed_by INT UNSIGNED NOT NULL,
    approved_by INT UNSIGNED NULL,
    assessed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    approved_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_assessments_taxpayer
        FOREIGN KEY (taxpayer_id) REFERENCES taxpayers(taxpayer_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_assessments_declaration
        FOREIGN KEY (declaration_id) REFERENCES tax_declarations(declaration_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_assessments_tax_type
        FOREIGN KEY (tax_type_id) REFERENCES tax_types(tax_type_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_assessments_period
        FOREIGN KEY (period_id) REFERENCES tax_periods(period_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_assessments_assessed_by
        FOREIGN KEY (assessed_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_assessments_approved_by
        FOREIGN KEY (approved_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    UNIQUE KEY uq_assessment_declaration (declaration_id),
    INDEX idx_assessments_taxpayer (taxpayer_id),
    INDEX idx_assessments_type_period (tax_type_id, period_id),
    INDEX idx_assessments_status (status),
    INDEX idx_assessments_due_date (due_date),
    INDEX idx_assessments_balance (balance_due)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS assessment_items (
    item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assessment_id BIGINT UNSIGNED NOT NULL,
    band_id BIGINT UNSIGNED NULL,
    item_type ENUM(
        'TAX_BAND',
        'DEDUCTION',
        'CREDIT',
        'PENALTY',
        'INTEREST',
        'DEVELOPMENT_LEVY',
        'OTHER'
    ) NOT NULL DEFAULT 'TAX_BAND',
    description VARCHAR(255) NOT NULL,
    taxable_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    rate DECIMAL(7,4) NULL,
    calculated_amount DECIMAL(18,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_assessment_items_assessment
        FOREIGN KEY (assessment_id) REFERENCES tax_assessments(assessment_id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    CONSTRAINT fk_assessment_items_band
        FOREIGN KEY (band_id) REFERENCES tax_bands(band_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_assessment_items_assessment (assessment_id),
    INDEX idx_assessment_items_type (item_type),
    INDEX idx_assessment_items_band (band_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    payment_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_reference VARCHAR(50) NOT NULL UNIQUE,
    assessment_id BIGINT UNSIGNED NOT NULL,
    taxpayer_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    payment_method ENUM(
        'BANK_TRANSFER',
        'POS',
        'ONLINE',
        'CASH',
        'OTHER'
    ) NOT NULL,
    transaction_reference VARCHAR(100) NULL UNIQUE,
    payment_date DATETIME NOT NULL,
    status ENUM(
        'PENDING',
        'PROCESSING',
        'VERIFIED',
        'FAILED',
        'REJECTED',
        'REVERSED'
    ) NOT NULL DEFAULT 'PENDING',
    verified_by INT UNSIGNED NULL,
    verified_at DATETIME NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payments_assessment
        FOREIGN KEY (assessment_id) REFERENCES tax_assessments(assessment_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_payments_taxpayer
        FOREIGN KEY (taxpayer_id) REFERENCES taxpayers(taxpayer_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_payments_verified_by
        FOREIGN KEY (verified_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_payments_assessment (assessment_id),
    INDEX idx_payments_taxpayer (taxpayer_id),
    INDEX idx_payments_status (status),
    INDEX idx_payments_date (payment_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS receipts (
    receipt_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    payment_id BIGINT UNSIGNED NOT NULL UNIQUE,
    taxpayer_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    issued_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    generated_by INT UNSIGNED NOT NULL,

    CONSTRAINT fk_receipts_payment
        FOREIGN KEY (payment_id) REFERENCES payments(payment_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_receipts_taxpayer
        FOREIGN KEY (taxpayer_id) REFERENCES taxpayers(taxpayer_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_receipts_generated_by
        FOREIGN KEY (generated_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    INDEX idx_receipts_taxpayer (taxpayer_id),
    INDEX idx_receipts_issued_at (issued_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    notification_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(50) NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notifications_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE CASCADE,

    INDEX idx_notifications_user_read (user_id, is_read),
    INDEX idx_notifications_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(100) NOT NULL,
    record_id BIGINT NULL,
    description TEXT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL,

    INDEX idx_audit_user (user_id),
    INDEX idx_audit_module_action (module, action),
    INDEX idx_audit_record (record_id),
    INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS system_settings (
    setting_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NULL,
    description VARCHAR(255) NULL,
    updated_by INT UNSIGNED NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_settings_user
        FOREIGN KEY (updated_by) REFERENCES users(user_id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;