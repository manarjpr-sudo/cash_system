using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using API.DTOs;
using Domain.Entities;
using Domain.Enums;
using System.Security.Claims;
using Microsoft.EntityFrameworkCore;
using API.Services;

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
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );

        var order = new Order
        {
            Type = Enum.Parse<OrderType>(dto.Type),
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
            .Include(o => o.Approvals)
            .Include(o => o.Transactions)
            .ToListAsync();

        return Ok(orders);
    }


    [Authorize(Roles = "Admin")]
    [HttpPost("{id}/approve")]
    public async Task<IActionResult> ApproveOrder(
        int id,
        [FromServices] IOrderService orderService)
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );

        var result = await orderService.ApproveOrderAsync(id, userId);

        if (result == null)
            return BadRequest("Order cannot be approved");

        return Ok(result);
    }


    [Authorize(Roles = "Admin")]
    [HttpPost("{id}/reject")]
    public async Task<IActionResult> RejectOrder(
        int id,
        [FromServices] IOrderService orderService)
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );

        var result = await orderService.RejectOrderAsync(id, userId);

        if (result == null)
            return BadRequest("Order cannot be rejected");

        return Ok(result);
    }
}