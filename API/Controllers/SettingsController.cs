using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;
using API.Services;
using System.Security.Claims;
using API.Authorization;


namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
public class SettingsController : ControllerBase
{

    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLogService;



    public SettingsController(
        AppDbContext context,
        IAuditLogService auditLogService)
    {
        _context = context;
        _auditLogService = auditLogService;
    }







    // GET: api/settings
    [HttpGet]
    [PermissionAuthorize("Manage_Settings")]
    public async Task<IActionResult> GetAllSettings()
    {

        var settings = await _context.Settings
            .ToListAsync();


        return Ok(settings);

    }









    // POST: api/settings
    [HttpPost]
    [PermissionAuthorize("Manage_Settings")]
    public async Task<IActionResult> CreateSetting(
        [FromBody] Setting setting)
    {


        var exists = await _context.Settings
            .AnyAsync(s => s.Key == setting.Key);



        if (exists)
            return BadRequest(
                "Setting key already exists"
            );




        _context.Settings.Add(setting);


        await _context.SaveChangesAsync();




        var userId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );




        await _auditLogService.CreateAsync(
            "Create Setting",
            userId
        );




        return Ok(setting);

    }









    // PUT: api/settings/{id}
    [HttpPut("{id}")]
    [PermissionAuthorize("Manage_Settings")]
    public async Task<IActionResult> UpdateSetting(
        int id,
        [FromBody] Setting setting)
    {


        var existing = await _context.Settings
            .FirstOrDefaultAsync(
                s => s.Id == id
            );



        if (existing == null)
            return NotFound(
                "Setting not found"
            );




        existing.Key = setting.Key;

        existing.Value = setting.Value;




        await _context.SaveChangesAsync();




        var userId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );




        await _auditLogService.CreateAsync(
            "Update Setting",
            userId
        );




        return Ok(existing);

    }









    // DELETE: api/settings/{id}
    [HttpDelete("{id}")]
    [PermissionAuthorize("Manage_Settings")]
    public async Task<IActionResult> DeleteSetting(int id)
    {


        var setting = await _context.Settings
            .FirstOrDefaultAsync(
                s => s.Id == id
            );



        if (setting == null)
            return NotFound(
                "Setting not found"
            );




        _context.Settings.Remove(setting);


        await _context.SaveChangesAsync();




        var userId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );




        await _auditLogService.CreateAsync(
            "Delete Setting",
            userId
        );




        return Ok(
            "Setting deleted"
        );

    }

}