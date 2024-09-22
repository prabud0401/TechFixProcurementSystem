-- Disable foreign key constraints to allow dropping tables with dependencies
EXEC sp_MSforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL'

-- Drop all tables
EXEC sp_MSforeachtable 'DROP TABLE ?'

-- Enable foreign key constraints again
EXEC sp_MSforeachtable 'ALTER TABLE ? WITH CHECK CHECK CONSTRAINT ALL'
