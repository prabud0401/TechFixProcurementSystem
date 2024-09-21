using Microsoft.EntityFrameworkCore;

namespace TechFixOrderManagementSystemAPI.Models
{
    [Index("Email", IsUnique = true)]
    public class Customer
    {
        public int CustomerID { get; set; }
        public string Name { get; set; }
        public string Email { get; set; }
        public string Phone { get; set; }
        public string Address { get; set; }

        // Password field
        public string Password { get; set; }  // Make sure to hash this before storing
    }
}
