namespace API.DTOs;

public class OrderResponseDto
{
    public int Id { get; set; }

    public string Type { get; set; }

    public string Status { get; set; }

    public decimal Amount { get; set; }

    public string? Description { get; set; }

    public DateTime CreatedAt { get; set; }

    public int UserId { get; set; }

    public string UserName { get; set; }

    public int? CustomerId { get; set; }

    public string? CustomerName { get; set; }
}