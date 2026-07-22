using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
using Domain.Entities;
using Microsoft.EntityFrameworkCore;
using API.DTOs;
using API.Authorization;
using API.Services;
using System.Security.Claims;


namespace API.Controllers;


[ApiController]
[Route("api/[controller]")]
[Authorize]
public class UsersController : ControllerBase
{

    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLogService;



    public UsersController(
        AppDbContext context,
        IAuditLogService auditLogService)
    {
        _context = context;
        _auditLogService = auditLogService;
    }






    // GET: api/users
    [HttpGet]
    [PermissionAuthorize("View_Users")]
    public async Task<IActionResult> GetAllUsers()
    {

        var users = await _context.Users

            .Include(u => u.Role)

            .Select(u => new UserResponseDto
            {
                Id = u.Id,

                Name = u.Name,

                Email = u.Email,

                Role = u.Role.Name,

                IsActive = u.IsActive

            })

            .ToListAsync();



        return Ok(users);

    }









    // POST: api/users
    [HttpPost]
    [PermissionAuthorize("Create_User")]
    public async Task<IActionResult> CreateUser(
        [FromBody] CreateUserDto dto)
    {


        var existingUser = await _context.Users

            .AnyAsync(u => u.Email == dto.Email);



        if (existingUser)

            return BadRequest(
                "Email already exists"
            );






        var roleExists = await _context.Roles
            .AnyAsync(r => r.Id == dto.RoleId);



        if (!roleExists)

            return BadRequest(
                "Invalid role"
            );







        var user = new User
        {

            Name = dto.Name,


            Email = dto.Email,



            PasswordHash =
                BCrypt.Net.BCrypt.HashPassword(
                    dto.Password
                ),



            RoleId = dto.RoleId,


            IsActive = true

        };







        _context.Users.Add(user);



        await _context.SaveChangesAsync();







        var currentUserId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );







        await _auditLogService.CreateAsync(
            "Create User",
            currentUserId
        );








        var createdUser = await _context.Users

            .Include(u => u.Role)

            .Where(u => u.Id == user.Id)

            .Select(u => new UserResponseDto
            {

                Id = u.Id,

                Name = u.Name,

                Email = u.Email,

                Role = u.Role.Name,

                IsActive = u.IsActive

            })

            .FirstAsync();






        return Ok(createdUser);

    }

}