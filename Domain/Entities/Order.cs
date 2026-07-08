namespace Domain.Entities;

public class Order
{
    public int Id { get; set; }

    // نوع الطلب (صرف - قبض - سلفة ...)
    public string Type { get; set; }

    // قيمة الطلب
    public decimal Amount { get; set; }

    // وصف مختصر
    public string? Description { get; set; }

    // حالة الطلب
    public string Status { get; set; } = "Pending";

    // تاريخ إنشاء الطلب
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;

    // مقدم الطلب
    public int UserId { get; set; }
    public User User { get; set; }

    // العميل (اختياري)
    public int? CustomerId { get; set; }
    public Customer? Customer { get; set; }
public ICollection<Approval> Approvals { get; set; } = new List<Approval>();

}