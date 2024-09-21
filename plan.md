# .NET C# API with SQL Server

This project is a backend API built using .NET Core and SQL Server, following a structured approach with models, controllers, services, and database connections through `EntityFrameworkCore`.

## Steps to Build the API

1. Set up SQL Server Database:
   - Create the database.
   - Create tables with appropriate fields using SQL scripts.

2. Create a New ASP.NET Core Web API Project:
   - Open Visual Studio.
   - Create a new **ASP.NET Core Web Application** project.
   - Choose the **API** template.

3. Install Required NuGet Packages:
   - Install Microsoft.EntityFrameworkCore.SqlServer.
   - Install Microsoft.EntityFrameworkCore.Tools.

4. Create Models:
   - In the `Models` folder, create classes that map to your database tables.

5. Create ApplicationDbContext:
   - In the `Data` folder, create the `ApplicationDbContext` class.
   - This class should inherit from `DbContext` and include `DbSet` properties for your models.

6. Configure the Database Connection:
   - In `appsettings.json`, configure the connection string for SQL Server.
   - Update the `Startup.cs` or `Program.cs` to add the `DbContext` service in `ConfigureServices`.

7. Create Repositories or Services for CRUD Operations:
   - In the `Services` folder, create service classes to handle database interactions using `ApplicationDbContext`.

8. Create API Controllers:
   - Create a controller for each model in the `Controllers` folder.
   - Use dependency injection to access the services for CRUD operations.

9. Apply Migrations and Update the Database:
   - Use Entity Framework commands to add migrations and update the database:
     - Add-Migration InitialCreate
     - Update-Database

10. Test the API:
    - Use Postman or Swagger to test the CRUD endpoints for each controller.

## License
This project is licensed under the MIT License.
