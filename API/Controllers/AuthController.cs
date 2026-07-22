using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;
using API.DTOs;
using API.Services;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
public class AuthController : ControllerBase
{
    private readonly AppDbContext _context;
    private readonly JwtTokenService _jwtService;


    public AuthController(
        AppDbContext context,
        JwtTokenService jwtService)
    {
        _context = context;
        _jwtService = jwtService;
    }



    [HttpPost("login")]
    public async Task<IActionResult> Login(
        [FromBody] LoginDto dto)
    {

        var user = await _context.Users
            .Include(u => u.Role)
                .ThenInclude(r => r.RolePermissions)
                    .ThenInclude(rp => rp.Permission)
            .FirstOrDefaultAsync(
                u => u.Email == dto.Email
            );


        if (user == null)
            return Unauthorized("Invalid credentials");



        var validPassword =
            BCrypt.Net.BCrypt.Verify(
                dto.Password,
                user.PasswordHash
            );



        if (!validPassword)
            return Unauthorized("Invalid credentials");



        var token = _jwtService.CreateToken(user);



        var permissions = user.Role.RolePermissions
            .Select(rp => rp.Permission.Name)
            .ToList();



        return Ok(new
        {
            token,

            user = new
            {
                user.Id,

                user.Name,

                user.Email,

                Role = user.Role.Name,

                Permissions = permissions
            }
        });

    }
}