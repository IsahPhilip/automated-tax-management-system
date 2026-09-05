# Automated Tax Management System (ATMS)
## Complete User Journey

### 1. Overview

The Automated Tax Management System (ATMS) is a centralized tax administration application designed to capture taxpayer information, apply configurable tax rules, automatically calculate tax liabilities, facilitate assessment and payment management, and provide reporting and audit capabilities for the Board of Internal Revenue.

### Primary Actors

1. **Taxpayer** — registers, submits tax information, receives assessments, makes/records payments, and accesses receipts and tax history.
2. **Revenue/Tax Officer** — manages taxpayers, reviews declarations, performs or validates assessments, records/verifies payments, and generates reports.
3. **System Administrator** — has complete system control and can perform all Revenue Officer functions, in addition to managing users, tax rules, penalties, interest, and system settings.

> **Role principle:** The System Administrator is the overall system operator/manager and can perform all duties assigned to the Revenue/Tax Officer.

---

# 2. Overall User Journey

```text
                    AUTOMATED TAX MANAGEMENT SYSTEM
                              │
                              ▼
                         LOGIN / ACCESS
                              │
             ┌────────────────┼────────────────┐
             ▼                ▼                ▼
         TAXPAYER       REVENUE OFFICER   ADMINISTRATOR
             │                │                │
             ▼                ▼                ▼
       Taxpayer Portal    Staff Dashboard   Admin Dashboard
             │                │                │
             ▼                ▼                ▼
       Submit Income     Manage Taxpayers   Manage Users
             │           Review Returns     Manage Tax Rules
             ▼                │             Manage Penalties
       Automated             ▼             Manage Interest
       Assessment       Verify / Assess     System Settings
             │                │                │
             └────────────────┼────────────────┘
                              ▼
                       TAX ASSESSMENT
                              │
                              ▼
                       PAYMENT / DEBT
                              │
                              ▼
                       RECEIPT ISSUED
                              │
                              ▼
                         REPORTING
```

# 3. Taxpayer Journey

## Registration

```text
Home Page
    ↓
Register
    ↓
Select Taxpayer Type
    ├── Individual
    └── Business/Company
    ↓
Enter Personal/Business Information
    ↓
Create Account
    ↓
System Generates/Records Taxpayer Profile
```

### Individual information

- Full name
- Tax Identification Number (TIN)
- Phone number
- Email
- Address
- Employment/income information

### Business information

- Business/company name
- TIN
- Registration number
- Business address
- Business type
- Revenue/income information

## Login

```text
Login
   ↓
Enter Email/TIN + Password
   ↓
Authentication
   ↓
Taxpayer Dashboard
```

The dashboard should show current liability, outstanding balance, tax year, declarations, assessments, payments, and receipts.

# 4. Tax Declaration Journey

```text
Select Tax Year
       ↓
Select Tax Type
       │
       ├── Personal Income Tax
       └── Business/Corporate Tax
       ↓
Enter Income/Revenue
       ↓
Enter Allowable Deductions
       ↓
Enter Tax Credits
       ↓
Review Information
       ↓
Submit Declaration
```

Declaration statuses:

```text
DRAFT → SUBMITTED → UNDER_REVIEW → APPROVED
                         │
                         └────────→ REJECTED
```

If rejected, the taxpayer should see the reason and be able to correct the declaration.

# 5. Automated Tax Assessment

## Personal Income Tax

```text
Gross Income
     ↓
Less Allowable Deductions
     ↓
Taxable Income
     ↓
Apply PIT Tax Bands
     ↓
Gross Tax
     ↓
Less Applicable Credits
     ↓
Add Penalties/Interest where applicable
     ↓
FINAL TAX LIABILITY
```

## Business/Corporate Tax

```text
Gross Revenue
     ↓
Allowable Business Expenses
     ↓
Taxable Profit
     ↓
Apply Applicable CIT Rule
     ↓
Corporate Tax
     ↓
Add Penalties/Interest
     ↓
FINAL TAX LIABILITY
```

The system should show the taxpayer how the assessment was calculated rather than displaying only the final amount.

