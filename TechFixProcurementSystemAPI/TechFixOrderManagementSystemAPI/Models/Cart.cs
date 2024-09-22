using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class Cart
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int CartID { get; set; }  // Primary key for each cart entry

        [Required]
        [StringLength(255)]
        public string QuotationCode { get; set; }  // Quotation code associated with the cart

        [Required]
        public int CustomerID { get; set; }  // Customer ID (column remains, no FK constraint)

        [Required]
        public int NewProductID { get; set; }  // New Product ID (column remains, no FK constraint)

        [Required]
        public int Quantity { get; set; }  // Quantity of the product

        [Required]
        public bool IsUrgent { get; set; }  // Indicates whether the order is urgent

        [Required]
        public DateTime AddedAt { get; set; } = DateTime.Now;  // Timestamp when the product is added to the cart
    }
}
