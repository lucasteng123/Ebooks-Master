DROP TABLE IF EXISTS accountmgr_permissions;
DROP TABLE IF EXISTS accountmgr_accounts;

CREATE TABLE accountmgr_accounts
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name VARCHAR(40),
        username VARCHAR(40),
        p_hash TEXT,
        p_salt TEXT,
        reset_email TEXT,
        attempts TINYINT NOT NULL DEFAULT 0,
        pwd_reset VARCHAR(12) NOT NULL default 'OK',
        /*
                Password reset values:
                OK -> user may login as normal
                EX -> password expired; prompt for reset
                anything else -> user has requested password reset - this is the reset code
        */
        activation CHAR(8) NOT NULL default 'OK',
        /*
                Activation values:
                OK -> account is activated
                DE -> account was deactivated
                anything else -> Account has yet to be activated
        */
        last_attempt datetime NOT NULL default '0000-00-00 00:00:00',
        date_created datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
