using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using API.Services;
using API.Authorization;

namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
public class AuditLogsController : ControllerBase
{

    private readonly IAuditLogService _auditLogService;


    public AuditLogsController(
        IAuditLogService auditLogService)
    {
        _auditLogService = auditLogService;
    }

    // GET: api/auditlogs
    [HttpGet]
    [PermissionAuthorize("View_AuditLogs")]
    public async Task<IActionResult> GetAllAuditLogs()
    {

        var logs = await _auditLogService.GetAllAsync();



        var result = logs.Select(log => new
        {
            log.Id,

            log.Action,

            UserId = log.UserId,

            UserName = log.User != null
                ? log.User.Name
                : null,

            OrderId = log.OrderId,

            CreatedAt = log.CreatedAt
        });



        return Ok(result);

    }

}