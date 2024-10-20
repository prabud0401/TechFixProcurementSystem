using Microsoft.EntityFrameworkCore;
using System.ComponentModel.DataAnnotations;

namespace TechFixOrderManagementSystemAPI.Models
{
    [Index("Username", IsUnique = true)]  // Ensure the username is unique
    public class Supplier
    {
        public int SupplierID { get; set; }  // Primary key for the Supplier entity

        [Required]
        public string Name { get; set; }  // Supplier's name

        [Required]
        [EmailAddress]
        public string Email { get; set; }  // Supplier's email

        [Phone]
        public string Phone { get; set; }  // Supplier's phone number

        public string Address { get; set; }  // Supplier's address

        [Required]
        public string Username { get; set; }  // Unique username for login

        [Required]
        public string Password { get; set; }    
    }
}
