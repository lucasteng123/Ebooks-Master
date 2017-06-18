CREATE TABLE IF NOT EXISTS blogmgr_authors
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name VARCHAR(30),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS blogmgr_feeds
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name VARCHAR(40),
        identifier VARCHAR(20),
        date_created datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS blogmgr_posts
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        title VARCHAR(40),
        contents TEXT,
        author MEDIUMINT,
        feed MEDIUMINT,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        date_edited datetime default '0000-00-00 00:00:00',
        FOREIGN KEY (author) REFERENCES blogmgr_authors(id),
        FOREIGN KEY (feed) REFERENCES blogmgr_feeds(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
