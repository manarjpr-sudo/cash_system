namespace Domain.Entities;

public class User
{
    public int Id { get; set; }

    public string Name { get; set; }

    public string Email { get; set; }

    public string Password { get; set; }


    public int RoleId { get; set; }

    public Role Role { get; set; }


    // الطلبات التي أنشأها المستخدم
    public ICollection<Order> Orders { get; set; } = new List<Order>();


    // الموافقات التي قام بها المستخدم
    public ICollection<Approval> Approvals { get; set; } = new List<Approval>();


    // الحركات المالية التي سجلها المستخدم
    public ICollection<Transaction> Transactions { get; set; } = new List<Transaction>();


    // سجل العمليات التي قام بها المستخدم
    public ICollection<AuditLog> AuditLogs { get; set; } = new List<AuditLog>();
}