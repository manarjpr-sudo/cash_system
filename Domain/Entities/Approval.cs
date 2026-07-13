namespace Domain.Entities;
using Domain.Enums;

public class Approval
{
    public int Id { get; set; }

    // حالة الموافقة: Approved / Rejected
    public ApprovalStatus Status { get; set; }

    // ملاحظات المدير
    public string? Comment { get; set; }

    // تاريخ القرار
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;


    // الطلب المرتبط
    public int OrderId { get; set; }
    public Order Order { get; set; }


    // المستخدم الذي قام بالموافقة
    public int UserId { get; set; }
    public User User { get; set; }
}