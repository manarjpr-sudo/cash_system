using API.Services;
using Microsoft.AspNetCore.Authorization;
using System.Security.Claims;

namespace API.Authorization;

public class PermissionAuthorizationHandler 
    : AuthorizationHandler<PermissionRequirement>
{
    private readonly PermissionService _permissionService;

    public PermissionAuthorizationHandler(
        PermissionService permissionService)
    {
        _permissionService = permissionService;
    }


    protected override async Task HandleRequirementAsync(
        AuthorizationHandlerContext context,
        PermissionRequirement requirement)
    {
        var userIdClaim = context.User.FindFirst(
            ClaimTypes.NameIdentifier
        );

        if (userIdClaim == null)
            return;


        var userId = int.Parse(userIdClaim.Value);


        var hasPermission = await _permissionService.HasPermission(
            userId,
            requirement.PermissionName
        );


        if (hasPermission)
        {
            context.Succeed(requirement);
        }
    }
}