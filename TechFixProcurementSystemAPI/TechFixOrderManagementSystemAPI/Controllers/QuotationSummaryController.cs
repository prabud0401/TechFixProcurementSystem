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
    public class QuotationSummaryController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public QuotationSummaryController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/QuotationSummary
        [HttpGet]
        public async Task<ActionResult<IEnumerable<QuotationSummary>>> GetQuotationSummaries()
        {
            return await _context.QuotationSummarys.ToListAsync();
        }

        // GET: api/QuotationSummary/{id}
        [HttpGet("{id}")]
        public async Task<ActionResult<QuotationSummary>> GetQuotationSummary(int id)
        {
            var quotationSummary = await _context.QuotationSummarys.FindAsync(id);

            if (quotationSummary == null)
            {
                return NotFound(new { message = "Quotation summary not found." });
            }

            return Ok(quotationSummary);
        }

        // GET: api/QuotationSummary/byQuotationCode/{quotationCode}
        [HttpGet("byQuotationCode/{quotationCode}")]
        public async Task<ActionResult<IEnumerable<QuotationSummary>>> GetQuotationSummaryByQuotationCode(string quotationCode)
        {
            var quotationSummaries = await _context.QuotationSummarys
                                                   .Where(q => q.QuotationCode == quotationCode)
                                                   .ToListAsync();

            if (quotationSummaries == null || quotationSummaries.Count == 0)
            {
                return NotFound(new { message = "No quotation summaries found for the given Quotation Code." });
            }

            return Ok(quotationSummaries);
        }

        // GET: api/QuotationSummary/byCustomerID/{customerID}
        [HttpGet("byCustomerID/{customerID}")]
        public async Task<ActionResult<IEnumerable<QuotationSummary>>> GetQuotationSummariesByCustomerID(int customerID)
        {
            var quotationSummaries = await _context.QuotationSummarys
                                                   .Where(q => q.CustomerID == customerID)
                                                   .ToListAsync();

            if (quotationSummaries == null || quotationSummaries.Count == 0)
            {
                return NotFound(new { message = "No quotation summaries found for the given Customer ID." });
            }

            return Ok(quotationSummaries);
        }

        // GET: api/QuotationSummary/bySupplierID/{supplierID}
        [HttpGet("bySupplierID/{supplierID}")]
        public async Task<ActionResult<IEnumerable<QuotationSummary>>> GetQuotationSummariesBySupplierID(int supplierID)
        {
            var quotationSummaries = await _context.QuotationSummarys
                                                   .Where(q => q.SupplierID == supplierID)
                                                   .ToListAsync();

            if (quotationSummaries == null || quotationSummaries.Count == 0)
            {
                return NotFound(new { message = "No quotation summaries found for the given Supplier ID." });
            }

            return Ok(quotationSummaries);
        }

        // POST: api/QuotationSummary
        [HttpPost]
        public async Task<ActionResult> PostQuotationSummary(QuotationSummary quotationSummary)
        {
            if (!ModelState.IsValid)
            {
                return BadRequest(new { message = "Invalid quotation summary data." });
            }

            _context.QuotationSummarys.Add(quotationSummary);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Quotation summary created successfully!" });
        }

        // PUT: api/QuotationSummary/{id}
        [HttpPut("{id}")]
        public async Task<IActionResult> PutQuotationSummary(int id, QuotationSummary quotationSummary)
        {
            if (id != quotationSummary.QuotationSummaryID)
            {
                return BadRequest(new { message = "QuotationSummary ID mismatch." });
            }

            _context.Entry(quotationSummary).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Quotation summary updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!QuotationSummaryExists(id))
                {
                    return NotFound(new { message = "Quotation summary not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // PUT: api/QuotationSummary/UpdateStatus/{id} - Update only the status field
        [HttpPut("UpdateStatus/{id}")]
        public async Task<IActionResult> UpdateQuotationStatus(int id, [FromBody] string status)
        {
            var quotationSummary = await _context.QuotationSummarys.FindAsync(id);

            if (quotationSummary == null)
            {
                return NotFound(new { message = "Quotation summary not found." });
            }

            // Update only the status field
            quotationSummary.Status = status;

            _context.Entry(quotationSummary).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
                return Ok(new { message = "Quotation status updated successfully!" });
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!QuotationSummaryExists(id))
                {
                    return NotFound(new { message = "Quotation summary not found." });
                }
                else
                {
                    throw;
                }
            }
        }

        // DELETE: api/QuotationSummary/{id}
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteQuotationSummary(int id)
        {
            var quotationSummary = await _context.QuotationSummarys.FindAsync(id);
            if (quotationSummary == null)
            {
                return NotFound(new { message = "Quotation summary not found." });
            }

            _context.QuotationSummarys.Remove(quotationSummary);
            await _context.SaveChangesAsync();

            return Ok(new { message = "Quotation summary deleted successfully!" });
        }

        private bool QuotationSummaryExists(int id)
        {
            return _context.QuotationSummarys.Any(e => e.QuotationSummaryID == id);
        }
    }
}
