namespace Domain.Entities;

public class Order
{
    public int Id { get; set; }

    public string Type { get; set; }

    public decimal Amount { get; set; }

    public DateTime CreatedAt { get; set; }

}