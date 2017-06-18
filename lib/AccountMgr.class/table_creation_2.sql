
CREATE TABLE accountmgr_permissions
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        permission VARCHAR(40),
        account MEDIUMINT,
        FOREIGN KEY (account) REFERENCES accountmgr_accounts(id),
        CONSTRAINT uc_accperm UNIQUE (account,permission),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
-- the creation of this table had to be put in a separate file
-- for currently unknown reasons. :/