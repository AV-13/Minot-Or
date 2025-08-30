DELIMITER //

CREATE TRIGGER update_truck_availability
AFTER INSERT ON truck_cleaning
FOR EACH ROW
BEGIN
    IF NEW.cleaning_end_date > NOW() THEN
        UPDATE truck SET is_available = 0 WHERE id = NEW.truck_id;
    ELSE
        UPDATE truck SET is_available = 1 WHERE id = NEW.truck_id;
    END IF;
END;
//

DELIMITER ;