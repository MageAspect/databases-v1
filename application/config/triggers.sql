DELIMITER $$
CREATE TRIGGER collect_user_carrier_journal_on_update AFTER UPDATE ON users
    FOR EACH ROW
BEGIN
    DECLARE lastDepartmentId INTEGER;
    DECLARE lastUserCarrierJournalEntry INTEGER;
    SELECT department_id INTO lastDepartmentId
    FROM user_department
    WHERE user_id = NEW.id
    ORDER BY department_id DESC
    LIMIT 1;

    IF (NEW.position != OLD.position OR NEW.salary != OLD.salary) THEN
        SELECT id INTO lastUserCarrierJournalEntry
        FROM users_career_journal
        WHERE user_id = NEW.id
        ORDER BY id DESC
        LIMIT 1;

        IF (lastUserCarrierJournalEntry > 0) THEN
            UPDATE users_career_journal SET end_datetime = NOW() WHERE id = lastUserCarrierJournalEntry;
        END IF;

        INSERT INTO users_career_journal (user_id, department_id, salary, position)
        VALUES (NEW.id, lastDepartmentId , NEW.salary, NEW.position);
    END IF;

END $$;
DELIMITER ;

DELIMITER $$
CREATE TRIGGER collect_user_carrier_journal_on_insert AFTER INSERT ON users
    FOR EACH ROW
BEGIN
    DECLARE lastUserCarrierJournalEntry integer;
        SELECT id INTO lastUserCarrierJournalEntry
        FROM users_career_journal
        WHERE user_id = NEW.id
        ORDER BY id DESC
        LIMIT 1;

        IF (lastUserCarrierJournalEntry > 0) THEN
            UPDATE users_career_journal SET end_datetime = NOW() WHERE id = lastUserCarrierJournalEntry;
        END IF;

        INSERT INTO users_career_journal (user_id, department_id, salary, position)
        VALUES (NEW.id, NULL , NEW.salary, NEW.position);

END $$;
DELIMITER ;