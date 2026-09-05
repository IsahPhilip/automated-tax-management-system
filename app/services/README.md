# ATMS Service Layer

Controllers -> Services -> Models -> MySQL.

Services included:
AuthService, TaxAssessmentService, PersonalIncomeTaxService,
CorporateTaxService, PenaltyService, InterestService, PaymentService,
ReceiptService, NotificationService and ReportService.

Tax rates should ultimately be read from the database tax-rule configuration.
Fallback rates in the assessment services are development safeguards and must
be reconciled with the approved tax rules for the project's implementation year.