# 6. Revenue Officer Journey

```text
Login
   ↓
Staff Dashboard
   ↓
Taxpayers
Declarations
Assessments
Payments
Reports
```

The dashboard should provide statistics such as registered taxpayers, pending declarations, pending assessments, outstanding tax, and revenue collected.

# 7. Taxpayer Management

```text
Taxpayers
    ↓
Search
    ↓
TIN / Name / Phone
    ↓
Taxpayer Profile
```

The officer can view:

- Declarations
- Assessments
- Payments
- Receipts
- Tax history

# 8. Declaration Review

```text
Taxpayer submits declaration
          ↓
     Status: SUBMITTED
          ↓
Revenue Officer reviews
          ↓
     ┌────┴────┐
     ▼         ▼
  Approve    Reject
     │         │
     ▼         ▼
Assessment   Reason
Generated    Recorded
```

# 9. Automated Assessment Workflow

```text
Approved Declaration
       ↓
Tax Assessment Engine
       ↓
Load Tax Period
       ↓
Load Applicable Tax Rules
       ↓
Calculate Taxable Income
       ↓
Apply Tax Bands / Tax Rate
       ↓
Apply Deductions
       ↓
Apply Credits
       ↓
Calculate Penalty
       ↓
Calculate Interest
       ↓
Generate Assessment
       ↓
Assessment Number
       ↓
Taxpayer Notified
```

Tax rules should be retrieved from MySQL so that tax parameters can be configured without changing PHP source code.

# 10. Assessment Review

The Revenue Officer reviews the generated assessment:

```text
Assessment No: ASS-2026-000001
Taxpayer: John Doe
Tax Type: PIT
Tax Year: 2026

Gross Income:       ₦5,000,000
Deductions:           ₦500,000
Taxable Income:     ₦4,500,000
Tax:                ₦XXX,XXX
Penalty:             ₦XX,XXX
Interest:            ₦XX,XXX
────────────────────────────
Total Liability:    ₦XXX,XXX
```

Workflow:

```text
Review → Approve → Issue Assessment
```

# 11. Taxpayer Receives Assessment

```text
View Assessment
       ↓
See Calculation
       ↓
See Amount Due
       ↓
See Due Date
       ↓
[Pay Now]
```

# 12. Payment Journey

```text
Assessment
    ↓
Pay Tax
    ↓
Select Payment Method
    ↓
Enter Amount
    ↓
Submit Payment
    ↓
Payment Recorded
    ↓
Payment Verified
    ↓
Balance Updated
    ↓
Receipt Generated
```

For the academic implementation, payment methods can include:

- Cash
- Bank Transfer
- Card
- Online

A real payment gateway can be integrated later.

# 13. Receipt Journey

```text
Payment Successful
       ↓
Generate Receipt
       ↓
RCT-2026-000001
       ↓
Taxpayer can:
    ├── View Receipt
    ├── Print Receipt
    └── Download Receipt
```

Receipt information should include taxpayer name, TIN, assessment number, payment reference, tax type, tax period, amount paid, payment date, and remaining balance.

# 14. Administrator Journey

```text
Administrator Login
        ↓
Admin Dashboard
        ↓
┌────────────────────────────────┐
│ Users                          │
│ Taxpayers                      │
│ Tax Rules                      │
│ Tax Types                      │
│ Tax Bands                      │
│ Deductions                     │
│ Credits                        │
│ Penalties                      │
│ Interest                       │
│ Assessments                    │
│ Payments                       │
│ Reports                        │
│ Audit Logs                     │
│ System Settings                │
└────────────────────────────────┘
```

The Administrator can also perform all Revenue Officer operational activities.

# 15. Tax Rule Management

```text
Administrator
      ↓
Tax Rules
      ↓
Create / Edit / Activate Rule
      ↓
Database
      ↓
Assessment Engine
```

Example:

```text
Tax Type: PIT
Tax Year: 2026
Band: 1
Lower Limit: ₦0
Upper Limit: ₦800,000
Rate: X%
Status: ACTIVE
```

# 16. Penalties and Interest

Penalty configuration:

