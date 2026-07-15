namespace Domain.Entities;

public class User
{
    public int Id { get; set; }

    public string Name { get; set; }

    public string Email { get; set; }

    public string PasswordHash { get; set; }


    public int RoleId { get; set; }

    public Role Role { get; set; }


    public ICollection<Order> Orders { get; set; } = new List<Order>();

    public ICollection<Approval> Approvals { get; set; } = new List<Approval>();

    public ICollection<Transaction> Transactions { get; set; } = new List<Transaction>();

    public ICollection<AuditLog> AuditLogs { get; set; } = new List<AuditLog>();
}