using Microsoft.EntityFrameworkCore;
using TechFixOrderManagementSystemAPI.Models;

namespace TechFixOrderManagementSystemAPI.Services
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) : base(options)
        {
        }

        public DbSet<Customer> Customers { get; set; }
        public DbSet<Product1> Product1s { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // Configure precision for decimal fields
            modelBuilder.Entity<Product1>()
                .Property(p => p.Price)
                .HasColumnType("decimal(18,2)"); // 18 precision, 2 decimal places
        }
    }
}
