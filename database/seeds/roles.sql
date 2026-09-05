-- roles.sql
INSERT INTO roles (role_id, role_name, description) VALUES
(1,'System Administrator','Overall system operator/manager with administrative, revenue officer and management privileges.'),
(2,'Revenue/Tax Officer','Handles taxpayer administration, declarations, assessment review, payment verification and revenue operations.'),
(3,'Taxpayer','Accesses taxpayer profile, declarations, assessments, payments and receipts.')
ON DUPLICATE KEY UPDATE description=VALUES(description);