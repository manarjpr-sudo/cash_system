using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using API.DTOs;
using Domain.Entities;
using Domain.Enums;
using System.Security.Claims;
using Microsoft.EntityFrameworkCore;
using API.Services;

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


        return Ok(new
        {
            order.Id,
            Type = order.Type.ToString(),
            Status = order.Status.ToString(),
            order.Amount,
            order.Description,
            order.CreatedAt,
            order.UserId,
            order.CustomerId
        });
    }



    [HttpGet]
    public async Task<IActionResult> GetAllOrders()
    {
        var orders = await _context.Orders
            .Include(o => o.User)
            .Include(o => o.Customer)
            .ToListAsync();


        var result = orders.Select(o => new OrderResponseDto
        {
            Id = o.Id,
            Type = o.Type.ToString(),
            Status = o.Status.ToString(),
            Amount = o.Amount,
            Description = o.Description,
            CreatedAt = o.CreatedAt,
            UserId = o.UserId,
            UserName = o.User.Name,
            CustomerId = o.CustomerId,
            CustomerName = o.Customer != null 
                ? o.Customer.Name 
                : null
        });


        return Ok(result);
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