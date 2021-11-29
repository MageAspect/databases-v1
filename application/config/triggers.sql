CREATE or REPLACE FUNCTION collect_user_carrier_journal_on_update()
    RETURNS trigger AS
$$
DECLARE
    lastDepartmentId                    INTEGER;
    DECLARE lastUserCarrierJournalEntry INTEGER;
BEGIN
    SELECT id
    INTO lastDepartmentId
    FROM departments
    WHERE head_id = NEW.id
    ORDER BY id DESC
    LIMIT 1;

    IF (lastDepartmentId is null) THEN
        SELECT department_id
        INTO lastDepartmentId
        FROM user_department
        WHERE user_id = NEW.id
        ORDER BY department_id DESC
        LIMIT 1;
    END IF;

    IF (NEW.position != OLD.position OR NEW.salary != OLD.salary) THEN
        SELECT id
        INTO lastUserCarrierJournalEntry
        FROM users_career_journal
        WHERE user_id = NEW.id
        ORDER BY id DESC
        LIMIT 1;

        IF (lastUserCarrierJournalEntry > 0) THEN
            UPDATE users_career_journal SET end_datetime = NOW()::timestamp(0) WHERE id = lastUserCarrierJournalEntry;
        END IF;

        INSERT INTO users_career_journal (user_id, department_id, salary, position)
        VALUES (NEW.id, lastDepartmentId, NEW.salary, NEW.position);
    END IF;
    RETURN NEW;
END;
$$
    LANGUAGE 'plpgsql';

CREATE or REPLACE FUNCTION collect_user_carrier_journal_on_insert()
    RETURNS trigger AS
$$
DECLARE
    lastUserCarrierJournalEntry integer;
BEGIN
    SELECT id
    INTO lastUserCarrierJournalEntry
    FROM users_career_journal
    WHERE user_id = NEW.id
    ORDER BY id DESC
    LIMIT 1;

    IF
        (lastUserCarrierJournalEntry > 0)
    THEN
        UPDATE users_career_journal
        SET end_datetime = NOW()::timestamp(0)
        WHERE id = lastUserCarrierJournalEntry;
    END IF;

    INSERT INTO users_career_journal (user_id, department_id, salary, position)
    VALUES (NEW.id, NULL, NEW.salary, NEW.position);
    RETURN NEW;
END;
$$
    LANGUAGE 'plpgsql';

CREATE TRIGGER collect_user_carrier_journal_on_update
    AFTER UPDATE
    ON users
    FOR EACH ROW
EXECUTE PROCEDURE collect_user_carrier_journal_on_update();

CREATE TRIGGER collect_user_carrier_journal_on_insert
    AFTER INSERT
    ON users
    FOR EACH ROW
EXECUTE PROCEDURE collect_user_carrier_journal_on_insert();