
CREATE TABLE IF NOT EXISTS newsletter_subscriptions
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        email TEXT,
        date_posted datetime    NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
