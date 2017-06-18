CREATE TABLE IF NOT EXISTS site_strings
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name VARCHAR(128) NOT NULL UNIQUE,
        value TEXT,
        entry_date datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
