using Domain.Entities;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Services;

public class TransactionService : ITransactionService
{
    private readonly AppDbContext _context;


    public TransactionService(AppDbContext context)
    {
        _context = context;
    }



    public async Task<List<Transaction>> GetAllAsync()
    {
        return await _context.Transactions
            .Include(t => t.User)
            .Include(t => t.Customer)
            .Include(t => t.Order)
            .ToListAsync();
    }



    public async Task<Transaction?> GetByIdAsync(int id)
    {
        return await _context.Transactions
            .Include(t => t.User)
            .Include(t => t.Customer)
            .Include(t => t.Order)
            .FirstOrDefaultAsync(t => t.Id == id);
    }
}