-- ============================================================
-- REALISTIC SEED DATA
-- Declarations → Assessments → Payments → Receipts
-- Taxpayer 1 = John Adebayo (INDIVIDUAL, PIT)
-- Taxpayer 2 = Sample Technology Limited (CORPORATE, CIT - small)
-- Taxpayer 3 = Sample Manufacturing Plc (CORPORATE, CIT - large)
-- All reference period_id=1 (2026), assessed_by=2 (officer),
-- approved_by=1 (admin), created_by=1 (admin)
-- ============================================================

USE automated_tax_management;

-- ============================================================
-- TAX DECLARATIONS
-- ============================================================

INSERT INTO tax_declarations
(declaration_id, taxpayer_id, tax_type_id, period_id,
 gross_income, other_income, taxable_benefits,
 allowable_expenses, claimed_deductions, pension_contribution,
 annual_rent_paid, mortgage_interest, life_insurance_premium,
 foreign_tax_paid, status, submitted_at, reviewed_by, reviewed_at)
VALUES
-- John Adebayo: salary ₦7.2m, rent ₦600k, pension ₦360k → APPROVED
(1, 1, 1, 1,
 7200000.00, 0.00, 0.00,
 0.00, 460000.00, 360000.00,
 600000.00, 0.00, 0.00,
 0.00,
 'APPROVED', '2026-03-15 09:22:00', 2, '2026-03-17 11:05:00'),

-- John Adebayo: second declaration SUBMITTED (pending review)
(2, 1, 1, 1,
 7200000.00, 480000.00, 0.00,
 0.00, 460000.00, 360000.00,
 600000.00, 0.00, 120000.00,
 0.00,
 'SUBMITTED', '2026-04-02 14:10:00', NULL, NULL),

-- Sample Technology Ltd: small company CIT → APPROVED
(3, 2, 2, 1,
 0.00, 0.00, 0.00,
 18500000.00, 0.00, 0.00,
 0.00, 0.00, 0.00,
 0.00,
 'APPROVED', '2026-03-20 10:00:00', 2, '2026-03-22 09:30:00'),

-- Sample Manufacturing Plc: large company CIT → APPROVED
(4, 3, 2, 1,
 0.00, 0.00, 0.00,
 62000000.00, 0.00, 0.00,
 0.00, 0.00, 0.00,
 0.00,
 'APPROVED', '2026-03-25 08:45:00', 2, '2026-03-27 14:20:00'),

-- John Adebayo: REJECTED declaration
(5, 1, 1, 1,
 7200000.00, 0.00, 0.00,
 0.00, 900000.00, 360000.00,
 600000.00, 0.00, 0.00,
 0.00,
 'REJECTED', '2026-02-10 11:00:00', 2, '2026-02-12 10:00:00'),

-- Sample Technology Ltd: UNDER_REVIEW
(6, 2, 2, 1,
 0.00, 0.00, 0.00,
 19000000.00, 0.00, 0.00,
 0.00, 0.00, 0.00,
 0.00,
 'UNDER_REVIEW', '2026-04-10 09:00:00', NULL, NULL);

-- ============================================================
-- TAX ASSESSMENTS
-- ============================================================
-- PIT calculation for John Adebayo (declaration 1):
--   Gross income:        ₦7,200,000
--   Pension deduction:   ₦360,000
--   Rent relief (20% of ₦600k, max ₦500k): ₦120,000
--   Total deductions:    ₦480,000  (claimed_deductions stored)
--   Taxable income:      ₦6,720,000
--   Band 1: ₦800k @ 0%  = ₦0
--   Band 2: ₦2,200k @15% = ₦330,000
--   Band 3: ₦3,720k @18% = ₦669,600
--   Gross tax:           ₦999,600
--   Total liability:     ₦999,600
--
-- CIT for Sample Technology Ltd (declaration 3):
--   Turnover ₦75m < ₦100m AND fixed assets ₦150m < ₦250m → small company 0% CIT
--   Assessable profit: ₦75m - ₦18.5m expenses = ₦56,500,000
--   CIT: ₦0  (small company exemption)
--   Dev levy: 0 (small company exempt)
--   Total liability: ₦0
--
-- CIT for Sample Manufacturing Plc (declaration 4):
--   Turnover ₦350m > ₦100m → large company 30% CIT
--   Assessable profit: ₦350m - ₦62m expenses = ₦288,000,000
--   CIT @ 30%: ₦86,400,000
--   Dev levy @ 4%: ₦11,520,000
--   Total liability: ₦97,920,000
-- ============================================================

INSERT INTO tax_assessments
(assessment_id, assessment_number, taxpayer_id, declaration_id,
 tax_type_id, period_id,
 taxable_income, assessable_profit,
 gross_tax, total_deductions, tax_credits,
 penalties, interest, development_levy,
 total_liability, amount_paid, balance_due,
 due_date, status,
 assessed_by, approved_by, assessed_at, approved_at)
VALUES
-- Assessment 1: John Adebayo PIT — PAID
(1, 'ASS-2026-000001', 1, 1,
 1, 1,
 6720000.00, NULL,
 999600.00, 480000.00, 0.00,
 0.00, 0.00, 0.00,
 999600.00, 999600.00, 0.00,
 '2026-04-30', 'PAID',
 2, 1, '2026-03-17 12:00:00', '2026-03-18 09:00:00'),

