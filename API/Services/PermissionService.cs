using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Services;

public class PermissionService
{
    private readonly AppDbContext _context;

    public PermissionService(AppDbContext context)
    {
        _context = context;
    }


    public async Task<bool> HasPermission(
        int userId,
        string permissionName)
    {

        var user = await _context.Users
            .Include(u => u.Role)
            .FirstOrDefaultAsync(u => u.Id == userId);


        if (user == null)
            return false;


        // Admin has full access
        if (user.Role.Name == "Admin")
            return true;



        return await _context.Users
            .Where(u => u.Id == userId)
            .SelectMany(u => u.Role.RolePermissions)
            .AnyAsync(rp =>
                rp.Permission.Name == permissionName
            );
    }
}