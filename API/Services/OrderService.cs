using Domain.Entities;
using Domain.Enums;
using Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace API.Services;

public class OrderService : IOrderService
{
    private readonly AppDbContext _context;
    private readonly IAuditLogService _auditLogService;

    public OrderService(
        AppDbContext context,
        IAuditLogService auditLogService)
    {
        _context = context;
        _auditLogService = auditLogService;
    }

    public async Task<Order?> ApproveOrderAsync(int orderId, int userId)
    {
        var order = await _context.Orders
            .FirstOrDefaultAsync(o => o.Id == orderId);


        if (order == null)
            return null;


        if (order.Status != OrderStatus.Pending)
            return null;



        // تغيير حالة الطلب
        order.Status = OrderStatus.Approved;

        // إنشاء سجل الموافقة
        var approval = new Approval
        {
            OrderId = order.Id,
            UserId = userId,
            Status = ApprovalStatus.Approved,
            CreatedAt = DateTime.UtcNow
        };

        _context.Approvals.Add(approval);

        // إنشاء الحركة المالية
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

        // تسجيل العملية في Audit Log
        await _auditLogService.CreateAsync(
            "Approve Order",
            userId,
            order.Id
        );

        return order;
    }

    public async Task<Order?> RejectOrderAsync(int orderId, int userId)
    {
        var order = await _context.Orders
            .FirstOrDefaultAsync(o => o.Id == orderId);


        if (order == null)
            return null;


        if (order.Status != OrderStatus.Pending)
            return null;

        // تغيير حالة الطلب
        order.Status = OrderStatus.Rejected;

        // تسجيل الرفض
        var approval = new Approval
        {
            OrderId = order.Id,
            UserId = userId,
            Status = ApprovalStatus.Rejected,
            CreatedAt = DateTime.UtcNow
        };

        _context.Approvals.Add(approval);

        await _context.SaveChangesAsync();

        // تسجيل العملية في Audit Log
        await _auditLogService.CreateAsync(
            "Reject Order",
            userId,
            order.Id
        );

        return order;
    }
}