namespace API.DTOs;

public class CreateOrderDto
{
    public string Type { get; set; }

    public decimal Amount { get; set; }

    public string? Description { get; set; }

    public int? CustomerId { get; set; }
}