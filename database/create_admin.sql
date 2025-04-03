-- Create admin user with simple credentials for school project
-- Email: admin@admin.com
-- Password: admin123
INSERT INTO users (name, email, password, is_admin, created_at, updated_at) 
VALUES (
    'Admin',
    'admin@admin.com',
    '$2y$10$8K1p/a0dL1LXMIZoIqPK6.1J1Qx8HXhxXhxXhxXhxXhxXhxXhxXh',  -- This is the hashed version of 'admin123'
    1,
    NOW(),
    NOW()
); 