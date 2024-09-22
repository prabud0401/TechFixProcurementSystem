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
    public class NewProductController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public NewProductController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/NewProduct
        [HttpGet]
        public async Task<ActionResult<IEnumerable<NewProduct>>> GetNewProducts()
        {
            return await _context.NewProducts.OrderByDescending(p => p.NewProductID).ToListAsync();
        }

        // GET: api/NewProduct/5
        [HttpGet("{id}")]
        public async Task<ActionResult<NewProduct>> GetNewProduct(int id)
        {
            var product = await _context.NewProducts.FindAsync(id);

            if (product == null)
            {
                return NotFound(new { message = "Product not found." });
            }

            return Ok(product);
        }

        // POST: api/NewProduct
        [HttpPost]
        public async Task<ActionResult> PostNewProduct(NewProduct product)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(new { message = "Invalid product data." });
            }

            try
            {
                _context.NewProducts.Add(product);
                await _context.SaveChangesAsync();

                return Ok(new { message = "Product created successfully!" });
            }
            catch (Exception ex)
            {
                return StatusCode(500, new { message = "An error occurred while creating the product.", error = ex.Message });
            }
        }


        // PUT: api/NewProduct/5
        [HttpPut("{id}")]
        public async Task<IActionResult> PutNewProduct(int id, NewProduct product)
        {
            if (id != product.NewProductID)
            {
                return BadRequest(new { message = "Product ID mismatch." });
            }

            _context.Entry(product).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Product updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!NewProductExists(id))
                {
                    return NotFound(new { message = "Product not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/NewProduct/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteNewProduct(int id)
        {
            var product = await _context.NewProducts.FindAsync(id);
            if (product == null)
            {
                return NotFound(new { message = "Product not found." });
            }

            _context.NewProducts.Remove(product);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Product deleted successfully!" });
        }

        private bool NewProductExists(int id)
        {
            return _context.NewProducts.Any(e => e.NewProductID == id);
        }
    }
}
