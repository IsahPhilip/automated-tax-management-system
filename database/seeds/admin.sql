-- admin.sql
-- Development password for all seeded users: Password@123
-- Change these passwords immediately before production use.

INSERT INTO users(user_id,role_id,first_name,last_name,email,phone,password_hash,status) VALUES
(1,1,'System','Administrator','admin@taxsystem.local','+2348000000001','$2y$10$8Yqt6jA97k2jAQhQ1we0p.nfVXK.swMhSKuMZ7opmFA8xQtADEGEa','ACTIVE'),
(2,2,'Revenue','Officer','officer@taxsystem.local','+2348000000002','$2y$10$8Yqt6jA97k2jAQhQ1we0p.nfVXK.swMhSKuMZ7opmFA8xQtADEGEa','ACTIVE'),
(3,3,'Test','Taxpayer','taxpayer@taxsystem.local','+2348000000003','$2y$10$8Yqt6jA97k2jAQhQ1we0p.nfVXK.swMhSKuMZ7opmFA8xQtADEGEa','ACTIVE')
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id),first_name=VALUES(first_name),last_name=VALUES(last_name),status=VALUES(status);

INSERT INTO system_settings(setting_key,setting_value,description,updated_by) VALUES
('organization_name','Board of Internal Revenue - Automated Tax Management System','Application display name.',1),
('organization_country','Nigeria','Country of operation.',1),
('currency','NGN','System currency code.',1),
('currency_symbol','NGN','System currency symbol.',1),
('assessment_prefix','ASS-','Assessment number prefix.',1),
('payment_prefix','PAY-','Payment reference prefix.',1),
('receipt_prefix','RCT-','Receipt number prefix.',1),
('taxpayer_prefix','TIN-','Taxpayer number prefix.',1)
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),description=VALUES(description),updated_by=VALUES(updated_by);
