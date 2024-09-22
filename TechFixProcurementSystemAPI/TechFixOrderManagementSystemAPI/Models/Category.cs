using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace TechFixOrderManagementSystemAPI.Models
{
    public class Category
    {
        public int CategoryID { get; set; }

        public string CategoryCode { get; set; }

        public string CategoryName { get; set; }
    }
}
