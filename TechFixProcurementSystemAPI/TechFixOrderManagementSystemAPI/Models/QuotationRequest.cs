using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class QuotationRequest
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int QuotationRequestID { get; set; }  // Primary key for each quotation request

        [Required]
        [StringLength(255)]
        public string QuotationCode { get; set; }  // Quotation code associated with the request

        [Required]
        public int CustomerID { get; set; }  // Customer ID (no FK constraint)

        [Required]
        [StringLength(50)]
        public string Status { get; set; }  // Status of the quotation request (e.g., Pending, Approved, Rejected)

        [Required]
        public DateTime RequestDate { get; set; } = DateTime.Now;  // Timestamp when the request is made

        [StringLength(500)]
        public string QuotationRequestNote { get; set; }  // Optional note for the quotation request (max 500 characters)
    }
}
