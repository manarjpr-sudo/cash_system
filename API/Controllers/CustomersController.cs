using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;
using API.DTOs;
using API.Authorization;
using API.Services;
using System.Security.Claims;

namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
public class CustomersController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLogService;


    public CustomersController(
        AppDbContext context,
        IAuditLogService auditLogService)
    {
        _context = context;
        _auditLogService = auditLogService;
    }



    // GET: api/customers
    [HttpGet]
    [PermissionAuthorize("View_Customers")]
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
    [PermissionAuthorize("View_Customers")]
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
    [PermissionAuthorize("Create_Customer")]
    public async Task<IActionResult> CreateCustomer(
        Customer customer)
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );


        _context.Customers.Add(customer);

        await _context.SaveChangesAsync();



        await _auditLogService.CreateAsync(
            "Create Customer",
            userId
        );



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
    [PermissionAuthorize("Edit_Customer")]
    public async Task<IActionResult> UpdateCustomer(
        int id,
        Customer updatedCustomer)
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );


        var customer = await _context.Customers
            .FirstOrDefaultAsync(c => c.Id == id);



        if (customer == null)
            return NotFound("Customer not found");



        customer.Name = updatedCustomer.Name;
        customer.Phone = updatedCustomer.Phone;
        customer.IdentityNumber = updatedCustomer.IdentityNumber;
        customer.RoomNumber = updatedCustomer.RoomNumber;



        await _context.SaveChangesAsync();



        await _auditLogService.CreateAsync(
            "Update Customer",
            userId
        );



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
    [PermissionAuthorize("Delete_Customer")]
    public async Task<IActionResult> DeleteCustomer(int id)
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );


        var customer = await _context.Customers
            .FirstOrDefaultAsync(c => c.Id == id);



        if (customer == null)
            return NotFound("Customer not found");



        _context.Customers.Remove(customer);

        await _context.SaveChangesAsync();



        await _auditLogService.CreateAsync(
            "Delete Customer",
            userId
        );



        return Ok("Customer deleted successfully");
    }
}