CREATE TABLE IF NOT EXISTS gc_contests
        (
        id INT NOT NULL AUTO_INCREMENT,
        phrase VARCHAR(255),
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        date_closed datetime,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
CREATE TABLE IF NOT EXISTS gc_contestants
        (
        id INT NOT NULL AUTO_INCREMENT,
        name  VARCHAR(40),
        email  VARCHAR(255),
        guess VARCHAR(255),
        contest INT,
        date_entered datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (contest) REFERENCES gc_contests(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
CREATE TABLE IF NOT EXISTS gc_names_roll
        (
        id INT NOT NULL AUTO_INCREMENT,
        name  VARCHAR(40),
        contestant INT,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (contestant) REFERENCES gc_contestants(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
