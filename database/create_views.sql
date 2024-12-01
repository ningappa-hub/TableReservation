
-- Create RestaurantBookingSummary view
CREATE OR REPLACE VIEW RestaurantBookingSummary AS
SELECT 
    r.id AS RestaurantID,
    COUNT(b.id) AS TotalBookings,
    IFNULL(SUM(b.bill), 0) AS TotalRevenue
FROM restaurant_info r
LEFT JOIN booking_details b ON r.id = b.res_id
GROUP BY r.id;

// ...existing code...

-- Create AddBooking stored procedure
DELIMITER $$

CREATE PROCEDURE AddBooking(
    IN p_booking_id VARCHAR(200),
    IN p_res_id INT,
    IN p_c_id INT,
    IN p_name VARCHAR(50),
    IN p_phone VARCHAR(100),
    IN p_booking_date DATE,
    IN p_booking_time VARCHAR(30),
    IN p_bill FLOAT,
    IN p_transactionid VARCHAR(100),
    IN p_chairs JSON,
    IN p_menus JSON
)
BEGIN
    -- Insert into booking_details
    INSERT INTO booking_details (
        booking_id, res_id, c_id, name, phone, booking_date, booking_time, bill, transactionid, status
    ) VALUES (
        p_booking_id, p_res_id, p_c_id, p_name, p_phone, p_booking_date, p_booking_time, p_bill, p_transactionid, 1
    );

    -- Insert into booking_chair
    INSERT INTO booking_chair (booking_id, chair_id, chair_no)
    SELECT 
        p_booking_id, 
        JSON_UNQUOTE(JSON_EXTRACT(chair, '$.chair_id')), 
        JSON_UNQUOTE(JSON_EXTRACT(chair, '$.chair_no'))
    FROM JSON_TABLE(p_chairs, '$[*]' COLUMNS (chair JSON PATH '$')) AS chairs;

    -- Insert into booking_menus
    INSERT INTO booking_menus (booking_id, item_id, qty)
    SELECT 
        p_booking_id, 
        JSON_UNQUOTE(JSON_EXTRACT(menu, '$.item_id')), 
        JSON_UNQUOTE(JSON_EXTRACT(menu, '$.qty'))
    FROM JSON_TABLE(p_menus, '$[*]' COLUMNS (menu JSON PATH '$')) AS menus;
END$$

DELIMITER ;



-- Create CalculateBookingRevenue function
DELIMITER $$

CREATE FUNCTION CalculateBookingRevenue(p_res_id INT)
RETURNS FLOAT
DETERMINISTIC
BEGIN
    DECLARE total_revenue FLOAT;
    SELECT SUM(bill) INTO total_revenue
    FROM booking_details
    WHERE res_id = p_res_id;
    RETURN total_revenue;
END$$

DELIMITER ;

