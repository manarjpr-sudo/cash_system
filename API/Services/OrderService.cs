using Domain.Entities;
using Domain.Enums;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Services;

public class OrderService : IOrderService
{
    private readonly AppDbContext _context;

    public OrderService(AppDbContext context)
    {
        _context = context;
    }


    public async Task<Order?> ApproveOrderAsync(int orderId, int userId)
    {
        // جلب الطلب
        var order = await _context.Orders
            .FirstOrDefaultAsync(o => o.Id == orderId);


        // إذا لم يوجد الطلب
        if (order == null)
            return null;


        // لا يسمح بالموافقة على طلب تمت معالجته سابقاً
        if (order.Status != OrderStatus.Pending)
            return null;


        // تغيير حالة الطلب
        order.Status = OrderStatus.Approved;


        // إنشاء سجل الموافقة
        var approval = new Approval
        {
            OrderId = order.Id,
            UserId = userId,
            Status = "Approved",
            CreatedAt = DateTime.UtcNow
        };

        _context.Approvals.Add(approval);


        // إنشاء الحركة المالية بعد الموافقة فقط
        var transaction = new Transaction
        {
            Type = (TransactionType)order.Type,
            Status = TransactionStatus.Completed,
            Amount = order.Amount,
            Description = order.Description,
            OrderId = order.Id,
            UserId = userId,
            CustomerId = order.CustomerId,
            CreatedAt = DateTime.UtcNow
        };


        _context.Transactions.Add(transaction);


        await _context.SaveChangesAsync();


        return order;
    }



    public async Task<Order?> RejectOrderAsync(int orderId, int userId)
    {
        // جلب الطلب
        var order = await _context.Orders
            .FirstOrDefaultAsync(o => o.Id == orderId);


        if (order == null)
            return null;


        // لا يسمح برفض طلب تمت معالجته
        if (order.Status != OrderStatus.Pending)
            return null;


        // تغيير الحالة فقط
        order.Status = OrderStatus.Rejected;


        // تسجيل الرفض
        var approval = new Approval
        {
            OrderId = order.Id,
            UserId = userId,
            Status = "Rejected",
            CreatedAt = DateTime.UtcNow
        };


        _context.Approvals.Add(approval);


        // لا يوجد Transaction هنا


        await _context.SaveChangesAsync();


        return order;
    }
}