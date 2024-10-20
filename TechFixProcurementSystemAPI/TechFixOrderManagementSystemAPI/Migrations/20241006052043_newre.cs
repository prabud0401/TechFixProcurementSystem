using System;
using Microsoft.EntityFrameworkCore.Migrations;

#nullable disable

namespace TechFixOrderManagementSystemAPI.Migrations
{
    /// <inheritdoc />
    public partial class newre : Migration
    {
        /// <inheritdoc />
        protected override void Up(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.CreateTable(
                name: "QuotationSummarys",
                columns: table => new
                {
                    QuotationSummaryID = table.Column<int>(type: "int", nullable: false)
                        .Annotation("SqlServer:Identity", "1, 1"),
                    QuotationCode = table.Column<string>(type: "nvarchar(255)", maxLength: 255, nullable: false),
                    CustomerID = table.Column<int>(type: "int", nullable: false),
                    SupplierID = table.Column<int>(type: "int", nullable: false),
                    TotalPrice = table.Column<decimal>(type: "decimal(18,2)", nullable: false),
                    QuotationPrice = table.Column<decimal>(type: "decimal(18,2)", nullable: false),
                    QuotationNote = table.Column<string>(type: "nvarchar(500)", maxLength: 500, nullable: false),
                    RequestDate = table.Column<DateTime>(type: "datetime2", nullable: false),
                    Status = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: false),
                    PayStatus = table.Column<string>(type: "nvarchar(50)", maxLength: 50, nullable: false),
                    PayID = table.Column<string>(type: "nvarchar(255)", maxLength: 255, nullable: false)
                },
                constraints: table =>
                {
                    table.PrimaryKey("PK_QuotationSummarys", x => x.QuotationSummaryID);
                });
        }

        /// <inheritdoc />
        protected override void Down(MigrationBuilder migrationBuilder)
        {
            migrationBuilder.DropTable(
                name: "QuotationSummarys");
        }
    }
}