-- Assessment 2: Sample Technology Ltd CIT — PAID (₦0 liability)
(2, 'ASS-2026-000002', 2, 3,
 2, 1,
 0.00, 56500000.00,
 0.00, 18500000.00, 0.00,
 0.00, 0.00, 0.00,
 0.00, 0.00, 0.00,
 '2026-06-30', 'PAID',
 2, 1, '2026-03-22 10:00:00', '2026-03-23 09:00:00'),

-- Assessment 3: Sample Manufacturing Plc CIT — PARTIALLY_PAID
(3, 'ASS-2026-000003', 3, 4,
 2, 1,
 0.00, 288000000.00,
 86400000.00, 62000000.00, 0.00,
 0.00, 0.00, 11520000.00,
 97920000.00, 50000000.00, 47920000.00,
 '2026-06-30', 'PARTIALLY_PAID',
 2, 1, '2026-03-27 15:00:00', '2026-03-28 10:00:00');

-- ============================================================
-- ASSESSMENT ITEMS (breakdown lines)
-- ============================================================

INSERT INTO assessment_items
(assessment_id, band_id, item_type, description, taxable_amount, rate, calculated_amount)
VALUES
-- Assessment 1: John Adebayo PIT bands
(1, 1, 'TAX_BAND', 'Band 1: ₦0 – ₦800,000 @ 0%',        800000.00,  0.0000,  0.00),
(1, 2, 'TAX_BAND', 'Band 2: ₦800,001 – ₦3,000,000 @ 15%', 2200000.00, 15.0000, 330000.00),
(1, 3, 'TAX_BAND', 'Band 3: ₦3,000,001 – ₦6,720,000 @ 18%', 3720000.00, 18.0000, 669600.00),
(1, NULL, 'DEDUCTION', 'Pension Contribution',              360000.00,  NULL,   -360000.00),
(1, NULL, 'DEDUCTION', 'Rent Relief (20% of ₦600,000)',     600000.00,  20.0000, -120000.00),

-- Assessment 2: Sample Technology Ltd — small company exemption
(2, NULL, 'OTHER', 'Small Company CIT Exemption (Turnover < ₦100m, Fixed Assets < ₦250m)', 56500000.00, 0.0000, 0.00),
(2, NULL, 'DEDUCTION', 'Allowable Business Expenses',       18500000.00, NULL,  -18500000.00),

-- Assessment 3: Sample Manufacturing Plc CIT
(3, NULL, 'DEDUCTION', 'Allowable Business Expenses',       62000000.00, NULL,  -62000000.00),
(3, NULL, 'TAX_BAND',  'Companies Income Tax @ 30%',        288000000.00, 30.0000, 86400000.00),
(3, NULL, 'DEVELOPMENT_LEVY', 'Development Levy @ 4%',      288000000.00, 4.0000,  11520000.00);

-- ============================================================
-- PAYMENTS
-- ============================================================

INSERT INTO payments
(payment_id, payment_reference, assessment_id, taxpayer_id,
 amount, payment_method, transaction_reference,
 payment_date, status, verified_by, verified_at, notes)
VALUES
-- John Adebayo: full payment
(1, 'PAY-2026-000001', 1, 1,
 999600.00, 'BANK_TRANSFER', 'TXN-GTB-20260420-001',
 '2026-04-20 10:15:00', 'VERIFIED', 2, '2026-04-20 14:30:00',
 'Full payment via GTBank transfer. Ref confirmed.'),

-- Sample Technology Ltd: ₦0 assessment — nominal acknowledgement payment
(2, 'PAY-2026-000002', 2, 2,
 0.00, 'BANK_TRANSFER', 'TXN-ZEN-20260510-002',
 '2026-05-10 09:00:00', 'VERIFIED', 2, '2026-05-10 11:00:00',
 'Zero-liability assessment acknowledged.'),

-- Sample Manufacturing Plc: first instalment ₦30m
(3, 'PAY-2026-000003', 3, 3,
 30000000.00, 'BANK_TRANSFER', 'TXN-UBA-20260515-003',
 '2026-05-15 11:30:00', 'VERIFIED', 2, '2026-05-15 15:00:00',
 'First instalment payment — UBA transfer.'),

-- Sample Manufacturing Plc: second instalment ₦20m
(4, 'PAY-2026-000004', 3, 3,
 20000000.00, 'BANK_TRANSFER', 'TXN-UBA-20260601-004',
 '2026-06-01 10:00:00', 'VERIFIED', 2, '2026-06-01 13:45:00',
 'Second instalment payment — UBA transfer.'),

-- Sample Manufacturing Plc: third instalment PENDING
(5, 'PAY-2026-000005', 3, 3,
 47920000.00, 'BANK_TRANSFER', NULL,
 '2026-06-25 09:00:00', 'PENDING', NULL, NULL,
 'Balance payment submitted — awaiting verification.');

-- ============================================================
-- RECEIPTS (only for VERIFIED payments)
-- ============================================================

INSERT INTO receipts
(receipt_id, receipt_number, payment_id, taxpayer_id, amount, issued_at, generated_by)
VALUES
(1, 'RCT-2026-000001', 1, 1, 999600.00,  '2026-04-20 14:35:00', 2),
(2, 'RCT-2026-000002', 2, 2,      0.00,  '2026-05-10 11:05:00', 2),
(3, 'RCT-2026-000003', 3, 3, 30000000.00,'2026-05-15 15:10:00', 2),
(4, 'RCT-2026-000004', 4, 3, 20000000.00,'2026-06-01 13:50:00', 2);
