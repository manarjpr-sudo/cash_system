using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Infrastructure.Data;
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




        var user = new Domain.Entities.User
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



        return await GetUserResponse(user.Id);

    }









    // PUT: api/users/{id}
    [HttpPut("{id}")]
    [PermissionAuthorize("Edit_User")]
    public async Task<IActionResult> UpdateUser(
        int id,
        [FromBody] UpdateUserDto dto)
    {

        var user = await _context.Users
            .FirstOrDefaultAsync(u => u.Id == id);



        if (user == null)

            return NotFound(
                "User not found"
            );





        var emailExists = await _context.Users

            .AnyAsync(u =>
                u.Email == dto.Email &&
                u.Id != id
            );



        if (emailExists)

            return BadRequest(
                "Email already exists"
            );






        user.Name = dto.Name;

        user.Email = dto.Email;

        user.RoleId = dto.RoleId;

        user.IsActive = dto.IsActive;




        // تغيير كلمة المرور فقط إذا تم إرسالها
        if (!string.IsNullOrWhiteSpace(dto.Password))
        {
            user.PasswordHash =
                BCrypt.Net.BCrypt.HashPassword(
                    dto.Password
                );
        }





        await _context.SaveChangesAsync();





        var currentUserId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );




        await _auditLogService.CreateAsync(
            "Update User",
            currentUserId
        );





        return await GetUserResponse(id);

    }









    private async Task<IActionResult> GetUserResponse(int id)
    {

        var user = await _context.Users

            .Include(u => u.Role)

            .Where(u => u.Id == id)

            .Select(u => new UserResponseDto
            {

                Id = u.Id,

                Name = u.Name,

                Email = u.Email,

                Role = u.Role.Name,

                IsActive = u.IsActive

            })

            .FirstAsync();



        return Ok(user);

    }


    // DELETE: api/users/{id}
    [HttpDelete("{id}")]
    [PermissionAuthorize("Delete_User")]
    public async Task<IActionResult> DeleteUser(int id)
    {
        var user = await _context.Users
            .FirstOrDefaultAsync(u => u.Id == id);


        if (user == null)
            return NotFound("User not found");


        _context.Users.Remove(user);

        await _context.SaveChangesAsync();


        var currentUserId = int.Parse(
            User.FindFirstValue(
                ClaimTypes.NameIdentifier
            )!
        );


        await _auditLogService.CreateAsync(
            "Delete User",
            currentUserId
        );


        return Ok("User deleted successfully");
    }


}