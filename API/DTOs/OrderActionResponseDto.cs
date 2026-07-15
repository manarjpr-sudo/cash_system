namespace API.DTOs;

public class OrderActionResponseDto
{
    public int Id { get; set; }

    public string Type { get; set; }

    public string Status { get; set; }

    public decimal Amount { get; set; }

    public string? Description { get; set; }

    public DateTime CreatedAt { get; set; }

    public int UserId { get; set; }

    public int? CustomerId { get; set; }
}