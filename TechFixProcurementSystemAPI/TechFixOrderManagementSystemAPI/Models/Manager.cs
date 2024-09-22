using Microsoft.EntityFrameworkCore;
using System.ComponentModel.DataAnnotations;

namespace TechFixOrderManagementSystemAPI.Models
{
    [Index("Username", IsUnique = true)]  // Ensure the username is unique
    public class Manager
    {
        public int ManagerID { get; set; }  // Primary key for the Manager entity

        [Required]
        public string Name { get; set; }  // Manager's name

        [Required]
        [EmailAddress]
        public string Email { get; set; }  // Manager's email

        [Phone]
        public string Phone { get; set; }  // Manager's phone number

        public string Address { get; set; }  // Manager's address

        [Required]
        public string Username { get; set; }  // Unique username for login

        [Required]
        public string Password { get; set; }  // Password field (should be hashed before storing)
    }
}
