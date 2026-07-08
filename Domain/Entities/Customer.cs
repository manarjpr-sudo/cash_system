namespace Domain.Entities;

public class Customer
{
    public int Id { get; set; }

    public string Name { get; set; }

    public string? Phone { get; set; }

    public string? IdentityNumber { get; set; }

    public string? RoomNumber { get; set; }

    public ICollection<Order> Orders { get; set; } = new List<Order>();
}