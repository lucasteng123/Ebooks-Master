CREATE TABLE IF NOT EXISTS blog_posts
        (
        id              MEDIUMINT   NOT NULL AUTO_INCREMENT,
        title           TEXT,
        contents        TEXT,
        image_id        MEDIUMINT,

        date_posted     datetime    NOT NULL default '0000-00-00 00:00:00',

        FOREIGN KEY (image_id)    REFERENCES uploaded_images(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS blog_comments
        (
        id              MEDIUMINT   NOT NULL AUTO_INCREMENT,
        post_id         MEDIUMINT   NOT NULL,
        uploader_id     MEDIUMINT,
        approval_status TINYINT     NOT NULL DEFAULT 0,

        contents        TEXT,
        name            TEXT,

        date_posted     datetime    NOT NULL default '0000-00-00 00:00:00',

        FOREIGN KEY (post_id)     REFERENCES blog_posts(id),
        FOREIGN KEY (uploader_id) REFERENCES accountmgr_accounts(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
