namespace API.DTOs;

public class AuditLogResponseDto
{
    public int Id { get; set; }

    public string Action { get; set; }

    public DateTime CreatedAt { get; set; }


    public int UserId { get; set; }

    public string UserName { get; set; }


    public int? OrderId { get; set; }
}