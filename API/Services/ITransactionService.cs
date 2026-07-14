using Domain.Entities;

namespace API.Services;

public interface ITransactionService
{
    Task<List<Transaction>> GetAllAsync();

    Task<Transaction?> GetByIdAsync(int id);
}