using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;
using API.DTOs;

namespace API.Controllers;

[ApiController]
[Route("api/[controller]")]
[Authorize(Roles = "Admin")]
public class UsersController : ControllerBase
{
    private readonly AppDbContext _context;


    public UsersController(AppDbContext context)
    {
        _context = context;
    }



    // GET: api/users
    [HttpGet]
    public async Task<IActionResult> GetAllUsers()
    {
        var users = await _context.Users
            .Include(u => u.Role)
            .Select(u => new UserResponseDto
            {
                Id = u.Id,
                Name = u.Name,
                Email = u.Email,
                Role = u.Role.Name
            })
            .ToListAsync();


        return Ok(users);
    }



    // POST: api/users
    [HttpPost]
    public async Task<IActionResult> CreateUser(
    [FromBody] CreateUserDto dto)
    {
        var existingUser = await _context.Users
            .AnyAsync(u => u.Email == dto.Email);

        if (existingUser)
            return BadRequest("Email already exists");


        var user = new User
        {
            Name = dto.Name,
            Email = dto.Email,

            PasswordHash = BCrypt.Net.BCrypt.HashPassword(
                dto.Password
            ),

            RoleId = dto.RoleId
        };

        
        _context.Users.Add(user);

        await _context.SaveChangesAsync();



        var createdUser = await _context.Users
            .Include(u => u.Role)
            .Where(u => u.Id == user.Id)
            .Select(u => new UserResponseDto
            {
                Id = u.Id,
                Name = u.Name,
                Email = u.Email,
                Role = u.Role.Name
            })
            .FirstAsync();


        return Ok(createdUser);
    }
}