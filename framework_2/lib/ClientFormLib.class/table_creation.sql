CREATE TABLE IF NOT EXISTS cfl_forms
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name TEXT,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS cfl_fields
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        form_id MEDIUMINT,
        flags TINYINT NOT NULL,
        name TEXT,
        type TEXT,
        FOREIGN KEY (form_id) REFERENCES cfl_forms(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
CREATE TABLE IF NOT EXISTS cfl_entries
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        form_id MEDIUMINT,
        name TEXT,
        entry_date datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (form_id) REFERENCES cfl_forms(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS cfl_values
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        entry_id MEDIUMINT,
        field_id MEDIUMINT,
        value TEXT,
        FOREIGN KEY (entry_id) REFERENCES cfl_entries(id),
        FOREIGN KEY (field_id) REFERENCES cfl_fields(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
