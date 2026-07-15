using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize]
public class SettingsController : ControllerBase
{
    private readonly AppDbContext _context;


    public SettingsController(AppDbContext context)
    {
        _context = context;
    }



    // GET: api/settings
    [HttpGet]
    public async Task<IActionResult> GetAllSettings()
    {
        var settings = await _context.Settings
            .ToListAsync();

        return Ok(settings);
    }



    // POST: api/settings
    [HttpPost]
    public async Task<IActionResult> CreateSetting(
        [FromBody] Setting setting)
    {
        var exists = await _context.Settings
            .AnyAsync(s => s.Key == setting.Key);


        if (exists)
            return BadRequest("Setting key already exists");


        _context.Settings.Add(setting);

        await _context.SaveChangesAsync();


        return Ok(setting);
    }



    // PUT: api/settings/{id}
    [HttpPut("{id}")]
    public async Task<IActionResult> UpdateSetting(
        int id,
        [FromBody] Setting setting)
    {
        var existing = await _context.Settings
            .FirstOrDefaultAsync(s => s.Id == id);


        if (existing == null)
            return NotFound("Setting not found");


        existing.Key = setting.Key;
        existing.Value = setting.Value;


        await _context.SaveChangesAsync();


        return Ok(existing);
    }



    // DELETE: api/settings/{id}
    [HttpDelete("{id}")]
    public async Task<IActionResult> DeleteSetting(int id)
    {
        var setting = await _context.Settings
            .FirstOrDefaultAsync(s => s.Id == id);


        if (setting == null)
            return NotFound("Setting not found");


        _context.Settings.Remove(setting);

        await _context.SaveChangesAsync();


        return Ok("Setting deleted");
    }
}