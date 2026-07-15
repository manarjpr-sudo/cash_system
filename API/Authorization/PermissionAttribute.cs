namespace API.Authorization;

[AttributeUsage(AttributeTargets.Method | AttributeTargets.Class)]
public class PermissionAttribute : Attribute
{
    public string PermissionName { get; }

    public PermissionAttribute(string permissionName)
    {
        PermissionName = permissionName;
    }
}