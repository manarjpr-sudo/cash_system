using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class CustomersController : ControllerBase
{
    private readonly AppDbContext _context;

    public CustomersController(AppDbContext context)
    {
        _context = context;
    }


    // GET: api/customers
    [HttpGet]
    public async Task<IActionResult> GetAllCustomers()
    {
        var customers = await _context.Customers
            .Include(c => c.Orders)
            .ToListAsync();

        return Ok(customers);
    }


    // POST: api/customers
    [HttpPost]
    public async Task<IActionResult> CreateCustomer(Customer customer)
    {
        _context.Customers.Add(customer);

        await _context.SaveChangesAsync();

        return Ok(customer);
    }
}