```text
Penalty Rules
      ↓
Configure penalty
      ↓
Database
      ↓
Assessment Engine
```

Interest configuration:

```text
Interest Configuration
      ↓
Configure rate
      ↓
Database
      ↓
Assessment Engine
```

# 17. Reporting Journey

```text
Reports
   ↓
Select Report
   │
   ├── Revenue Report
   ├── Taxpayer Report
   ├── Assessment Report
   ├── Payment Report
   └── Tax Collection Report
   ↓
Select Tax Year / Period
   ↓
Generate
   ↓
View / Print / Export
```

# 18. Audit Trail

Every important operation should be recorded.

```text
Admin logged in
      ↓
Admin modified PIT rule
      ↓
System records:
      │
      ├── User
      ├── Action
      ├── Table
      ├── Record ID
      ├── Previous value
      ├── New value
      ├── IP address
      └── Timestamp
```

# 19. Complete End-to-End Journey

```text
                    START
                      │
                      ▼
             Taxpayer Registers
                      │
                      ▼
                Taxpayer Login
                      │
                      ▼
             Submit Tax Declaration
                      │
                      ▼
             System Validates Data
                      │
                      ▼
            Revenue Officer Reviews
                      │
              ┌───────┴────────┐
              │                │
           Reject            Approve
              │                │
              ▼                ▼
        Taxpayer Corrects   Tax Engine
                              │
                              ▼
                      Automated Assessment
                              │
                              ▼
                       Assessment Issued
                              │
                              ▼
                        Taxpayer Notified
                              │
                              ▼
                           Payment
                              │
                              ▼
                      Payment Verification
                              │
                              ▼
                       Receipt Generated
                              │
                              ▼
                       Balance Updated
                              │
```

# 20. Production-Grade Baseline

The project has been hardened for production use with the following baseline checks:

- Environment-based configuration via `.env` and `.env.example`
- secure session cookies with `httponly`, `samesite=Lax`, and strict session mode
- login throttling to prevent brute-force attacks
- strong password policy enforcement for staff/admin account creation
- secure HTTP headers for pages and assets
- audit logging for key user events and login failures
- graceful error handling for production with minimal leakage of internal details

Recommended deployment checklist:

1. Set `APP_ENV=production` and `APP_DEBUG=false`.
2. Use a dedicated database user with least privilege.
3. Use HTTPS everywhere and set a valid `APP_BASE_URL`.
4. Restrict filesystem write access to storage and logs.
5. Rotate secrets and do not commit real `.env` values to source control.
6. Configure cron or scheduler tasks for report generation and audit retention.
7. Add automated tests and CI/CD verification before production release.

This application is now structured as a production-ready MVC foundation, with the business workflow and operational safeguards in place for a real tax administration deployment.
                              ▼
                       Revenue Reports
                              │
                              ▼
                         AUDIT LOG
                              │
                              ▼
                             END
```

# 20. System Architecture Flow

```text
Authentication
      ↓
Dashboard
      ↓
Taxpayer Management
      ↓
Tax Declaration
      ↓
Tax Assessment Engine
      ↓
Assessment Management
      ↓
Payment Management
      ↓
Receipt Management
      ↓
Reports
      ↓
Administration
      ↓
Audit Trail
```

# 21. Core Project Concept

The system should not be presented merely as:

> "A website where people pay tax."

A stronger description is:

> **A centralized system that captures taxpayer information, applies configurable tax rules, automatically calculates tax liabilities, facilitates assessment and payment management, and provides reporting and audit capabilities for the Board of Internal Revenue.**

# 22. Recommended Implementation Flow

```text
Authentication
      ↓
Dashboard
      ↓
Taxpayer Management
      ↓
Tax Declaration
      ↓
Tax Assessment Engine
      ↓
Assessment Management
      ↓
Payment Management
      ↓
Receipt Management
      ↓
Reports
      ↓
Administration
      ↓
Audit Trail
```

This sequence provides the foundation for mapping the user journey to:

- `routes/web.php`
- `routes/api.php`
- Controllers
- Services
- Models
- Views
- MySQL tables
- Tax calculation logic
