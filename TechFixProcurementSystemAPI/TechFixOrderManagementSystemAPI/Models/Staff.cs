using Microsoft.EntityFrameworkCore;
using System.ComponentModel.DataAnnotations;

namespace TechFixOrderManagementSystemAPI.Models
{
    [Index("Username", IsUnique = true)]  // Ensure the username is unique
    public class Staff
    {
        public int StaffID { get; set; }  // Primary key for the Staff entity

        [Required]
        public string Name { get; set; }  // Staff's name

        [Required]
        [EmailAddress]
        public string Email { get; set; }  // Staff's email

        [Phone]
        public string Phone { get; set; }  // Staff's phone number

        public string Address { get; set; }  // Staff's address

        [Required]
        public string Username { get; set; }  // Unique username for login

        [Required]
        public string Password { get; set; }
    }
}
