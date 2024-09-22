INSERT INTO Carts (QuotationCode, CustomerID, NewProductID, Quantity, IsUrgent, AddedAt)
VALUES
('QTN12345', 1, 1001, 2, 1, GETDATE()), -- Urgent order
('QTN12346', 2, 1002, 1, 0, GETDATE()), -- Non-urgent order
('QTN12347', 1, 1003, 5, 1, GETDATE()), -- Urgent order
('QTN12348', 3, 1004, 3, 0, GETDATE()), -- Non-urgent order
('QTN12349', 2, 1005, 4, 1, GETDATE()); -- Urgent order
