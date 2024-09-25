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
    public class OrderRequestController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public OrderRequestController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/OrderRequest
        [HttpGet]
        public async Task<ActionResult<IEnumerable<OrderRequest>>> GetOrderRequests()
        {
            return await _context.OrderRequests.ToListAsync();
        }

        // GET: api/OrderRequest/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<OrderRequest>> GetOrderRequest(int id)
        {
            var orderRequest = await _context.OrderRequests.FindAsync(id);

            if (orderRequest == null)
            {
                return NotFound(new { message = "Order request not found." });
            }

            return Ok(orderRequest);
        }

        // GET: api/OrderRequest/byQuotationCode/{quotationCode}
        [HttpGet("byQuotationCode/{quotationCode}")]
        public async Task<ActionResult<IEnumerable<OrderRequest>>> GetOrderRequestsByQuotationCode(string quotationCode)
        {
            var orderRequests = await _context.OrderRequests
                                              .Where(or => or.QuotationCode == quotationCode)
                                              .ToListAsync();

            if (orderRequests == null || orderRequests.Count == 0)
            {
                return NotFound(new { message = "No order requests found for the given Quotation Code." });
            }

            return Ok(orderRequests);
        }

        // DELETE: api/OrderRequest/byQuotationCode/{quotationCode}
        [HttpDelete("byQuotationCode/{quotationCode}")]
        public async Task<IActionResult> DeleteOrderRequestsByQuotationCode(string quotationCode)
        {
            var orderRequests = await _context.OrderRequests
                                              .Where(or => or.QuotationCode == quotationCode)
                                              .ToListAsync();

            if (orderRequests == null || orderRequests.Count == 0)
            {
                return NotFound(new { message = "No order requests found for the given Quotation Code." });
            }

            _context.OrderRequests.RemoveRange(orderRequests);
            await _context.SaveChangesAsync();

            return Ok(new { message = "All order requests for the given Quotation Code have been deleted successfully." });
        }

        // POST: api/OrderRequest
        [HttpPost]
        public async Task<ActionResult> PostOrderRequest(OrderRequest orderRequest)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(new { message = "Invalid order request data." });
            }

            _context.OrderRequests.Add(orderRequest);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Order request created successfully!" });
        }

        // PUT: api/OrderRequest/{id}
        [HttpPut("{id}")]
        public async Task<IActionResult> PutOrderRequest(int id, OrderRequest orderRequest)
        {
            if (id != orderRequest.OrderRequestID)
            {
                return BadRequest(new { message = "Order Request ID mismatch." });
            }

            _context.Entry(orderRequest).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Order request updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!OrderRequestExists(id))
                {
                    return NotFound(new { message = "Order request not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/OrderRequest/{id}
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteOrderRequest(int id)
        {
            var orderRequest = await _context.OrderRequests.FindAsync(id);
            if (orderRequest == null)
            {
                return NotFound(new { message = "Order request not found." });
            }

            _context.OrderRequests.Remove(orderRequest);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Order request deleted successfully!" });
        }

        private bool OrderRequestExists(int id)
        {
            return _context.OrderRequests.Any(e => e.OrderRequestID == id);
        }
    }
}
