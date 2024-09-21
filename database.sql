-- Create the database
CREATE DATABASE TechFixOrderManagement;

-- Use the database
USE TechFixOrderManagement;

-- Create Product Category table
CREATE TABLE ProductCategory (
    CategoryID INT PRIMARY KEY IDENTITY(1,1),
    CategoryName NVARCHAR(100) NOT NULL,
    Description NVARCHAR(255)
);

-- Create Product table with ImageURL
CREATE TABLE Product (
    ProductID INT PRIMARY KEY IDENTITY(1,1),
    Name NVARCHAR(100) NOT NULL,
    Description NVARCHAR(255),
    Price DECIMAL(10, 2),
    CategoryID INT,
    ImageURL NVARCHAR(255), -- New column to store the product image URL
    FOREIGN KEY (CategoryID) REFERENCES ProductCategory(CategoryID)
);


-- Create Customers table
CREATE TABLE Customers (
    CustomerID INT PRIMARY KEY IDENTITY(1,1),
    Name NVARCHAR(100) NOT NULL,
    Email NVARCHAR(100) NOT NULL,
    Phone NVARCHAR(15),
    Address NVARCHAR(255),
    Password NVARCHAR(255),
);

-- Create Supplier table
CREATE TABLE Supplier (
    SupplierID INT PRIMARY KEY IDENTITY(1,1),
    Name NVARCHAR(100) NOT NULL,
    ContactDetails NVARCHAR(255),
    Location NVARCHAR(255)
);

-- Create Order Request table
CREATE TABLE OrderRequest (
    OrderID INT PRIMARY KEY IDENTITY(1,1),
    CustomerID INT,
    OrderDate DATETIME NOT NULL,
    Status NVARCHAR(50) NOT NULL,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
);

-- Create Quotation table
CREATE TABLE Quotation (
    QuotationID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT,
    SupplierID INT,
    QuotationDate DATETIME NOT NULL,
    TotalPrice DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES OrderRequest(OrderID),
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID)
);

-- Create Inventory table
CREATE TABLE Inventory (
    InventoryID INT PRIMARY KEY IDENTITY(1,1),
    SupplierID INT,
    ProductID INT,
    QuantityAvailable INT NOT NULL,
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- Create Order Item table
CREATE TABLE OrderItem (
    OrderItemID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT,
    ProductID INT,
    Quantity INT NOT NULL,
    Price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES OrderRequest(OrderID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- Create Payment table
CREATE TABLE Payment (
    PaymentID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT,
    PaymentDate DATETIME NOT NULL,
    Amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES OrderRequest(OrderID)
);

-- Create Delivery table
CREATE TABLE Delivery (
    DeliveryID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT,
    DeliveryDate DATETIME NOT NULL,
    Status NVARCHAR(50) NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES OrderRequest(OrderID)
);

-- Create User Account table
CREATE TABLE UserAccount (
    AccountID INT PRIMARY KEY IDENTITY(1,1),
    Username NVARCHAR(100) NOT NULL,
    Password NVARCHAR(255) NOT NULL,
    Role NVARCHAR(50) NOT NULL
);

-- Create Order History table
CREATE TABLE OrderHistory (
    HistoryID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT,
    Status NVARCHAR(50) NOT NULL,
    UpdateDate DATETIME NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES OrderRequest(OrderID)
);

-- Create Discount table
CREATE TABLE Discount (
    DiscountID INT PRIMARY KEY IDENTITY(1,1),
    SupplierID INT,
    DiscountRate DECIMAL(5, 2) NOT NULL,
    StartDate DATETIME NOT NULL,
    EndDate DATETIME NOT NULL,
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID)
);

-- Create Customers Feedback table
CREATE TABLE CustomerFeedback (
    FeedbackID INT PRIMARY KEY IDENTITY(1,1),
    CustomerID INT,
    ProductID INT,
    Rating INT NOT NULL CHECK (Rating BETWEEN 1 AND 5),
    Comment NVARCHAR(255),
    FeedbackDate DATETIME NOT NULL,
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- Create Return Request table
CREATE TABLE ReturnRequest (
    ReturnID INT PRIMARY KEY IDENTITY(1,1),
    OrderID INT,
    ReturnReason NVARCHAR(255),
    Status NVARCHAR(50),
    RequestDate DATETIME NOT NULL,
    FOREIGN KEY (OrderID) REFERENCES OrderRequest(OrderID)
);

-- Create Supplier Review table
CREATE TABLE SupplierReview (
    ReviewID INT PRIMARY KEY IDENTITY(1,1),
    SupplierID INT,
    CustomerID INT,
    Rating INT NOT NULL CHECK (Rating BETWEEN 1 AND 5),
    Comment NVARCHAR(255),
    ReviewDate DATETIME NOT NULL,
    FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID),
    FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
);

-- Create Manager table
CREATE TABLE Manager (
    ManagerID INT PRIMARY KEY IDENTITY(1,1),
    Name NVARCHAR(100) NOT NULL,
    Email NVARCHAR(100),
    Phone NVARCHAR(15),
    Username NVARCHAR(100),
    Password NVARCHAR(255)
);

-- Create Staff table
CREATE TABLE Staff (
    StaffID INT PRIMARY KEY IDENTITY(1,1),
    Name NVARCHAR(100) NOT NULL,
    Email NVARCHAR(100),
    Phone NVARCHAR(15),
    Username NVARCHAR(100),
    Password NVARCHAR(255)
);
