-- Add status column to enrollments table if it doesn't exist
ALTER TABLE `enrollments` 
ADD COLUMN `status` ENUM('Pending', 'Active', 'Inactive') NOT NULL DEFAULT 'Active' 
AFTER `enrollment_date`;

-- Update existing enrollments to have 'Active' status
UPDATE `enrollments` SET `status` = 'Active' WHERE `status` IS NULL;

-- Create uploads directory structure (to be created manually)
-- uploads/payment_slips/