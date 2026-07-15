using API.Services;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace API.Controllers;

[ApiController]
[Route("api/test")]
[Authorize]
public class TestPermissionController : ControllerBase
{
    private readonly PermissionService _permissionService;

    public TestPermissionController(PermissionService permissionService)
    {
        _permissionService = permissionService;
    }


    [HttpGet]
    public async Task<IActionResult> Test()
    {
        var result = await _permissionService.HasPermission(
            1,
            "Create_User"
        );

        return Ok(result);
    }
}