-- tax_rules.sql
-- 2026 tax configuration. Verify legal parameters before production use.

INSERT INTO tax_periods(period_id,period_name,start_date,end_date,status)
VALUES(1,'2026 Annual Tax Period','2026-01-01','2026-12-31','OPEN')
ON DUPLICATE KEY UPDATE period_name=VALUES(period_name),status=VALUES(status);

INSERT INTO tax_rules(rule_id,tax_type_id,period_id,rule_name,calculation_method,base_rate,base_amount,effective_from,status,created_by)
VALUES(1,1,1,'PIT 2026 Progressive Tax Rule','PROGRESSIVE_BANDS',NULL,0.00,'2026-01-01','ACTIVE',1)
ON DUPLICATE KEY UPDATE rule_name=VALUES(rule_name),status=VALUES(status);

INSERT INTO tax_bands(band_id,rule_id,lower_limit,upper_limit,tax_rate,fixed_amount,band_order) VALUES
(1,1,0.00,800000.00,0.0000,0.00,1),
(2,1,800000.00,3000000.00,15.0000,0.00,2),
(3,1,3000000.00,12000000.00,18.0000,0.00,3),
(4,1,12000000.00,25000000.00,21.0000,0.00,4),
(5,1,25000000.00,50000000.00,23.0000,0.00,5),
(6,1,50000000.00,NULL,25.0000,0.00,6)
ON DUPLICATE KEY UPDATE lower_limit=VALUES(lower_limit),upper_limit=VALUES(upper_limit),tax_rate=VALUES(tax_rate);

INSERT INTO tax_deductions(rule_id,name,description,deduction_type,value,maximum_value,requires_document,status) VALUES
(1,'Rent Relief','20% of annual rent paid, subject to statutory maximum of ₦500,000.','PERCENTAGE',20.0000,500000.00,TRUE,'ACTIVE'),
(1,'Pension Contribution','Eligible pension contribution subject to applicable rules.','INPUT_BASED',0.0000,NULL,TRUE,'ACTIVE'),
(1,'Mortgage Interest','Eligible mortgage interest subject to applicable rules.','INPUT_BASED',0.0000,NULL,TRUE,'ACTIVE'),
(1,'Life Insurance Premium','Eligible life insurance premium subject to applicable rules.','INPUT_BASED',0.0000,NULL,TRUE,'ACTIVE');

INSERT INTO tax_credits(rule_id,credit_code,name,description,credit_type,value,requires_document,carry_forward_allowed,status) VALUES
(1,'FOREIGN_TAX_CREDIT','Foreign Tax Credit','Eligible foreign tax credit subject to applicable conditions.','INPUT_BASED',0.0000,TRUE,FALSE,'ACTIVE')
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),status=VALUES(status);

INSERT INTO tax_rules(rule_id,tax_type_id,period_id,rule_name,calculation_method,base_rate,small_company_turnover_limit,small_company_fixed_asset_limit,development_levy_rate,minimum_effective_tax_rate,effective_from,status,created_by)
VALUES(2,2,1,'CIT 2026 Companies Tax Rule','RULE_BASED',30.0000,100000000.00,250000000.00,4.0000,15.0000,'2026-01-01','ACTIVE',1)
ON DUPLICATE KEY UPDATE rule_name=VALUES(rule_name),status=VALUES(status);

INSERT INTO tax_deductions(rule_id,name,description,deduction_type,value,requires_document,status) VALUES
(2,'Allowable Business Expenses','Qualifying business expenses subject to statutory conditions.','INPUT_BASED',0.0000,TRUE,'ACTIVE'),
(2,'Tax Loss Relief','Eligible tax loss relief subject to statutory rules.','INPUT_BASED',0.0000,TRUE,'ACTIVE');

INSERT INTO tax_credits(rule_id,credit_code,name,description,credit_type,value,requires_document,carry_forward_allowed,status) VALUES
(2,'EDI','Economic Development Incentive','Configurable incentive for eligible qualifying expenditure.','PERCENTAGE',5.0000,TRUE,TRUE,'ACTIVE')
ON DUPLICATE KEY UPDATE value=VALUES(value),status=VALUES(status);

INSERT INTO penalty_rules(penalty_rule_id,tax_type_id,period_id,penalty_code,penalty_name,description,taxpayer_category,calculation_method,first_period_amount,subsequent_period_amount,percentage_rate,period_unit,status,created_by) VALUES
(1,NULL,1,'FAILURE_TO_REGISTER','Failure to Register','Penalty for failure to register where required.','ALL','FIXED_FIRST_PERIOD_PLUS_SUBSEQUENT',50000.00,25000.00,NULL,'MONTH','ACTIVE',1),
(2,NULL,1,'FAILURE_TO_FILE','Failure to File Returns','Penalty for failure to file required returns.','ALL','FIXED_FIRST_PERIOD_PLUS_SUBSEQUENT',100000.00,50000.00,NULL,'MONTH','ACTIVE',1),
(3,NULL,1,'FAILURE_TO_KEEP_RECORDS_INDIVIDUAL','Failure to Keep/Provide Records - Individual','Penalty for an individual failing to keep/provide required records.','INDIVIDUAL','FIXED_AMOUNT',10000.00,NULL,NULL,'MONTH','ACTIVE',1),
(4,NULL,1,'FAILURE_TO_KEEP_RECORDS_COMPANY','Failure to Keep/Provide Records - Company','Penalty for a company failing to keep/provide required records.','COMPANY','FIXED_AMOUNT',50000.00,NULL,NULL,'MONTH','ACTIVE',1),
(5,NULL,1,'LATE_PAYMENT','Late Payment','Penalty on unpaid tax for late payment.','ALL','PERCENTAGE_OF_UNPAID_TAX',NULL,NULL,10.0000,'YEAR','ACTIVE',1)
ON DUPLICATE KEY UPDATE penalty_name=VALUES(penalty_name),description=VALUES(description),status=VALUES(status);

INSERT INTO interest_rates(tax_type_id,period_id,rate_name,cbr_mpr_rate,statutory_spread_rate,annual_interest_rate,effective_from,status,created_by)
VALUES(NULL,1,'2026 Late Tax Payment Interest - Administrator Configured',NULL,NULL,0.0000,'2026-01-01','ACTIVE',1);