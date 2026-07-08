namespace Domain.Entities;

public class AuditLog
{
    public int Id { get; set; }

    // العملية التي حدثت
    public string Action { get; set; }

    // التاريخ
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;


    // المستخدم الذي قام بالفعل
    public int UserId { get; set; }
    public User User { get; set; }


    // الطلب المرتبط
    public int? OrderId { get; set; }
    public Order? Order { get; set; }
}