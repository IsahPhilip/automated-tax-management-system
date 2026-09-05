-- tax_types.sql
INSERT INTO tax_types (tax_type_id,code,name,description,calculation_method,status) VALUES
(1,'PIT','Personal Income Tax','Personal income tax using configured progressive bands.','PROGRESSIVE_BANDS','ACTIVE'),
(2,'CIT','Companies Income Tax','Companies income tax using configured company rules.','RULE_BASED','ACTIVE')
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),calculation_method=VALUES(calculation_method),status=VALUES(status);