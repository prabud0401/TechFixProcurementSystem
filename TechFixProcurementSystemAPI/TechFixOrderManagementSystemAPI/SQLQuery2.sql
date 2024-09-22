INSERT INTO Carts (QuotationCode, CustomerID, NewProductID, Quantity, IsUrgent, AddedAt)
VALUES
('QTN12345', 1, 1002, 2, 1, GETDATE()),  -- John Doe adds product with ID 1002, urgent
('QTN12346', 2, 1003, 1, 0, GETDATE()),  -- Jane Smith adds product with ID 1003, not urgent
('QTN12347', 3, 1002, 3, 1, GETDATE()),  -- Alice Johnson adds product with ID 1002, urgent
('QTN12348', 1002, 1003, 1, 0, GETDATE()), -- Prabu Prabu adds product with ID 1003, not urgent
('QTN12349', 1, 1003, 2, 1, GETDATE());  -- John Doe adds product with ID 1003, urgent
