INSERT INTO Categories (CategoryCode, CategoryName)
VALUES 
('ELEC', 'Electronics'),
('HOM', 'Home Appliances'),
('TOYS', 'Toys'),
('CLO', 'Clothing'),
('FURN', 'Furniture');
INSERT INTO NewProducts (Name, Description, Price, CategoryName, CategoryCode, ImageURL)
VALUES 
('Smartphone', 'Latest model smartphone with 5G capability.', 699.99, 'Electronics', 'ELEC', 'https://example.com/images/smartphone.jpg'),
('Refrigerator', 'Energy-efficient refrigerator with large capacity.', 1200.00, 'Home Appliances', 'HOM', 'https://example.com/images/fridge.jpg'),
('Action Figure', 'Popular superhero action figure.', 19.99, 'Toys', 'TOYS', 'https://example.com/images/action_figure.jpg'),
('T-shirt', 'Cotton T-shirt with graphic print.', 15.50, 'Clothing', 'CLO', 'https://example.com/images/tshirt.jpg'),
('Sofa', 'Comfortable 3-seater sofa.', 799.00, 'Furniture', 'FURN', 'https://example.com/images/sofa.jpg');
