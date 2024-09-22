using Microsoft.EntityFrameworkCore;
using TechFixOrderManagementSystemAPI.Models;

namespace TechFixOrderManagementSystemAPI.Services
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options)
            : base(options)
        {
        }

        public DbSet<Customer> Customers { get; set; }
        public DbSet<Manager> Managers { get; set; } // Managers DbSet
        public DbSet<Supplier> Suppliers { get; set; } // Suppliers DbSet
        public DbSet<Staff> Staff { get; set; } // Use 'Staff' (Recommended)
        public DbSet<NewProduct> NewProducts { get; set; } // Use 'NewProducts' (Recommended)
        public DbSet<Category> Categories { get; set; } // Changed to 'Categories' for consistency
        public DbSet<Cart> Carts { get; set; } // Added Cart DbSet for cart management
        public DbSet<QuotationRequest> QuotationRequests { get; set; } // Added QuotationRequest DbSet

    }
}
