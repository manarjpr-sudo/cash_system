namespace Domain.Entities;

public class Transaction
{
    public int Id { get; set; }

    // نوع الحركة: قبض / صرف
    public string Type { get; set; }

    // المبلغ
    public decimal Amount { get; set; }

    // الوصف
    public string? Description { get; set; }

    // تاريخ التنفيذ
    public DateTime CreatedAt { get; set; } = DateTime.UtcNow;


    // العملية المرتبطة
    public int? OrderId { get; set; }
    public Order? Order { get; set; }


    // الموظف الذي سجل الحركة
    public int UserId { get; set; }
    public User User { get; set; }
}