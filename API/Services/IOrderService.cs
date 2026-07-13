using Domain.Entities;
namespace API.Services;

public interface IOrderService
{
    Task<Order?> ApproveOrderAsync(int orderId, int userId);
    Task<Order?> RejectOrderAsync(int orderId, int userId);
}