using API.Services;
using Infrastructure.Data;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;
using System.Text;
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
// CORS
// ===============================

builder.Services.AddCors(options =>
{
    options.AddPolicy("ReactPolicy", policy =>
    {
        policy
            .WithOrigins("http://localhost:5173")
            .AllowAnyHeader()
            .AllowAnyMethod();
    });
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


        IssuerSigningKey =
            new SymmetricSecurityKey(key)
    };
});



// ===============================
// Authorization
// ===============================

builder.Services.AddAuthorization();



// ===============================
// Build
// ===============================

var app = builder.Build();



// ===============================
// Swagger
// ===============================

if (app.Environment.IsDevelopment())
{
    app.UseSwagger();

    app.UseSwaggerUI();
}



// ===============================
// HTTP Pipeline
// ===============================

app.UseCors("ReactPolicy");


app.UseHttpsRedirection();


app.UseAuthentication();


app.UseAuthorization();


app.MapControllers();


app.Run();