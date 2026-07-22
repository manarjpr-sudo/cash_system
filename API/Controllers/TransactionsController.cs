using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using API.Services;
using API.DTOs;
using API.Authorization;
using System.Security.Claims;

namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
[PermissionAuthorize("View_Transactions")]
public class TransactionsController : ControllerBase
{

    private readonly ITransactionService _transactionService;


    public TransactionsController(
        ITransactionService transactionService)
    {
        _transactionService = transactionService;
    }







    // GET: api/transactions
    [HttpGet]
    public async Task<IActionResult> GetAllTransactions()
    {
        var userId = int.Parse(
            User.FindFirstValue(ClaimTypes.NameIdentifier)!
        );


        var role = User.FindFirstValue(ClaimTypes.Role);



        var transactions = await _transactionService.GetAllAsync();



        if (role != "Admin")
        {
            transactions = transactions
                .Where(t => t.UserId == userId)
                .ToList();
        }




        var result = transactions.Select(t => new TransactionResponseDto
        {
            Id = t.Id,

            Type = t.Type.ToString(),

            Status = t.Status.ToString(),

            Amount = t.Amount,

            Description = t.Description,

            CreatedAt = t.CreatedAt,


            OrderId = t.OrderId,


            UserId = t.UserId,

            UserName = t.User != null
                ? t.User.Name
                : null,


            CustomerId = t.CustomerId,

            CustomerName = t.Customer != null
                ? t.Customer.Name
                : null

        });


        return Ok(result);
    }







    // GET: api/transactions/{id}
    [HttpGet("{id}")]
    public async Task<IActionResult> GetTransactionById(
        int id)
    {

        var transaction =
            await _transactionService.GetByIdAsync(id);



        if (transaction == null)
            return NotFound(
                "Transaction not found"
            );




        var result = new TransactionResponseDto
        {
            Id = transaction.Id,

            Type = transaction.Type.ToString(),

            Status = transaction.Status.ToString(),

            Amount = transaction.Amount,

            Description = transaction.Description,

            CreatedAt = transaction.CreatedAt,


            OrderId = transaction.OrderId,


            UserId = transaction.UserId,

            UserName = transaction.User != null
                ? transaction.User.Name
                : null,


            CustomerId = transaction.CustomerId,

            CustomerName = transaction.Customer != null
                ? transaction.Customer.Name
                : null
        };



        return Ok(result);

    }

}