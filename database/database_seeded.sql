-- ============================================================
-- AUTOMATED TAX MANAGEMENT SYSTEM
-- MySQL 8.x Database Schema + Seed Data
-- Nigeria Tax Act 2025 / 2026 prototype configuration
--
-- IMPORTANT:
-- This is an academic/prototype database. Tax rules are stored as
-- effective-dated configuration so they can be updated without
-- changing PHP source code. Validate legal/tax parameters before
-- production use.
-- ============================================================

CREATE DATABASE IF NOT EXISTS automated_tax_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE automated_tax_management;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS receipts;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS assessment_items;
DROP TABLE IF EXISTS tax_assessments;
DROP TABLE IF EXISTS tax_declarations;
DROP TABLE IF EXISTS penalty_rules;
DROP TABLE IF EXISTS interest_rates;
DROP TABLE IF EXISTS tax_credits;
DROP TABLE IF EXISTS tax_deductions;
DROP TABLE IF EXISTS tax_bands;
DROP TABLE IF EXISTS tax_rules;
DROP TABLE IF EXISTS tax_periods;
DROP TABLE IF EXISTS tax_types;
DROP TABLE IF EXISTS taxpayer_documents;
DROP TABLE IF EXISTS taxpayers;
DROP TABLE IF EXISTS system_settings;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. ROLES
-- ============================================================
CREATE TABLE roles (
    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 2. USERS
-- ============================================================
CREATE TABLE users (
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

-- ============================================================
-- 3. TAXPAYERS
-- A taxpayer may optionally have a portal user account.
-- ============================================================
CREATE TABLE taxpayers (
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

-- ============================================================
-- 4. TAXPAYER DOCUMENTS
-- ============================================================
CREATE TABLE taxpayer_documents (
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

-- ============================================================
-- 5. TAX TYPES
-- ============================================================
CREATE TABLE tax_types (
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

-- ============================================================
-- 6. TAX PERIODS
-- ============================================================
CREATE TABLE tax_periods (
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

-- ============================================================
-- 7. TAX RULES
-- Generic rule header. Detailed progressive bands live in
-- tax_bands. Other rule parameters are stored here.
-- ============================================================
CREATE TABLE tax_rules (
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

-- ============================================================
-- 8. TAX BANDS
-- ============================================================
CREATE TABLE tax_bands (
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

-- ============================================================
-- 9. TAX DEDUCTIONS
-- ============================================================
CREATE TABLE tax_deductions (
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

-- ============================================================
-- 10. TAX CREDITS
-- ============================================================
CREATE TABLE tax_credits (
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

-- ============================================================
-- 11. PENALTY RULES
-- ============================================================
CREATE TABLE penalty_rules (
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

-- ============================================================
-- 12. INTEREST RATES
-- ============================================================
CREATE TABLE interest_rates (
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

-- ============================================================
-- 13. TAX DECLARATIONS
-- ============================================================
CREATE TABLE tax_declarations (
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

-- ============================================================
-- 14. TAX ASSESSMENTS
-- ============================================================
CREATE TABLE tax_assessments (
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

-- ============================================================
-- 15. ASSESSMENT ITEMS
-- ============================================================
CREATE TABLE assessment_items (
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

-- ============================================================
-- 16. PAYMENTS
-- ============================================================
CREATE TABLE payments (
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

-- ============================================================
-- 17. RECEIPTS
-- ============================================================
CREATE TABLE receipts (
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

-- ============================================================
-- 18. NOTIFICATIONS
-- ============================================================
CREATE TABLE notifications (
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

-- ============================================================
-- 19. AUDIT LOGS
-- ============================================================
CREATE TABLE audit_logs (
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

-- ============================================================
-- 20. SYSTEM SETTINGS
-- ============================================================
CREATE TABLE system_settings (
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

-- ============================================================
-- SEED DATA
-- ============================================================

-- ------------------------------------------------------------
-- Roles
-- ------------------------------------------------------------
INSERT INTO roles (role_id, role_name, description) VALUES
(1, 'System Administrator',
 'Overall system operator/manager with administrative, revenue officer and management privileges.'),
(2, 'Revenue/Tax Officer',
 'Handles taxpayer administration, declarations, assessment review, payment verification and revenue operations.'),
(3, 'Taxpayer',
 'Accesses taxpayer profile, declarations, assessments, payments and receipts.');

-- ------------------------------------------------------------
-- Users
--
-- IMPORTANT: The password hashes below are placeholders for
-- development. Generate production hashes with PHP password_hash().
-- Example: password_hash('YourPassword', PASSWORD_DEFAULT)
-- ------------------------------------------------------------
INSERT INTO users
(user_id, role_id, first_name, last_name, email, phone, password_hash, status)
VALUES
(1, 1, 'System', 'Administrator', 'admin@taxsystem.local',
 '+2348000000001',
 '$2y$10$REPLACE_WITH_PASSWORD_HASH_FOR_ADMIN',
 'ACTIVE'),

(2, 2, 'Revenue', 'Officer', 'officer@taxsystem.local',
 '+2348000000002',
 '$2y$10$REPLACE_WITH_PASSWORD_HASH_FOR_OFFICER',
 'ACTIVE'),

(3, 3, 'Test', 'Taxpayer', 'taxpayer@taxsystem.local',
 '+2348000000003',
 '$2y$10$REPLACE_WITH_PASSWORD_HASH_FOR_TAXPAYER',
 'ACTIVE');

-- ------------------------------------------------------------
-- Tax types
-- ------------------------------------------------------------
INSERT INTO tax_types
(tax_type_id, code, name, description, calculation_method, status)
VALUES
(1, 'PIT',
 'Personal Income Tax',
 'Personal income tax for qualifying individual taxpayers using the configured progressive bands.',
 'PROGRESSIVE_BANDS',
 'ACTIVE'),

(2, 'CIT',
 'Companies Income Tax',
 'Companies income tax for incorporated businesses/companies under the configured 2026 rules.',
 'RULE_BASED',
 'ACTIVE');

-- ------------------------------------------------------------
-- 2026 Tax Period
-- ------------------------------------------------------------
INSERT INTO tax_periods
(period_id, period_name, start_date, end_date, status)
VALUES
(1, '2026 Annual Tax Period', '2026-01-01', '2026-12-31', 'OPEN');

-- ------------------------------------------------------------
-- PIT 2026 rule
--
-- NTA 2025 regime configured for 2026:
-- 0% first â‚¦800,000
-- 15% next â‚¦2,200,000
-- 18% next â‚¦9,000,000
-- 21% next â‚¦13,000,000
-- 23% next â‚¦25,000,000
-- 25% above â‚¦50,000,000
-- ------------------------------------------------------------
INSERT INTO tax_rules
(rule_id, tax_type_id, period_id, rule_name, calculation_method,
 base_rate, base_amount, effective_from, status, created_by)
VALUES
(1, 1, 1, 'PIT 2026 Progressive Tax Rule',
 'PROGRESSIVE_BANDS',
 NULL, 0.00, '2026-01-01', 'ACTIVE', 1);

INSERT INTO tax_bands
(band_id, rule_id, lower_limit, upper_limit, tax_rate, fixed_amount, band_order)
VALUES
(1, 1, 0.00, 800000.00, 0.0000, 0.00, 1),
(2, 1, 800000.00, 3000000.00, 15.0000, 0.00, 2),
(3, 1, 3000000.00, 12000000.00, 18.0000, 0.00, 3),
(4, 1, 12000000.00, 25000000.00, 21.0000, 0.00, 4),
(5, 1, 25000000.00, 50000000.00, 23.0000, 0.00, 5),
(6, 1, 50000000.00, NULL, 25.0000, 0.00, 6);

-- ------------------------------------------------------------
-- PIT deductions
-- ------------------------------------------------------------
INSERT INTO tax_deductions
(rule_id, name, description, deduction_type, value, maximum_value, requires_document, status)
VALUES
(1, 'Rent Relief',
 '20% of annual rent paid, subject to the statutory maximum of â‚¦500,000.',
 'PERCENTAGE', 20.0000, 500000.00, TRUE, 'ACTIVE'),

(1, 'Pension Contribution',
 'Eligible pension contribution deductible according to the applicable pension arrangement and tax rules.',
 'INPUT_BASED', 0.0000, NULL, TRUE, 'ACTIVE'),

(1, 'Mortgage Interest',
 'Eligible interest paid on a mortgage loan for an owner-occupied residential property, subject to statutory conditions.',
 'INPUT_BASED', 0.0000, NULL, TRUE, 'ACTIVE'),

(1, 'Life Insurance Premium',
 'Eligible life insurance premium deduction subject to statutory conditions and documentation.',
 'INPUT_BASED', 0.0000, NULL, TRUE, 'ACTIVE');

-- ------------------------------------------------------------
-- PIT tax credit
-- ------------------------------------------------------------
INSERT INTO tax_credits
(rule_id, credit_code, name, description, credit_type, value,
 maximum_value, requires_document, carry_forward_allowed, status)
VALUES
(1, 'FOREIGN_TAX_CREDIT',
 'Foreign Tax Credit',
 'Eligible foreign tax credit against Nigerian tax liability, subject to applicable statutory conditions.',
 'INPUT_BASED', 0.0000, NULL, TRUE, FALSE, 'ACTIVE');

-- ------------------------------------------------------------
-- CIT 2026 rule
--
-- Small company:
-- turnover <= â‚¦100m AND fixed assets <= â‚¦250m => 0% CIT
--
-- Other companies:
-- 30% CIT
--
-- Development Levy:
-- 4% of assessable profit for applicable companies.
-- ------------------------------------------------------------
INSERT INTO tax_rules
(rule_id, tax_type_id, period_id, rule_name, calculation_method,
 base_rate, small_company_turnover_limit,
 small_company_fixed_asset_limit, development_levy_rate,
 minimum_effective_tax_rate, effective_from, status, created_by)
VALUES
(2, 2, 1, 'CIT 2026 Companies Tax Rule',
 'RULE_BASED',
 30.0000,
 100000000.00,
 250000000.00,
 4.0000,
 15.0000,
 '2026-01-01',
 'ACTIVE',
 1);

-- ------------------------------------------------------------
-- CIT deductions
-- ------------------------------------------------------------
INSERT INTO tax_deductions
(rule_id, name, description, deduction_type, value, maximum_value, requires_document, status)
VALUES
(2, 'Allowable Business Expenses',
 'Qualifying business expenses incurred wholly and exclusively in generating taxable income, subject to statutory conditions.',
 'INPUT_BASED', 0.0000, NULL, TRUE, 'ACTIVE'),

(2, 'Tax Loss Relief',
 'Eligible tax loss relief subject to the applicable statutory rules.',
 'INPUT_BASED', 0.0000, NULL, TRUE, 'ACTIVE');

-- ------------------------------------------------------------
-- CIT credit / incentive
-- Advanced configuration; can be implemented in a later module.
-- ------------------------------------------------------------
INSERT INTO tax_credits
(rule_id, credit_code, name, description, credit_type, value,
 maximum_value, requires_document, carry_forward_allowed, status)
VALUES
(2, 'EDI',
 'Economic Development Incentive',
 'Configurable economic development tax credit for eligible qualifying capital expenditure, subject to statutory requirements.',
 'PERCENTAGE', 5.0000, NULL, TRUE, TRUE, 'ACTIVE');

-- ------------------------------------------------------------
-- Penalty rules
-- 2026 prototype configuration.
-- ------------------------------------------------------------
INSERT INTO penalty_rules
(penalty_rule_id, tax_type_id, period_id, penalty_code, penalty_name,
 description, taxpayer_category, calculation_method,
 first_period_amount, subsequent_period_amount, percentage_rate,
 period_unit, status, created_by)
VALUES
(1, NULL, 1, 'FAILURE_TO_REGISTER',
 'Failure to Register',
 'Penalty for failure to register for tax where registration is required.',
 'ALL',
 'FIXED_FIRST_PERIOD_PLUS_SUBSEQUENT',
 50000.00, 25000.00, NULL, 'MONTH',
 'ACTIVE', 1),

(2, NULL, 1, 'FAILURE_TO_FILE',
 'Failure to File Returns',
 'Penalty for failure to file required tax returns or filing incomplete/inaccurate returns, subject to the applicable statutory conditions.',
 'ALL',
 'FIXED_FIRST_PERIOD_PLUS_SUBSEQUENT',
 100000.00, 50000.00, NULL, 'MONTH',
 'ACTIVE', 1),

(3, NULL, 1, 'FAILURE_TO_KEEP_RECORDS_INDIVIDUAL',
 'Failure to Keep/Provide Records - Individual',
 'Penalty for an individual taxpayer who fails to keep or provide required records.',
 'INDIVIDUAL',
 'FIXED_AMOUNT',
 10000.00, NULL, NULL, 'MONTH',
 'ACTIVE', 1),

(4, NULL, 1, 'FAILURE_TO_KEEP_RECORDS_COMPANY',
 'Failure to Keep/Provide Records - Company',
 'Penalty for a company that fails to keep or provide required records.',
 'COMPANY',
 'FIXED_AMOUNT',
 50000.00, NULL, NULL, 'MONTH',
 'ACTIVE', 1),

(5, NULL, 1, 'LATE_PAYMENT',
 'Late Payment',
 'Penalty on unpaid tax for late payment, subject to the applicable statutory conditions.',
 'ALL',
 'PERCENTAGE_OF_UNPAID_TAX',
 NULL, NULL, 10.0000, 'YEAR',
 'ACTIVE', 1);

-- ------------------------------------------------------------
-- Interest configuration
--
-- The actual CBN MPR and applicable spread should be verified
-- and updated by the administrator when the relevant official
-- rate is established. A placeholder configuration is therefore
-- provided rather than hard-coding an unverified live rate.
-- ------------------------------------------------------------
INSERT INTO interest_rates
(tax_type_id, period_id, rate_name, cbr_mpr_rate,
 statutory_spread_rate, annual_interest_rate,
 effective_from, status, created_by)
VALUES
(NULL, 1, '2026 Late Tax Payment Interest - Administrator Configured',
 NULL, NULL, 0.0000,
 '2026-01-01', 'ACTIVE', 1);

-- ------------------------------------------------------------
-- System settings
-- ------------------------------------------------------------
INSERT INTO system_settings
(setting_key, setting_value, description, updated_by)
VALUES
('organization_name', 'Board of Internal Revenue - Automated Tax Management System',
 'Display name of the application.', 1),

('organization_country', 'Nigeria',
 'Country of operation.', 1),

('currency', 'NGN',
 'System currency code.', 1),

('currency_symbol', 'â‚¦',
 'System currency symbol.', 1),

('assessment_prefix', 'ASS-',
 'Prefix for generated assessment numbers.', 1),

('payment_prefix', 'PAY-',
 'Prefix for generated payment references.', 1),

('receipt_prefix', 'RCT-',
 'Prefix for generated receipt numbers.', 1),

('taxpayer_prefix', 'TIN-',
 'Prefix for generated taxpayer numbers.', 1),

('minimum_wage_reference', '70000.00',
 'Configurable reference value for minimum-wage related tax treatment; verify against the applicable legal regime before production use.', 1);

-- ------------------------------------------------------------
-- Sample individual taxpayer
-- ------------------------------------------------------------
INSERT INTO taxpayers
(taxpayer_id, user_id, taxpayer_number, taxpayer_type,
 first_name, last_name, email, phone,
 identification_type, identification_number,
 address, city, state, status, registration_date)
VALUES
(1, 3, 'TIN-2026-000001', 'INDIVIDUAL',
 'John', 'Adebayo', 'taxpayer@taxsystem.local', '+2348000000003',
 'NIN', 'TEST-NIN-000001',
 '12 Sample Street', 'Abuja', 'FCT', 'ACTIVE', '2026-01-10');

-- ------------------------------------------------------------
-- Sample company taxpayer
-- ------------------------------------------------------------
INSERT INTO taxpayers
(taxpayer_id, user_id, taxpayer_number, taxpayer_type,
 business_name, email, phone,
 identification_type, identification_number,
 address, city, state, annual_turnover, fixed_assets_value,
 status, registration_date)
VALUES
(2, NULL, 'TIN-2026-000002', 'CORPORATE',
 'Sample Technology Limited', 'accounts@sampletech.local', '+2348000000010',
 'CAC', 'TEST-CAC-000001',
 '25 Technology Avenue', 'Abuja', 'FCT',
 75000000.00, 150000000.00,
 'ACTIVE', '2026-01-15');

-- ------------------------------------------------------------
-- Sample larger company
-- ------------------------------------------------------------
INSERT INTO taxpayers
(taxpayer_id, user_id, taxpayer_number, taxpayer_type,
 business_name, email, phone,
 identification_type, identification_number,
 address, city, state, annual_turnover, fixed_assets_value,
 status, registration_date)
VALUES
(3, NULL, 'TIN-2026-000003', 'CORPORATE',
 'Sample Manufacturing Plc', 'tax@samplemanufacturing.local', '+2348000000020',
 'CAC', 'TEST-CAC-000002',
 '50 Industrial Road', 'Abuja', 'FCT',
 350000000.00, 400000000.00,
 'ACTIVE', '2026-01-20');

-- ============================================================
-- Verification queries
-- ============================================================

-- SELECT * FROM roles;
-- SELECT * FROM users;
-- SELECT * FROM taxpayers;
-- SELECT * FROM tax_types;
-- SELECT * FROM tax_periods;
-- SELECT * FROM tax_rules;
-- SELECT * FROM tax_bands ORDER BY rule_id, band_order;
-- SELECT * FROM tax_deductions;
-- SELECT * FROM tax_credits;
-- SELECT * FROM penalty_rules;
-- SELECT * FROM interest_rates;

-- ============================================================
-- END OF DATABASE SCRIPT
-- ============================================================
