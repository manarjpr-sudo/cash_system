namespace API.Services;

public interface IAuditLogService
{
    Task CreateAsync(
        string action,
        int userId,
        int? orderId = null
    );

    Task<List<Domain.Entities.AuditLog>> GetAllAsync();
}