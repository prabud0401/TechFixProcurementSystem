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
    public class ManagerController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public ManagerController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Manager
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Manager>>> GetManagers()
        {
            return await _context.Managers.OrderByDescending(m => m.ManagerID).ToListAsync();
        }

        // GET: api/Manager/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Manager>> GetManager(int id)
        {
            var manager = await _context.Managers.FindAsync(id);

            if (manager == null)
            {
                return NotFound(new { message = "Manager not found." });
            }

            return Ok(manager);
        }

        // GET: api/Manager/username/{username}
        [HttpGet("username/{username}")]
        public async Task<ActionResult<Manager>> GetManagerByUsername(string username)
        {
            var manager = await _context.Managers.SingleOrDefaultAsync(m => m.Username == username);

            if (manager == null)
            {
                return NotFound(new { message = "Manager not found." });
            }

            return Ok(manager);
        }

        // POST: api/Manager/login
        [HttpPost("login")]
        public async Task<ActionResult> Login([FromBody] ManagerLoginModel loginModel)
        {
            // Fetch manager by username
            var manager = await _context.Managers
                .SingleOrDefaultAsync(m => m.Username == loginModel.Username);

            if (manager == null)
            {
                return Unauthorized(new { message = "Invalid username " });
            }

            // Check if the password matches (you should compare hashed passwords)
            if (manager.Password != loginModel.Password)
            {
                return Unauthorized(new { message = "Invalid username or password." });
            }

            // Successful login
            return Ok(new { message = "Login successful", managerID = manager.ManagerID });
        }

        // POST: api/Manager
        [HttpPost]
        public async Task<ActionResult<Manager>> PostManager(Manager manager)
        {
            if (_context.Managers.Any(m => m.Email == manager.Email || m.Username == manager.Username))
            {
                return BadRequest(new { message = "A manager with this email or username already exists." });
            }

            _context.Managers.Add(manager);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetManager), new { id = manager.ManagerID }, new { message = "Manager created successfully!", manager });
        }

        // PUT: api/Manager/5
        [HttpPut("{id}")]
        public async Task<IActionResult> PutManager(int id, Manager manager)
        {
            if (id != manager.ManagerID)
            {
                return BadRequest(new { message = "Manager ID mismatch." });
            }

            _context.Entry(manager).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Manager updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!ManagerExists(id))
                {
                    return NotFound(new { message = "Manager not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/Manager/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteManager(int id)
        {
            var manager = await _context.Managers.FindAsync(id);
            if (manager == null)
            {
                return NotFound(new { message = "Manager not found." });
            }

            _context.Managers.Remove(manager);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Manager deleted successfully!" });
        }

        private bool ManagerExists(int id)
        {
            return _context.Managers.Any(e => e.ManagerID == id);
        }
    }

    // Model for login request
    public class ManagerLoginModel
    {
        public string Username { get; set; }
        public string Password { get; set; }
    }
}
