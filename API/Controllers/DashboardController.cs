using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;
using Domain.Enums;
using System.Security.Claims;

namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
public class DashboardController : ControllerBase
{

    private readonly AppDbContext _context;


    public DashboardController(AppDbContext context)
    {
        _context = context;
    }



    [HttpGet]
    public async Task<IActionResult> GetDashboard()
    {

        var userId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );


        var role = User.FindFirstValue(
            ClaimTypes.Role
        );



        var isAdmin = role == "Admin";



        var ordersQuery = _context.Orders
            .AsQueryable();



        var transactionsQuery = _context.Transactions
            .AsQueryable();



        if (!isAdmin)
        {
            ordersQuery = ordersQuery
                .Where(o => o.UserId == userId);


            transactionsQuery = transactionsQuery
                .Where(t => t.UserId == userId);
        }






        var totalCustomers =
            await _context.Customers.CountAsync();




        var totalUsers =
            isAdmin
            ? await _context.Users.CountAsync()
            : 0;





        var totalOrders =
            await ordersQuery.CountAsync();





        var pendingOrders =
            await ordersQuery.CountAsync(o =>
                o.Status == OrderStatus.Pending);




        var approvedOrders =
            await ordersQuery.CountAsync(o =>
                o.Status == OrderStatus.Approved);




        var rejectedOrders =
            await ordersQuery.CountAsync(o =>
                o.Status == OrderStatus.Rejected);





        var totalTransactions =
            await transactionsQuery.CountAsync();





        var totalAmount =
            await transactionsQuery

            .Where(t =>
                t.Status == TransactionStatus.Completed)

            .SumAsync(t => t.Amount);







        var latestTransactions =
            await transactionsQuery

            .OrderByDescending(t => t.CreatedAt)

            .Take(5)

            .Select(t => new
            {
                t.Id,

                Type = t.Type.ToString(),

                t.Amount,

                t.Description,

                t.CreatedAt
            })

            .ToListAsync();







        return Ok(new
        {
            role,

            totalCustomers,

            totalUsers,

            totalOrders,

            pendingOrders,

            approvedOrders,

            rejectedOrders,

            totalTransactions,

            totalAmount,

            latestTransactions
        });

    }

}