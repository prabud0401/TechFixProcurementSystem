using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class NewProduct
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int NewProductID { get; set; }

        [Required]
        public string Name { get; set; }

        public string Description { get; set; }

        [Column(TypeName = "decimal(18, 2)")] // Explicitly specify the SQL column type
        public decimal Price { get; set; }

        [Required]
        public string CategoryName { get; set; }

        [Required]
        public string CategoryCode { get; set; }

        public string ImageURL { get; set; }
    }
}
