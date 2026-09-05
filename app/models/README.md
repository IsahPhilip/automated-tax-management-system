# ATMS Models

MySQLi models for the Automated Tax Management System. All models extend `BaseModel` and use the existing `db(): mysqli` helper from `config/database.php`. Values are bound through prepared statements; business-heavy tax calculations belong in services.

Models: BaseModel, Role, User, Taxpayer, TaxpayerDocument, TaxType, TaxPeriod, TaxRule, TaxBand, TaxDeduction, TaxCredit, PenaltyRule, InterestRate, TaxDeclaration, TaxAssessment, AssessmentItem, Payment, Receipt, Notification, AuditLog, SystemSetting.
