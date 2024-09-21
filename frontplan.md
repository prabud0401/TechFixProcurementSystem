# Process Plan: Customer Order for Quotation Flow

---

## 1. Customer Visit and Order Creation
- **Page**: Customer Dashboard
- **Actions**:
  - Customer logs into their account.
  - Customer clicks on "Create New Order Request".
  - Customer selects products from available inventory and fills out the form with product selection, quantity, and any additional details.
  - Customer submits the order request for quotation.
- **Entities Involved**:
  - Customer
  - OrderRequest
  - Product
  - OrderItem

---

## 2. Manager Approval
- **Page**: Manager Dashboard
- **Actions**:
  - Manager logs into their account.
  - Manager views all pending customer order requests for approval.
  - Manager reviews order details (products, quantity, customer information).
  - Manager approves or rejects the order request.
  - If approved, the order is sent to suppliers for quotations.
- **Entities Involved**:
  - Manager
  - OrderRequest

---

## 3. Suppliers Provide Quotations
- **Page**: Supplier Dashboard
- **Actions**:
  - Suppliers log into their accounts.
  - Suppliers view the newly approved order requests requiring quotations.
  - Each supplier creates a quotation by selecting products from their inventory, setting prices, and estimating delivery times.
  - Suppliers submit their quotations for customer review.
- **Entities Involved**:
  - Supplier
  - Quotation
  - OrderRequest

---

## 4. Customer Selects Quotation
- **Page**: Quotation Page (in Customer Dashboard)
- **Actions**:
  - Customer logs into their dashboard.
  - Customer views the list of quotations provided by suppliers for their order request.
  - Customer compares prices, delivery times, and selects the best quotation.
  - Customer confirms the order based on the selected quotation.
- **Entities Involved**:
  - Customer
  - Quotation
  - OrderRequest

---

## 5. Payment Processing
- **Page**: Payment Page
- **Actions**:
  - Customer makes payment for the confirmed order.
  - Payment details are recorded in the system, and an invoice is generated.
- **Entities Involved**:
  - Customer
  - Payment
  - OrderRequest

---

## 6. Delivery Scheduling and Status Update
- **Page**: Delivery Tracking (Staff Dashboard)
- **Actions**:
  - Staff logs into their account.
  - Staff tracks the status of the order as it is dispatched by the supplier.
  - Delivery personnel updates the delivery status (dispatched, in transit, delivered).
  - Staff monitors and updates the order's delivery progress for customer reference.
- **Entities Involved**:
  - Staff
  - Delivery
  - OrderRequest

---

## 7. Order Completion and Feedback
- **Page**: Customer Feedback Page
- **Actions**:
  - After the order is delivered, the customer can provide feedback on the products and the supplier.
  - Feedback is recorded in the system for future reference.
- **Entities Involved**:
  - Customer
  - CustomerFeedback
  - OrderRequest

---

## 8. Monitoring by Staff
- **Page**: Staff Dashboard
- **Actions**:
  - Staff continuously monitors the overall status of all orders (pending, in-progress, completed).
  - Staff can intervene and resolve issues if any arise in the order process (e.g., delays, missed deliveries).
- **Entities Involved**:
  - Staff
  - OrderHistory
  - OrderRequest

---

# Entities and Roles Involved

1. **Customer**:
   - Creates order requests.
   - Views quotations.
   - Selects suppliers.
   - Makes payments.
   - Provides feedback.
   
2. **Manager**:
   - Approves or rejects customer order requests.
   
3. **Supplier**:
   - Provides quotations based on customer orders.
   - Manages inventory and product availability.
   
4. **Staff**:
   - Tracks and monitors delivery status.
   - Manages the overall order workflow.
   
5. **System**:
   - Updates order history.
   - Generates invoices.
   - Tracks the entire order progress.

---

# Summary of Flow

1. **Order Request**: Customer submits an order request for a quotation.
2. **Approval**: Manager reviews and approves the request.
3. **Quotation**: Suppliers provide quotations for the approved order.
4. **Order Confirmation**: Customer selects a quotation and confirms the order.
5. **Payment**: Customer makes the payment, and the system generates an invoice.
6. **Delivery**: The order is dispatched and delivered, with progress monitored by staff.
7. **Feedback**: Customer provides feedback after the order completion.
8. **Monitoring**: Staff continuously monitors the entire order process for any issues or updates.

---

# Authentication and Access Control

- **Login Required**: All users (Customers, Staff, Managers, Suppliers) must log in to access their dashboards.
- **Registration**: Only customers can register for an account. Other users (Staff, Managers, Suppliers) are managed by the system administrators.
