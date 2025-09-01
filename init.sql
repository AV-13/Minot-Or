DELIMITER //

CREATE TRIGGER update_truck_availability
AFTER INSERT ON truck_cleaning
FOR EACH ROW
BEGIN
    DECLARE last_cleaning_date DATE;
    DECLARE delivery_count INT;

    -- Récupère la dernière date de nettoyage du camion
    SELECT MAX(cleaning_end_date) INTO last_cleaning_date
    FROM truck_cleaning
    WHERE truck_id = NEW.truck_id;

    -- Récupère le nombre de livraisons du camion
    SELECT delivery_count INTO delivery_count
    FROM truck
    WHERE id = NEW.truck_id;

    IF last_cleaning_date IS NOT NULL
       AND last_cleaning_date < DATE_SUB(NOW(), INTERVAL 3 WEEK)
       AND delivery_count >= 10 THEN
        UPDATE truck SET is_available = 0 WHERE id = NEW.truck_id;
    ELSE
        UPDATE truck SET is_available = 1 WHERE id = NEW.truck_id;
    END IF;
END;
//

DELIMITER ;