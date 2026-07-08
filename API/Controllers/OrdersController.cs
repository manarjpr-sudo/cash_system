using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using API.DTOs;
using System.Security.Claims;
using Microsoft.EntityFrameworkCore;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class OrdersController : ControllerBase
{
    private readonly AppDbContext _context;

    public OrdersController(AppDbContext context)
    {
        _context = context;
    }


    [HttpPost]
    public async Task<IActionResult> CreateOrder([FromBody] CreateOrderDto dto)
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)
        );

        var order = new Order
        {
            Type = dto.Type,
            Amount = dto.Amount,
            Description = dto.Description,
            UserId = userId,
            CustomerId = dto.CustomerId
        };

        _context.Orders.Add(order);

        await _context.SaveChangesAsync();

        return Ok(order);
    }
    [HttpGet]
    public async Task<IActionResult> GetAllOrders()
    {
        var orders = await _context.Orders
            .Include(o => o.Customer)
            .Include(o => o.User)
            .ToListAsync();

        return Ok(orders);
    }
}