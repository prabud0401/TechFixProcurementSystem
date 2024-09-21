# TechFixOrderManagementSystemAPI Development Plan

---

## 1. Create the ASP.NET Core Web API Project

1. Open Visual Studio.
2. Select **Create a new project**.
3. Choose **ASP.NET Core Web API** template.
4. Set the project name to `TechFixOrderManagementSystemAPI`.
5. Choose **.NET Core** and **ASP.NET Core 5.0** (or later).
6. Select a location for the project and click **Create**.

---

## 2. Install Required NuGet Packages

Install the necessary NuGet packages for database interaction, authentication, and API documentation. Follow these steps in the **NuGet Package Manager**:

1. **Install Microsoft.EntityFrameworkCore.SqlServer**:
   - Used for interacting with a SQL Server database.
   - Command:
     ```bash
     Install-Package Microsoft.EntityFrameworkCore.SqlServer
     ```

3. **Install Microsoft.EntityFrameworkCore.Tools**:
   - Enables migration commands to generate and apply database migrations.
   - Command:
     ```bash
     Install-Package Microsoft.EntityFrameworkCore.Tools
     ```


---

## 3. Create the Models Folder and Model Classes

## 4. Create the Controllers Folder and Classes