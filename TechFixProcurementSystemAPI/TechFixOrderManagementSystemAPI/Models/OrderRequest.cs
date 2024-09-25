using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class OrderRequest
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int OrderRequestID { get; set; }  // Primary key for the Order Request

        [Required]
        [StringLength(255)]
        public string QuotationCode { get; set; }  // Quotation code associated with the order

        [Required]
        public int CustomerID { get; set; }  // Customer ID related to the order request

        [Required]
        public int NewProductID { get; set; }  // Product ID of the requested product

        [Required]
        [StringLength(255)]
        public string ProductName { get; set; }  // Name of the product requested

        [Required]
        [Column(TypeName = "decimal(18,2)")]  // Specifies precision and scale for Price
        public decimal Price { get; set; }  // Price of the product

        [Required]
        [StringLength(50)]
        public string CategoryCode { get; set; }  // Category or type of the product

        [Required]
        public int Quantity { get; set; }  // Quantity of the product

        [Required]
        public bool IsUrgent { get; set; }  // Indicates whether the order is urgent

        [Required]
        public DateTime AddedAt { get; set; } = DateTime.Now;  // Timestamp when the product was added to the order

        [NotMapped]
        public decimal TotalPrice => Price * Quantity;  // Total price for the product (price * quantity)
    }
}
