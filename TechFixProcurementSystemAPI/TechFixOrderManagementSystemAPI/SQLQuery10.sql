-- Insert data into Customers table
INSERT INTO Customers (Name, Email, Phone, Address, Password)
VALUES 
    ('John Doe', 'johndoe@example.com', '555-1234', '123 Main St, Springfield', 'password123'),
    ('Jane Smith', 'janesmith@example.com', '555-5678', '456 Oak Ave, Metropolis', 'password456'),
    ('Alice Johnson', 'alicej@example.com', '555-8765', '789 Pine Lane, Gotham', 'password789');

-- Insert data into Managers table
INSERT INTO Managers (Name, Email, Phone, Address, Username, Password)
VALUES 
    ('Mark Brown', 'markbrown@example.com', '555-4321', '321 Birch Blvd, Star City', 'markbrown', 'passwordMark'),
    ('Susan Davis', 'susandavis@example.com', '555-1122', '654 Maple Dr, Central City', 'susandavis', 'passwordSusan');

-- Insert data into Staff table
INSERT INTO Staff (Name, Email, Phone, Address, Username, Password)
VALUES 
    ('Lisa Johnson', 'lisaj@example.com', '555-7654', '987 Oak Blvd, Smallville', 'lisaj', 'passwordLisa'),
    ('Tom Wilson', 'tomwilson@example.com', '555-9988', '246 Cedar St, Riverdale', 'tomwilson', 'passwordTom');

-- Insert data into Suppliers table
INSERT INTO Suppliers (Name, Email, Phone, Address, Username, Password)
VALUES 
    ('TechWorld Supplies', 'techworld@example.com', '555-4321', '123 Supply St, Springfield', 'techworld', 'passwordTech'),
    ('Global Electronics', 'globalelec@example.com', '555-5678', '456 Elm St, Metropolis', 'globalelec', 'passwordGlobal');
