using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class Product1
    {
        [Key]
        public int Product1ID { get; set; }

        [Required]
        [MaxLength(100)]
        public string Name { get; set; }

        [MaxLength(500)]
        public string Description { get; set; }

        [Required]
        public decimal Price { get; set; }  // We'll set precision in OnModelCreating

        // Foreign key for category
        [Required]
        public int CategoryID { get; set; }

        // Category name (for display, not mapped to the database)
        [NotMapped]
        public string CategoryName { get; set; }

        [MaxLength(200)]
        public string ImageURL { get; set; }
    }
}
