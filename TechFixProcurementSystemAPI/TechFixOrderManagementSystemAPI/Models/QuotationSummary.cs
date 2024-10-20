using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class QuotationSummary
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int QuotationSummaryID { get; set; }  // Primary key for the Quotation Summary

        [Required]
        [StringLength(255)]
        public string QuotationCode { get; set; }  // Quotation code associated with the summary

        [Required]
        public int CustomerID { get; set; }  // Foreign key for the customer

        [Required]
        public int SupplierID { get; set; }  // Foreign key for the supplier handling the quotation

        [Required]
        [Column(TypeName = "decimal(18,2)")]  // Specifies precision and scale for total price
        public decimal TotalPrice { get; set; }  // Total price of all products in the order request

        [Required]
        [Column(TypeName = "decimal(18,2)")]  // Specifies precision and scale for quotation price
        public decimal QuotationPrice { get; set; }  // Price quoted by the supplier

        [StringLength(500)]
        public string QuotationNote { get; set; }  // Any additional note for the quotation

        [Required]
        public DateTime RequestDate { get; set; } = DateTime.Now;  // Date when the quotation request was made

        [Required]
        [StringLength(50)]
        public string Status { get; set; }  // Status of the quotation (Pending, Approved, etc.)

        [Required]
        [StringLength(50)]
        public string PayStatus { get; set; }  // Indicates if the payment is made (Paid/Unpaid)

        [StringLength(255)]
        public string PayID { get; set; }  // Transaction ID for the payment, if applicable
    }
}
