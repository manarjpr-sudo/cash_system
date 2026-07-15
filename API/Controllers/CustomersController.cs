using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;
using API.DTOs;

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
            .Select(c => new CustomerResponseDto
            {
                Id = c.Id,
                Name = c.Name,
                Phone = c.Phone,
                IdentityNumber = c.IdentityNumber,
                RoomNumber = c.RoomNumber
            })
            .ToListAsync();


        return Ok(customers);
    }




    // GET: api/customers/{id}
    [HttpGet("{id}")]
    public async Task<IActionResult> GetCustomerById(int id)
    {
        var customer = await _context.Customers
            .Where(c => c.Id == id)
            .Select(c => new CustomerResponseDto
            {
                Id = c.Id,
                Name = c.Name,
                Phone = c.Phone,
                IdentityNumber = c.IdentityNumber,
                RoomNumber = c.RoomNumber
            })
            .FirstOrDefaultAsync();


        if (customer == null)
            return NotFound("Customer not found");


        return Ok(customer);
    }





    // POST: api/customers
    [HttpPost]
    public async Task<IActionResult> CreateCustomer(Customer customer)
    {
        _context.Customers.Add(customer);

        await _context.SaveChangesAsync();


        return Ok(new CustomerResponseDto
        {
            Id = customer.Id,
            Name = customer.Name,
            Phone = customer.Phone,
            IdentityNumber = customer.IdentityNumber,
            RoomNumber = customer.RoomNumber
        });
    }





    // PUT: api/customers/{id}
    [HttpPut("{id}")]
    public async Task<IActionResult> UpdateCustomer(
        int id,
        Customer updatedCustomer)
    {
        var customer = await _context.Customers
            .FirstOrDefaultAsync(c => c.Id == id);


        if (customer == null)
            return NotFound("Customer not found");


        customer.Name = updatedCustomer.Name;
        customer.Phone = updatedCustomer.Phone;
        customer.IdentityNumber = updatedCustomer.IdentityNumber;
        customer.RoomNumber = updatedCustomer.RoomNumber;


        await _context.SaveChangesAsync();


        return Ok(new CustomerResponseDto
        {
            Id = customer.Id,
            Name = customer.Name,
            Phone = customer.Phone,
            IdentityNumber = customer.IdentityNumber,
            RoomNumber = customer.RoomNumber
        });
    }





    // DELETE: api/customers/{id}
    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteCustomer(int id)
    {
        var customer = await _context.Customers
            .FirstOrDefaultAsync(c => c.Id == id);


        if (customer == null)
            return NotFound("Customer not found");


        _context.Customers.Remove(customer);

        await _context.SaveChangesAsync();


        return Ok("Customer deleted successfully");
    }
}