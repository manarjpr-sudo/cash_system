namespace API.DTOs;

public class UpdateUserDto
{
    public string Name { get; set; }

    public string Email { get; set; }

    public int RoleId { get; set; }

    public bool IsActive { get; set; }

    public string? Password { get; set; }
}