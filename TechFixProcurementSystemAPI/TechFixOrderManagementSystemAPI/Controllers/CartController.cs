using Microsoft.AspNetCore.Mvc;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using TechFixOrderManagementSystemAPI.Models;
using Microsoft.EntityFrameworkCore;
using TechFixOrderManagementSystemAPI.Services;

namespace TechFixOrderManagementSystemAPI.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class CartController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public CartController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Cart
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Cart>>> GetCarts()
        {
            return await _context.Carts.ToListAsync();
        }

        // GET: api/Cart/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<Cart>> GetCart(int id)
        {
            var cart = await _context.Carts.FindAsync(id);

            if (cart == null)
            {
                return NotFound(new { message = "Cart not found." });
            }

            return Ok(cart);
        }

        // GET: api/Cart/byQuotationCode/{quotationCode}
        [HttpGet("byQuotationCode/{quotationCode}")]
        public async Task<ActionResult<IEnumerable<Cart>>> GetCartsByQuotationCode(string quotationCode)
        {
            var carts = await _context.Carts
                                      .Where(c => c.QuotationCode == quotationCode)
                                      .ToListAsync();

            if (carts == null || carts.Count == 0)
            {
                return NotFound(new { message = "No carts found for the given Quotation Code." });
            }

            return Ok(carts);
        }
        // DELETE: api/Cart/byQuotationCode/{quotationCode}
        [HttpDelete("byQuotationCode/{quotationCode}")]
        public async Task<IActionResult> DeleteCartsByQuotationCode(string quotationCode)
        {
            // Find all carts with the given quotationCode
            var carts = await _context.Carts
                                      .Where(c => c.QuotationCode == quotationCode)
                                      .ToListAsync();

            // Check if there are no carts to delete
            if (carts == null || carts.Count == 0)
            {
                return NotFound(new { message = "No carts found for the given Quotation Code." });
            }

            // Remove the carts from the database
            _context.Carts.RemoveRange(carts);

            // Save changes to the database
            await _context.SaveChangesAsync();

            return Ok(new { message = "All carts for the given Quotation Code have been deleted successfully." });
        }

        // POST: api/Cart
        [HttpPost]
        public async Task<ActionResult> PostCart(Cart cart)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(new { message = "Invalid cart data." });
            }

            _context.Carts.Add(cart);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Cart created successfully!" });
        }

        // PUT: api/Cart/{id}
        [HttpPut("{id}")]
        public async Task<IActionResult> PutCart(int id, Cart cart)
        {
            if (id != cart.CartID)
            {
                return BadRequest(new { message = "Cart ID mismatch." });
            }

            _context.Entry(cart).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Cart updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!CartExists(id))
                {
                    return NotFound(new { message = "Cart not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/Cart/{id}
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteCart(int id)
        {
            var cart = await _context.Carts.FindAsync(id);
            if (cart == null)
            {
                return NotFound(new { message = "Cart not found." });
            }

            _context.Carts.Remove(cart);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Cart deleted successfully!" });
        }

        private bool CartExists(int id)
        {
            return _context.Carts.Any(e => e.CartID == id);
        }
    }
}
