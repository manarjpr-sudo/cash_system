namespace API.DTOs;

public class CustomerResponseDto
{
    public int Id { get; set; }

    public string Name { get; set; }

    public string Phone { get; set; }

    public string? IdentityNumber { get; set; }

    public string? RoomNumber { get; set; }
}