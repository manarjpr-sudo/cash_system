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





    public async Task<Order?> ApproveOrderAsync(
        int orderId,
        int userId)
    {

        using var transaction =
            await _context.Database.BeginTransactionAsync();


        try
        {

            var order = await _context.Orders
                .FirstOrDefaultAsync(o => o.Id == orderId);



            if (order == null)
                return null;



            if (order.Status != OrderStatus.Pending)
                return null;



            var existingTransaction =
                await _context.Transactions
                    .AnyAsync(t => t.OrderId == order.Id);



            if (existingTransaction)
                return null;





            order.Status = OrderStatus.Approved;





            var approval = new Approval
            {
                OrderId = order.Id,

                UserId = userId,

                Status = ApprovalStatus.Approved,

                CreatedAt = DateTime.UtcNow
            };


            _context.Approvals.Add(approval);





            var transactionType = order.Type switch
            {
                OrderType.Payment =>
                    TransactionType.Payment,


                OrderType.Receipt =>
                    TransactionType.Receipt,


                OrderType.Advance =>
                    TransactionType.Payment,


                _ => throw new InvalidOperationException(
                    "Invalid order type"
                )
            };





            var financialTransaction = new Transaction
            {
                Type = transactionType,

                Status = TransactionStatus.Completed,

                Amount = order.Amount,

                Description = order.Description,

                OrderId = order.Id,


                // صاحب الطلب
                UserId = order.UserId,


                CustomerId = order.CustomerId,


                CreatedAt = DateTime.UtcNow
            };



            _context.Transactions.Add(financialTransaction);



            await _context.SaveChangesAsync();





            await _auditLogService.CreateAsync(
                $"Approve Order #{order.Id}",
                userId,
                order.Id
            );





            await transaction.CommitAsync();



            return order;

        }

        catch
        {

            await transaction.RollbackAsync();

            throw;

        }

    }









    public async Task<Order?> RejectOrderAsync(
        int orderId,
        int userId)
    {

        using var transaction =
            await _context.Database.BeginTransactionAsync();



        try
        {

            var order = await _context.Orders
                .FirstOrDefaultAsync(o => o.Id == orderId);




            if (order == null)
                return null;




            if (order.Status != OrderStatus.Pending)
                return null;





            order.Status = OrderStatus.Rejected;





            var approval = new Approval
            {
                OrderId = order.Id,

                UserId = userId,

                Status = ApprovalStatus.Rejected,

                CreatedAt = DateTime.UtcNow
            };



            _context.Approvals.Add(approval);



            await _context.SaveChangesAsync();






            await _auditLogService.CreateAsync(
                $"Reject Order #{order.Id}",
                userId,
                order.Id
            );





            await transaction.CommitAsync();



            return order;

        }

        catch
        {

            await transaction.RollbackAsync();

            throw;

        }

    }

}