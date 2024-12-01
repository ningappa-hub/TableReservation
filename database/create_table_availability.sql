-- Create table to track table availability
CREATE TABLE IF NOT EXISTS `table_availability` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `table_id` INT NOT NULL,
    `booking_date` DATE NOT NULL,
    `booking_time` VARCHAR(30) NOT NULL,
    `status` ENUM('available', 'booked') DEFAULT 'available',
    `booking_id` VARCHAR(200),
    FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables`(`id`)
);

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS `after_booking_insert`;
DROP TRIGGER IF EXISTS `after_booking_reject`;

DELIMITER $$

-- Create trigger to mark table as booked after booking insertion
CREATE TRIGGER `after_booking_insert` 
AFTER INSERT ON `booking_details`
FOR EACH ROW
BEGIN
    -- Insert availability records for the booked time
    INSERT INTO `table_availability` (`table_id`, `booking_date`, `booking_time`, `status`, `booking_id`)
    SELECT DISTINCT rc.`tbl_id`, NEW.`booking_date`, NEW.`booking_time`, 'booked', NEW.`booking_id`
    FROM `booking_chair` bc
    JOIN `restaurant_chair` rc ON bc.`chair_id` = rc.`id`
    WHERE bc.`booking_id` = NEW.`booking_id`;
END$$

-- Create trigger to release table after booking cancellation/rejection
CREATE TRIGGER `after_booking_reject`
AFTER UPDATE ON `booking_details`
FOR EACH ROW
BEGIN
    IF NEW.`status` = 0 THEN -- If booking is rejected
        UPDATE `table_availability` 
        SET `status` = 'available'
        WHERE `booking_id` = OLD.`booking_id`;
    END IF;
END$$

DELIMITER ;