using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize(Roles = "Admin")]
public class AuditLogsController : ControllerBase
{
    private readonly AppDbContext _context;

    public AuditLogsController(AppDbContext context)
    {
        _context = context;
    }

    [HttpGet]
    public async Task<IActionResult> GetAll()
    {
        var logs = await _context.AuditLogs
            .Include(a => a.User)
            .Select(a => new
            {
                a.Id,
                a.Action,
                a.CreatedAt,
                UserId = a.UserId,
                UserName = a.User.Name
            })
            .OrderByDescending(a => a.CreatedAt)
            .ToListAsync();

        return Ok(logs);
    }
}