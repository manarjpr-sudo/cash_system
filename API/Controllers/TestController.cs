using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Controllers;

[ApiController]
[Route("api/test")]
public class TestController : ControllerBase
{
    private readonly AppDbContext _context;

    public TestController(AppDbContext context)
    {
        _context = context;
    }


    [HttpGet("permissions")]
    public async Task<IActionResult> GetPermissions()
    {
        var permissions = await _context.Permissions
            .ToListAsync();

        return Ok(permissions);
    }


    [HttpGet("role-permissions")]
    public async Task<IActionResult> GetRolePermissions()
    {
        var data = await _context.RolePermissions
            .Include(x => x.Role)
            .Include(x => x.Permission)
            .Select(x => new
            {
                Role = x.Role.Name,
                Permission = x.Permission.Name
            })
            .ToListAsync();

        return Ok(data);
    }
}