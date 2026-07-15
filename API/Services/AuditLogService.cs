using Domain.Entities;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Services;

public class AuditLogService : IAuditLogService
{
    private readonly AppDbContext _context;


    public AuditLogService(AppDbContext context)
    {
        _context = context;
    }



    public async Task CreateAsync(
        string action,
        int userId,
        int? orderId = null)
    {
        var log = new AuditLog
        {
            Action = action,
            UserId = userId,
            OrderId = orderId,
            CreatedAt = DateTime.UtcNow
        };

        _context.AuditLogs.Add(log);

        await _context.SaveChangesAsync();
    }



    public async Task<List<AuditLog>> GetAllAsync()
    {
        return await _context.AuditLogs
            .Include(a => a.User)
            .Include(a => a.Order)
            .ToListAsync();
    }
}