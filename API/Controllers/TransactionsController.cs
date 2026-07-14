using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using API.Services;
using API.DTOs;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class TransactionsController : ControllerBase
{
    private readonly ITransactionService _transactionService;


    public TransactionsController(ITransactionService transactionService)
    {
        _transactionService = transactionService;
    }



    // GET: api/transactions
    [HttpGet]
    public async Task<IActionResult> GetAllTransactions()
    {
        var transactions = await _transactionService.GetAllAsync();


        var result = transactions.Select(t => new TransactionResponseDto
        {
            Id = t.Id,
            Type = t.Type.ToString(),
            Status = t.Status.ToString(),
            Amount = t.Amount,
            Description = t.Description,
            CreatedAt = t.CreatedAt,
            OrderId = t.OrderId ?? 0,
            UserId = t.UserId,
            UserName = t.User.Name
        });


        return Ok(result);
    }



    // GET: api/transactions/{id}
    [HttpGet("{id}")]
    public async Task<IActionResult> GetTransactionById(int id)
    {
        var transaction = await _transactionService.GetByIdAsync(id);


        if (transaction == null)
            return NotFound("Transaction not found");


        var result = new TransactionResponseDto
        {
            Id = transaction.Id,
            Type = transaction.Type.ToString(),
            Status = transaction.Status.ToString(),
            Amount = transaction.Amount,
            Description = transaction.Description,
            CreatedAt = transaction.CreatedAt,
            OrderId = transaction.OrderId ?? 0,
            UserId = transaction.UserId,
            UserName = transaction.User.Name
        };


        return Ok(result);
    }
}