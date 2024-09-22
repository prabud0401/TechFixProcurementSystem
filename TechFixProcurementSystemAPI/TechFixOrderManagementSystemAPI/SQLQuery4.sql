INSERT INTO Categories (CategoryCode, CategoryName)
VALUES
('COMP', 'Computers'),
('MOB', 'Mobile Phones'),
('ACC', 'Accessories'),
('HOME', 'Home Appliances'),
('ELEC', 'Electronics');
INSERT INTO Customers (Name, Email, Phone, Address, Password)
VALUES
('John Doe', 'johndoe@example.com', '555-1234', '123 Main St, Springfield', 'password123'),
('Jane Smith', 'janesmith@example.com', '555-5678', '456 Oak Ave, Metropolis', 'password456'),
('Alice Johnson', 'alicej@example.com', '555-8765', '789 Pine Lane, Gotham', 'password789'),
('Bob Williams', 'bobw@example.com', '555-5432', '987 Maple St, Star City', 'password321'),
('Charlie Brown', 'charlieb@example.com', '555-6543', '321 Oak Rd, Central City', 'password654');
INSERT INTO Managers (Name, Email, Phone, Address, Username, Password)
VALUES
('Michael Scott', 'mscott@example.com', '555-1122', '1725 Slough Ave, Scranton', 'mscott', 'passwordmichael'),
('Dwight Schrute', 'dschrute@example.com', '555-3344', '1725 Slough Ave, Scranton', 'dschrute', 'passworddwight'),
('Pam Beesly', 'pbeesly@example.com', '555-5566', '1725 Slough Ave, Scranton', 'pbeesly', 'passwordpam');
INSERT INTO NewProducts (Name, Description, Price, CategoryName, CategoryCode, ImageURL)
VALUES
('Dell Optiplex 790 Desktop Computer', 'Intel i5 Quad Core, 16GB RAM, 2TB HDD, Windows 10', 499.99, 'Computers', 'COMP', 'https://example.com/images/dell-optiplex.jpg'),
('iPhone 13', 'Apple iPhone 13 with 128GB Storage', 799.99, 'Mobile Phones', 'MOB', 'https://example.com/images/iphone13.jpg'),
('Samsung Galaxy S21', 'Samsung Galaxy S21 with 256GB Storage', 999.99, 'Mobile Phones', 'MOB', 'https://example.com/images/galaxy-s21.jpg'),
('Sony WH-1000XM4 Headphones', 'Noise Cancelling Wireless Headphones', 349.99, 'Accessories', 'ACC', 'https://example.com/images/sony-headphones.jpg'),
('LG OLED TV 55 Inch', '55 Inch 4K OLED TV', 1499.99, 'Electronics', 'ELEC', 'https://example.com/images/lg-oled-tv.jpg');
INSERT INTO Staff (Name, Email, Phone, Address, Username, Password)
VALUES
('Jim Halpert', 'jhalpert@example.com', '555-7788', '1725 Slough Ave, Scranton', 'jhalpert', 'passwordjim'),
('Stanley Hudson', 'shudson@example.com', '555-9900', '1725 Slough Ave, Scranton', 'shudson', 'passwordstanley'),
('Kelly Kapoor', 'kkapoor@example.com', '555-2233', '1725 Slough Ave, Scranton', 'kkapoor', 'passwordkelly');
INSERT INTO Suppliers (Name, Email, Phone, Address, Username, Password)
VALUES
('ACME Electronics', 'acme@example.com', '555-0001', '123 Supply St, Gotham', 'acme', 'passwordacme'),
('Tech World', 'techworld@example.com', '555-0002', '456 Supplier Rd, Metropolis', 'techworld', 'passwordtech'),
('Gadget Pro', 'gadgetpro@example.com', '555-0003', '789 Gadget Ln, Central City', 'gadgetpro', 'passwordgadget');
