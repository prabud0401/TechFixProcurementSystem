-- Insert a sample manager with a bcrypt-hashed password
INSERT INTO Managers (Name, Email, Phone, Address, Username, Password)
VALUES 
    ('John Doe', 'johndoe@example.com', '123-456-7890', '123 Elm Street, Springfield', 'prabu', '$2y$10$K9EHRxQJTI7GPmr1T3lRl.nHpCNVwI.5tOue2JNOEJ5hUlU9FePDC');
