-- Inserting sample suppliers with plain passwords (not hashed)
INSERT INTO Suppliers (Name, Email, Phone, Address, Username, Password)
VALUES 
    ('TechWorld Supplies', 'techworld@example.com', '123-456-7890', '456 Elm Street, Springfield', 'techworld', 'password123'),
    ('Global Electronics', 'globalelec@example.com', '987-654-3210', '789 Maple Avenue, Metropolis', 'globalelec', 'password456'),
    ('Hardware Hub', 'hardwarehub@example.com', '555-123-4567', '123 Oak Lane, Gotham', 'hardwarehub', 'password789'),
    ('Component Store', 'componentstore@example.com', '321-555-9876', '987 Birch Road, Star City', 'componentstore', 'passwordabc');
