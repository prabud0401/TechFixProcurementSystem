-- Sample data for QuotationRequest table with new statuses
INSERT INTO QuotationRequests (QuotationCode, CustomerID, Status, RequestDate, QuotationRequestNote) 
VALUES
('QTN2024092301', 1, 'Processing', '2024-09-23', 'Quotation is currently being processed.'),
('QTN2024092302', 2, 'Delivered', '2024-09-23', 'Products have been delivered successfully.'),
('QTN2024092303', 3, 'Canceled', '2024-09-23', 'The customer canceled the order.'),
('QTN2024092304', 4, 'Returned', '2024-09-22', 'Products were returned due to defects.'),
('QTN2024092305', 1, 'Processing', '2024-09-22', 'Urgent order is being processed.'),
('QTN2024092306', 5, 'Delivered', '2024-09-21', 'Delivered on time as per customer request.'),
('QTN2024092307', 2, 'Canceled', '2024-09-21', 'Customer canceled the quotation due to a better offer.'),
('QTN2024092308', 3, 'Returned', '2024-09-20', 'Customer returned the products as they were incorrect.'),
('QTN2024092309', 6, 'Processing', '2024-09-19', 'Order is being processed with expedited shipping.'),
('QTN2024092310', 4, 'Delivered', '2024-09-19', 'Delivered with all requested products.');
