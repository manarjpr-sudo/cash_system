using API.Services;
using Infrastructure.Data;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using System.Text;
using Domain.Entities;
using API.Authorization;
using Microsoft.AspNetCore.Authorization;

var builder = WebApplication.CreateBuilder(args);


// ===============================
// Database
// ===============================
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlServer(
        builder.Configuration.GetConnectionString("DefaultConnection")
    ));


// ===============================
// Controllers
// ===============================
builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        options.JsonSerializerOptions.ReferenceHandler =
            System.Text.Json.Serialization.ReferenceHandler.IgnoreCycles;
    });


// ===============================
// Swagger
// ===============================
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();


// ===============================
// Dependency Injection
// ===============================
builder.Services.AddScoped<IOrderService, OrderService>();
builder.Services.AddScoped<ITransactionService, TransactionService>();
builder.Services.AddScoped<IAuditLogService, AuditLogService>();
builder.Services.AddScoped<JwtTokenService>();
builder.Services.AddScoped<PermissionService>();
builder.Services.AddScoped<IAuthorizationHandler, PermissionAuthorizationHandler>();
builder.Services.AddSingleton<IAuthorizationPolicyProvider, PermissionPolicyProvider>();

// ===============================
// JWT Authentication
// ===============================
var jwtSettings = builder.Configuration.GetSection("Jwt");

var key = Encoding.UTF8.GetBytes(
    jwtSettings["Key"]!
);

builder.Services.AddAuthentication(
    JwtBearerDefaults.AuthenticationScheme
)
.AddJwtBearer(options =>
{
    options.TokenValidationParameters = new TokenValidationParameters
    {
        ValidateIssuer = true,
        ValidateAudience = true,
        ValidateLifetime = true,
        ValidateIssuerSigningKey = true,

        ValidIssuer = jwtSettings["Issuer"],
        ValidAudience = jwtSettings["Audience"],

        IssuerSigningKey = new SymmetricSecurityKey(key)
    };
});


// ===============================
// Authorization
// ===============================
builder.Services.AddAuthorization();

// ===============================
// Build Application
// ===============================
var app = builder.Build();

using (var scope = app.Services.CreateScope())
{
    var context = scope.ServiceProvider.GetRequiredService<AppDbContext>();

    if (!context.Users.Any())
    {
        context.Users.Add(new User
        {
            Name = "Admin",
            Email = "admin@example.com",
            PasswordHash = BCrypt.Net.BCrypt.HashPassword("123456"),
            RoleId = 1
        });

        context.SaveChanges();
    }
}

// ===============================
// Swagger UI
// ===============================
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}


// ===============================
// HTTP Pipeline
// ===============================
app.UseHttpsRedirection();

app.UseAuthentication();

app.UseAuthorization();

app.MapControllers();


app.Run();