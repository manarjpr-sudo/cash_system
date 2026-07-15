using Microsoft.EntityFrameworkCore;
using Domain.Entities;

namespace Infrastructure.Data;

public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options)
        : base(options)
    {
    }


    /* Master Data */

    public DbSet<User> Users { get; set; }

    public DbSet<Role> Roles { get; set; }

    public DbSet<Permission> Permissions { get; set; }

    public DbSet<RolePermission> RolePermissions { get; set; }

    public DbSet<Setting> Settings { get; set; }


    /* Operational Data */

    public DbSet<Order> Orders { get; set; }

    public DbSet<Customer> Customers { get; set; }

    public DbSet<Transaction> Transactions { get; set; }

    public DbSet<AuditLog> AuditLogs { get; set; }

    public DbSet<Approval> Approvals { get; set; }


    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);


        // Decimal precision for financial values
        modelBuilder.Entity<Order>()
            .Property(o => o.Amount)
            .HasPrecision(18, 2);

        modelBuilder.Entity<Transaction>()
            .Property(t => t.Amount)
            .HasPrecision(18, 2);



        // User - Role relationship
        modelBuilder.Entity<User>()
            .HasOne(u => u.Role)
            .WithMany(r => r.Users)
            .HasForeignKey(u => u.RoleId);



        // Role - Permission relationship
        modelBuilder.Entity<RolePermission>()
            .HasKey(rp => new { rp.RoleId, rp.PermissionId });


        modelBuilder.Entity<RolePermission>()
            .HasOne(rp => rp.Role)
            .WithMany(r => r.RolePermissions)
            .HasForeignKey(rp => rp.RoleId);


        modelBuilder.Entity<RolePermission>()
            .HasOne(rp => rp.Permission)
            .WithMany(p => p.RolePermissions)
            .HasForeignKey(rp => rp.PermissionId);



        // Seed default roles
        modelBuilder.Entity<Role>().HasData(
            new Role
            {
                Id = 1,
                Name = "Admin"
            },
            new Role
            {
                Id = 2,
                Name = "Cashier"
            }
        );

        modelBuilder.Entity<User>().HasData(
            new User
            {
                Id = 1,
                Name = "Admin",
                Email = "admin@example.com",
                PasswordHash = "$2a$11$9Q3m3wYzq3QxJ9L7H8u7UO7X4vQyQfYpK5WqK9v1Q0QmQm1QmQmQm",
                RoleId = 1
            }
        );

        // Seed default permissions
        modelBuilder.Entity<Permission>().HasData(
            new Permission
            {
                Id = 1,
                Name = "Create_User"
            },
            new Permission
            {
                Id = 2,
                Name = "View_Users"
            },
            new Permission
            {
                Id = 3,
                Name = "Create_Order"
            },
            new Permission
            {
                Id = 4,
                Name = "Approve_Order"
            },
            new Permission
            {
                Id = 5,
                Name = "Reject_Order"
            },
            new Permission
            {
                Id = 6,
                Name = "View_Orders"
            },
            new Permission
            {
                Id = 7,
                Name = "View_Transactions"
            },
            new Permission
            {
                Id = 8,
                Name = "Manage_Settings"
            }
        );

        // Seed Role Permissions
        modelBuilder.Entity<RolePermission>().HasData(

            // Admin permissions
            new RolePermission { RoleId = 1, PermissionId = 1 },
            new RolePermission { RoleId = 1, PermissionId = 2 },
            new RolePermission { RoleId = 1, PermissionId = 3 },
            new RolePermission { RoleId = 1, PermissionId = 4 },
            new RolePermission { RoleId = 1, PermissionId = 5 },
            new RolePermission { RoleId = 1, PermissionId = 6 },
            new RolePermission { RoleId = 1, PermissionId = 7 },
            new RolePermission { RoleId = 1, PermissionId = 8 },


            // Cashier permissions
            new RolePermission { RoleId = 2, PermissionId = 3 },
            new RolePermission { RoleId = 2, PermissionId = 6 },
            new RolePermission { RoleId = 2, PermissionId = 7 }

        );


        // Approval - Order relationship
        modelBuilder.Entity<Approval>()
            .HasOne(a => a.Order)
            .WithMany(o => o.Approvals)
            .HasForeignKey(a => a.OrderId)
            .OnDelete(DeleteBehavior.Restrict);



        // Enum conversions

        modelBuilder.Entity<Order>()
            .Property(o => o.Type)
            .HasConversion<string>();

        modelBuilder.Entity<Order>()
            .Property(o => o.Status)
            .HasConversion<string>();

        modelBuilder.Entity<Transaction>()
            .Property(t => t.Type)
            .HasConversion<string>();

        modelBuilder.Entity<Transaction>()
            .Property(t => t.Status)
            .HasConversion<string>();

        modelBuilder.Entity<Approval>()
            .Property(a => a.Status)
            .HasConversion<string>();
    }
}