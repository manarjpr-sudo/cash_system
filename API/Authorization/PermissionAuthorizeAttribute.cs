using Microsoft.AspNetCore.Authorization;

namespace API.Authorization;

public class PermissionAuthorizeAttribute : AuthorizeAttribute
{
    public PermissionAuthorizeAttribute(string permission)
    {
        Policy = permission;
    }
}