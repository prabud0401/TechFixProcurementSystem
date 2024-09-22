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
    public class QuotationRequestController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public QuotationRequestController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/QuotationRequest
        [HttpGet]
        public async Task<ActionResult<IEnumerable<QuotationRequest>>> GetQuotationRequests()
        {
            return await _context.QuotationRequests.ToListAsync();
        }

        // GET: api/QuotationRequest/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<QuotationRequest>> GetQuotationRequest(int id)
        {
            var quotationRequest = await _context.QuotationRequests.FindAsync(id);

            if (quotationRequest == null)
            {
                return NotFound(new { message = "Quotation request not found." });
            }

            return Ok(quotationRequest);
        }

        // GET: api/QuotationRequest/byQuotationCode/{quotationCode}
        [HttpGet("byQuotationCode/{quotationCode}")]
        public async Task<ActionResult<IEnumerable<QuotationRequest>>> GetQuotationRequestsByQuotationCode(string quotationCode)
        {
            var quotationRequests = await _context.QuotationRequests
                                                  .Where(q => q.QuotationCode == quotationCode)
                                                  .ToListAsync();

            if (quotationRequests == null || quotationRequests.Count == 0)
            {
                return NotFound(new { message = "No quotation requests found for the given Quotation Code." });
            }

            return Ok(quotationRequests);
        }

        // GET: api/QuotationRequest/byCustomerID/{customerID}
        [HttpGet("byCustomerID/{customerID}")]
        public async Task<ActionResult<IEnumerable<QuotationRequest>>> GetQuotationRequestsByCustomerID(int customerID)
        {
            var quotationRequests = await _context.QuotationRequests
                                                  .Where(q => q.CustomerID == customerID)
                                                  .ToListAsync();

            if (quotationRequests == null || quotationRequests.Count == 0)
            {
                return NotFound(new { message = "No quotation requests found for the given Customer ID." });
            }

            return Ok(quotationRequests);
        }

        // POST: api/QuotationRequest
        [HttpPost]
        public async Task<ActionResult> PostQuotationRequest(QuotationRequest quotationRequest)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(new { message = "Invalid quotation request data." });
            }

            _context.QuotationRequests.Add(quotationRequest);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Quotation request created successfully!" });
        }

        // PUT: api/QuotationRequest/{id}
        [HttpPut("{id}")]
        public async Task<IActionResult> PutQuotationRequest(int id, QuotationRequest quotationRequest)
        {
            if (id != quotationRequest.QuotationRequestID)
            {
                return BadRequest(new { message = "QuotationRequest ID mismatch." });
            }

            _context.Entry(quotationRequest).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Quotation request updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!QuotationRequestExists(id))
                {
                    return NotFound(new { message = "Quotation request not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/QuotationRequest/{id}
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteQuotationRequest(int id)
        {
            var quotationRequest = await _context.QuotationRequests.FindAsync(id);
            if (quotationRequest == null)
            {
                return NotFound(new { message = "Quotation request not found." });
            }

            _context.QuotationRequests.Remove(quotationRequest);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Quotation request deleted successfully!" });
        }

        private bool QuotationRequestExists(int id)
        {
            return _context.QuotationRequests.Any(e => e.QuotationRequestID == id);
        }
    }
}
