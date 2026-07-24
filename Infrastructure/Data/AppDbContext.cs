using Microsoft.EntityFrameworkCore;
using Domain.Entities;

namespace Infrastructure.Data;

public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options)
        : base(options)
    {
    }


    public DbSet<User> Users { get; set; }

    public DbSet<Role> Roles { get; set; }

    public DbSet<Permission> Permissions { get; set; }

    public DbSet<RolePermission> RolePermissions { get; set; }

    public DbSet<Setting> Settings { get; set; }

    public DbSet<Order> Orders { get; set; }

    public DbSet<Customer> Customers { get; set; }

    public DbSet<Transaction> Transactions { get; set; }

    public DbSet<AuditLog> AuditLogs { get; set; }

    public DbSet<Approval> Approvals { get; set; }



    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        base.OnModelCreating(modelBuilder);



        // Decimal precision

        modelBuilder.Entity<Order>()
            .Property(o => o.Amount)
            .HasPrecision(18, 2);


        modelBuilder.Entity<Transaction>()
            .Property(t => t.Amount)
            .HasPrecision(18, 2);




        // User - Role

        modelBuilder.Entity<User>()
            .HasOne(u => u.Role)
            .WithMany(r => r.Users)
            .HasForeignKey(u => u.RoleId)
            .OnDelete(DeleteBehavior.Restrict);




        // Role Permission

        modelBuilder.Entity<RolePermission>()
            .HasKey(rp => new
            {
                rp.RoleId,
                rp.PermissionId
            });



        modelBuilder.Entity<RolePermission>()
            .HasOne(rp => rp.Role)
            .WithMany(r => r.RolePermissions)
            .HasForeignKey(rp => rp.RoleId)
            .OnDelete(DeleteBehavior.Restrict);



        modelBuilder.Entity<RolePermission>()
            .HasOne(rp => rp.Permission)
            .WithMany(p => p.RolePermissions)
            .HasForeignKey(rp => rp.PermissionId)
            .OnDelete(DeleteBehavior.Restrict);




        // Order - User

        modelBuilder.Entity<Order>()
            .HasOne(o => o.User)
            .WithMany(u => u.Orders)
            .HasForeignKey(o => o.UserId)
            .OnDelete(DeleteBehavior.Restrict);



        // Order - Customer

        modelBuilder.Entity<Order>()
            .HasOne(o => o.Customer)
            .WithMany(c => c.Orders)
            .HasForeignKey(o => o.CustomerId)
            .OnDelete(DeleteBehavior.Restrict);




        // Transaction - Order

        modelBuilder.Entity<Transaction>()
            .HasOne(t => t.Order)
            .WithMany(o => o.Transactions)
            .HasForeignKey(t => t.OrderId)
            .OnDelete(DeleteBehavior.Restrict);



        // Transaction - User

        modelBuilder.Entity<Transaction>()
            .HasOne(t => t.User)
            .WithMany(u => u.Transactions)
            .HasForeignKey(t => t.UserId)
            .OnDelete(DeleteBehavior.Restrict);



        // Transaction - Customer

        modelBuilder.Entity<Transaction>()
            .HasOne(t => t.Customer)
            .WithMany()
            .HasForeignKey(t => t.CustomerId)
            .OnDelete(DeleteBehavior.Restrict);




        // Approval - Order

        modelBuilder.Entity<Approval>()
            .HasOne(a => a.Order)
            .WithMany(o => o.Approvals)
            .HasForeignKey(a => a.OrderId)
            .OnDelete(DeleteBehavior.Restrict);



        // Approval - User

        modelBuilder.Entity<Approval>()
            .HasOne(a => a.User)
            .WithMany(u => u.Approvals)
            .HasForeignKey(a => a.UserId)
            .OnDelete(DeleteBehavior.Restrict);




        // AuditLog - User

        modelBuilder.Entity<AuditLog>()
            .HasOne(a => a.User)
            .WithMany(u => u.AuditLogs)
            .HasForeignKey(a => a.UserId)
            .OnDelete(DeleteBehavior.Restrict);



        // AuditLog - Order

        modelBuilder.Entity<AuditLog>()
            .HasOne(a => a.Order)
            .WithMany()
            .HasForeignKey(a => a.OrderId)
            .OnDelete(DeleteBehavior.Restrict);




        // Seed Roles

        modelBuilder.Entity<Role>()
            .HasData(

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




        // Seed Admin

        modelBuilder.Entity<User>()
            .HasData(

                new User
                {
                    Id = 1,
                    Name = "Admin",
                    Email = "admin@example.com",
                    PasswordHash = "$2a$11$9Q3m3wYzq3QxJ9L7H8u7UO7X4vQyQfYpK5WqK9v1Q0QmQm1QmQmQm",
                    RoleId = 1,
                    IsActive = true,
                    CreatedAt = new DateTime(2026, 7, 24, 0, 0, 0, DateTimeKind.Utc)
                }

            );



        // Permissions

        modelBuilder.Entity<Permission>()
            .HasData(

                new Permission { Id = 9, Name = "View_Customers" },
                new Permission { Id = 10, Name = "Create_Customer" },
                new Permission { Id = 11, Name = "Edit_Customer" },
                new Permission { Id = 12, Name = "Delete_Customer" },
                new Permission { Id = 13, Name = "View_AuditLogs" },
                new Permission { Id = 14, Name = "View_Dashboard" },
                new Permission { Id = 15, Name = "Edit_User" },
                new Permission { Id = 16, Name = "Delete_User" }
            );




        // Role Permissions

        modelBuilder.Entity<RolePermission>()
            .HasData(

                new RolePermission { RoleId = 1, PermissionId = 1 },
                new RolePermission { RoleId = 1, PermissionId = 2 },
                new RolePermission { RoleId = 1, PermissionId = 3 },
                new RolePermission { RoleId = 1, PermissionId = 4 },
                new RolePermission { RoleId = 1, PermissionId = 5 },
                new RolePermission { RoleId = 1, PermissionId = 6 },
                new RolePermission { RoleId = 1, PermissionId = 7 },
                new RolePermission { RoleId = 1, PermissionId = 8 },

                new RolePermission { RoleId = 1, PermissionId = 9 },
                new RolePermission { RoleId = 1, PermissionId = 10 },
                new RolePermission { RoleId = 1, PermissionId = 11 },
                new RolePermission { RoleId = 1, PermissionId = 12 },
                new RolePermission { RoleId = 1, PermissionId = 13 },
                new RolePermission { RoleId = 1, PermissionId = 14 },
                new RolePermission { RoleId = 1, PermissionId = 15 },
                new RolePermission { RoleId = 1, PermissionId = 16 },

                new RolePermission { RoleId = 2, PermissionId = 3 },
                new RolePermission { RoleId = 2, PermissionId = 6 },
                new RolePermission { RoleId = 2, PermissionId = 7 }

            );




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