using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using API.DTOs;
using Domain.Entities;
using Domain.Enums;
using System.Security.Claims;
using Microsoft.EntityFrameworkCore;
using API.Services;
using API.Authorization;


namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
public class OrdersController : ControllerBase
{

    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLogService;



    public OrdersController(
        AppDbContext context,
        IAuditLogService auditLogService)
    {
        _context = context;
        _auditLogService = auditLogService;
    }








    // POST: api/orders
    [HttpPost]
    [PermissionAuthorize("Create_Order")]
    public async Task<IActionResult> CreateOrder(
        [FromBody] CreateOrderDto dto)
    {

        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );



        if (!Enum.TryParse<OrderType>(
            dto.Type,
            true,
            out var orderType))
        {
            return BadRequest("Invalid order type");
        }




        var order = new Order
        {
            Type = orderType,

            Amount = dto.Amount,

            Description = dto.Description,

            UserId = userId,

            CustomerId = dto.CustomerId
        };



        _context.Orders.Add(order);


        await _context.SaveChangesAsync();




        await _auditLogService.CreateAsync(
            "Create Order",
            userId,
            order.Id
        );





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










    // GET: api/orders
    [HttpGet]
    [PermissionAuthorize("View_Orders")]
    public async Task<IActionResult> GetAllOrders()
    {

        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );


        var role = User.FindFirstValue(ClaimTypes.Role);



        var query = _context.Orders

            .Include(o => o.User)

            .Include(o => o.Customer)

            .AsQueryable();





        if (role != "Admin")
        {
            query = query
                .Where(o => o.UserId == userId);
        }





        var orders = await query.ToListAsync();






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









    // GET: api/orders/{id}
    [HttpGet("{id}")]
    [PermissionAuthorize("View_Orders")]
    public async Task<IActionResult> GetOrderById(int id)
    {

        var order = await _context.Orders

            .Include(o => o.User)

            .Include(o => o.Customer)

            .FirstOrDefaultAsync(o => o.Id == id);



        if (order == null)
            return NotFound("Order not found");




        var result = new OrderResponseDto
        {

            Id = order.Id,

            Type = order.Type.ToString(),

            Status = order.Status.ToString(),

            Amount = order.Amount,

            Description = order.Description,

            CreatedAt = order.CreatedAt,

            UserId = order.UserId,

            UserName = order.User.Name,

            CustomerId = order.CustomerId,

            CustomerName = order.Customer != null
                ? order.Customer.Name
                : null

        };



        return Ok(result);

    }









    // POST: api/orders/{id}/approve
    [HttpPost("{id}/approve")]
    [PermissionAuthorize("Approve_Order")]
    public async Task<IActionResult> ApproveOrder(
        int id,
        [FromServices] IOrderService orderService)
    {


        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );




        var result =
            await orderService.ApproveOrderAsync(
                id,
                userId
            );




        if (result == null)
            return BadRequest(
                "Order cannot be approved"
            );





        return Ok(new OrderActionResponseDto
        {

            Id = result.Id,

            Type = result.Type.ToString(),

            Status = result.Status.ToString(),

            Amount = result.Amount,

            Description = result.Description,

            CreatedAt = result.CreatedAt,

            UserId = result.UserId,

            CustomerId = result.CustomerId

        });

    }









    // POST: api/orders/{id}/reject
    [HttpPost("{id}/reject")]
    [PermissionAuthorize("Reject_Order")]
    public async Task<IActionResult> RejectOrder(
        int id,
        [FromServices] IOrderService orderService)
    {


        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );




        var result =
            await orderService.RejectOrderAsync(
                id,
                userId
            );





        if (result == null)
            return BadRequest(
                "Order cannot be rejected"
            );





        return Ok(new OrderActionResponseDto
        {

            Id = result.Id,

            Type = result.Type.ToString(),

            Status = result.Status.ToString(),

            Amount = result.Amount,

            Description = result.Description,

            CreatedAt = result.CreatedAt,

            UserId = result.UserId,

            CustomerId = result.CustomerId

        });

    }

}