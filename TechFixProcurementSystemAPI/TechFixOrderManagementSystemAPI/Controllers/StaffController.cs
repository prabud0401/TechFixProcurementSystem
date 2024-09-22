using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using TechFixOrderManagementSystemAPI.Models;
using TechFixOrderManagementSystemAPI.Services;
using Microsoft.EntityFrameworkCore;

namespace TechFixOrderManagementSystemAPI.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class StaffController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public StaffController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Staff
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Staff>>> GetStaffs()
        {
            return await _context.Staff.OrderByDescending(s => s.StaffID).ToListAsync();
        }

        // GET: api/Staff/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Staff>> GetStaff(int id)
        {
            var staff = await _context.Staff.FindAsync(id);

            if (staff == null)
            {
                return NotFound(new { message = "Staff not found." });
            }

            return Ok(staff);
        }

        // GET: api/Staff/username/{username}
        [HttpGet("username/{username}")]
        public async Task<ActionResult<Staff>> GetStaffByUsername(string username)
        {
            var staff = await _context.Staff.SingleOrDefaultAsync(s => s.Username == username);

            if (staff == null)
            {
                return NotFound(new { message = "Staff not found." });
            }

            return Ok(staff);
        }

        // POST: api/Staff/login
        [HttpPost("login")]
        public async Task<ActionResult> Login([FromBody] StaffLoginModel loginModel)
        {
            // Fetch staff by username
            var staff = await _context.Staff
                .SingleOrDefaultAsync(s => s.Username == loginModel.Username);

            if (staff == null)
            {
                return Unauthorized(new { message = "Invalid username." });
            }

            // Check if the password matches (no password hashing)
            if (staff.Password != loginModel.Password)
            {
                return Unauthorized(new { message = "Invalid username or password." });
            }

            // Successful login
            return Ok(new { message = "Login successful", staffID = staff.StaffID });
        }

        // POST: api/Staff
        [HttpPost]
        public async Task<ActionResult<Staff>> PostStaff(Staff staff)
        {
            if (_context.Staff.Any(s => s.Email == staff.Email || s.Username == staff.Username))
            {
                return BadRequest(new { message = "A staff member with this email or username already exists." });
            }

            _context.Staff.Add(staff);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetStaff), new { id = staff.StaffID }, new { message = "Staff created successfully!", staff });
        }

        // PUT: api/Staff/5
        [HttpPut("{id}")]
        public async Task<IActionResult> PutStaff(int id, Staff staff)
        {
            if (id != staff.StaffID)
            {
                return BadRequest(new { message = "Staff ID mismatch." });
            }

            _context.Entry(staff).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Staff updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!StaffExists(id))
                {
                    return NotFound(new { message = "Staff not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/Staff/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteStaff(int id)
        {
            var staff = await _context.Staff.FindAsync(id);
            if (staff == null)
            {
                return NotFound(new { message = "Staff not found." });
            }

            _context.Staff.Remove(staff);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Staff deleted successfully!" });
        }

        private bool StaffExists(int id)
        {
            return _context.Staff.Any(e => e.StaffID == id);
        }
    }

    // Model for staff login request
    public class StaffLoginModel
    {
        public string Username { get; set; }
        public string Password { get; set; }
    }
}
