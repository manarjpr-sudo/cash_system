using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace Infrastructure.Migrations
{
    /// <inheritdoc />
    public partial class AddEnumsToFinancialEntities : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Approval_Orders_OrderId",
                table: "Approval");

            migrationBuilder.DropForeignKey(
                name: "FK_Approval_Users_UserId",
                table: "Approval");

            migrationBuilder.DropPrimaryKey(
                name: "PK_Approval",
                table: "Approval");

            migrationBuilder.RenameTable(
                name: "Approval",
                newName: "Approvals");

            migrationBuilder.RenameIndex(
                name: "IX_Approval_UserId",
                table: "Approvals",
                newName: "IX_Approvals_UserId");

            migrationBuilder.RenameIndex(
                name: "IX_Approval_OrderId",
                table: "Approvals",
                newName: "IX_Approvals_OrderId");

            migrationBuilder.AddPrimaryKey(
                name: "PK_Approvals",
                table: "Approvals",
                column: "Id");

            migrationBuilder.AddForeignKey(
                name: "FK_Approvals_Orders_OrderId",
                table: "Approvals",
                column: "OrderId",
                principalTable: "Orders",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Approvals_Users_UserId",
                table: "Approvals",
                column: "UserId",
                principalTable: "Users",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropForeignKey(
                name: "FK_Approvals_Orders_OrderId",
                table: "Approvals");

            migrationBuilder.DropForeignKey(
                name: "FK_Approvals_Users_UserId",
                table: "Approvals");

            migrationBuilder.DropPrimaryKey(
                name: "PK_Approvals",
                table: "Approvals");

            migrationBuilder.RenameTable(
                name: "Approvals",
                newName: "Approval");

            migrationBuilder.RenameIndex(
                name: "IX_Approvals_UserId",
                table: "Approval",
                newName: "IX_Approval_UserId");

            migrationBuilder.RenameIndex(
                name: "IX_Approvals_OrderId",
                table: "Approval",
                newName: "IX_Approval_OrderId");

            migrationBuilder.AddPrimaryKey(
                name: "PK_Approval",
                table: "Approval",
                column: "Id");

            migrationBuilder.AddForeignKey(
                name: "FK_Approval_Orders_OrderId",
                table: "Approval",
                column: "OrderId",
                principalTable: "Orders",
                principalColumn: "Id",
                onDelete: ReferentialAction.Restrict);

            migrationBuilder.AddForeignKey(
                name: "FK_Approval_Users_UserId",
                table: "Approval",
                column: "UserId",
                principalTable: "Users",
                principalColumn: "Id",
                onDelete: ReferentialAction.Cascade);
        }
    }
}
