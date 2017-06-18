CREATE TABLE IF NOT EXISTS blog_posts
        (
        id       MEDIUMINT   NOT NULL AUTO_INCREMENT,
        title    TEXT,
        contents TEXT,
        image_id    MEDIUMINT,

        FOREIGN KEY (image_id)    REFERENCES uploaded_images(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS blog_comments
        (
        id      MEDIUMINT   NOT NULL AUTO_INCREMENT,
        post_id MEDIUMINT   NOT NULL,
        uploader_id MEDIUMINT,
        approval_status TINYINT,

        FOREIGN KEY (post_id) REFERENCES blog_posts(id),
        FOREIGN KEY (uploader_id) REFERENCES accountmgr_accounts(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
