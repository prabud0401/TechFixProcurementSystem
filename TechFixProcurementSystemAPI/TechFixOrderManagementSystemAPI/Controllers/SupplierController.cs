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
    public class SupplierController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public SupplierController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Supplier
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Supplier>>> GetSuppliers()
        {
            return await _context.Suppliers.OrderByDescending(s => s.SupplierID).ToListAsync();
        }

        // GET: api/Supplier/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Supplier>> GetSupplier(int id)
        {
            var supplier = await _context.Suppliers.FindAsync(id);

            if (supplier == null)
            {
                return NotFound(new { message = "Supplier not found." });
            }

            return Ok(supplier);
        }

        // GET: api/Supplier/username/{username}
        [HttpGet("username/{username}")]
        public async Task<ActionResult<Supplier>> GetSupplierByUsername(string username)
        {
            var supplier = await _context.Suppliers.SingleOrDefaultAsync(s => s.Username == username);

            if (supplier == null)
            {
                return NotFound(new { message = "Supplier not found." });
            }

            return Ok(supplier);
        }

        // POST: api/Supplier/login
        [HttpPost("login")]
        public async Task<ActionResult> Login([FromBody] SupplierLoginModel loginModel)
        {
            // Fetch supplier by username
            var supplier = await _context.Suppliers
                .SingleOrDefaultAsync(s => s.Username == loginModel.Username);

            if (supplier == null)
            {
                return Unauthorized(new { message = "Invalid username." });
            }

            // Check if the password matches (no hashing, directly compare)
            if (supplier.Password != loginModel.Password)
            {
                return Unauthorized(new { message = "Invalid username or password." });
            }

            // Successful login
            return Ok(new { message = "Login successful", supplierID = supplier.SupplierID });
        }

        // POST: api/Supplier
        [HttpPost]
        public async Task<ActionResult<Supplier>> PostSupplier(Supplier supplier)
        {
            if (_context.Suppliers.Any(s => s.Email == supplier.Email || s.Username == supplier.Username))
            {
                return BadRequest(new { message = "A supplier with this email or username already exists." });
            }

            _context.Suppliers.Add(supplier);
            await _context.SaveChangesAsync();

            return CreatedAtAction(nameof(GetSupplier), new { id = supplier.SupplierID }, new { message = "Supplier created successfully!", supplier });
        }

        // PUT: api/Supplier/5
        [HttpPut("{id}")]
        public async Task<IActionResult> PutSupplier(int id, Supplier supplier)
        {
            if (id != supplier.SupplierID)
            {
                return BadRequest(new { message = "Supplier ID mismatch." });
            }

            _context.Entry(supplier).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Supplier updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!SupplierExists(id))
                {
                    return NotFound(new { message = "Supplier not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/Supplier/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteSupplier(int id)
        {
            var supplier = await _context.Suppliers.FindAsync(id);
            if (supplier == null)
            {
                return NotFound(new { message = "Supplier not found." });
            }

            _context.Suppliers.Remove(supplier);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Supplier deleted successfully!" });
        }

        private bool SupplierExists(int id)
        {
            return _context.Suppliers.Any(e => e.SupplierID == id);
        }
    }

    // Model for supplier login request
    public class SupplierLoginModel
    {
        public string Username { get; set; }
        public string Password { get; set; }
    }
}
