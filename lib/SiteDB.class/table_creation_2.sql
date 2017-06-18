CREATE TABLE IF NOT EXISTS video_votes
        (
        account_id MEDIUMINT NOT NULL,
        video_pair_id MEDIUMINT NOT NULL,
        video_voted TINYINT NOT NULL,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (account_id) REFERENCES accountmgr_accounts(id),
        FOREIGN KEY (video_pair_id) REFERENCES video_vote_pairs(id),
        PRIMARY KEY (account_id,video_pair_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;     

CREATE TABLE IF NOT EXISTS categories
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name TEXT,
        base_category MEDIUMINT NOT NULL,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (base_category) REFERENCES base_categories(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS books
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        title       TEXT,
        author      TEXT,
        isbn        TEXT,
        description TEXT,
        price       TEXT,
        currency    TEXT,
        link        TEXT,
        image_id    MEDIUMINT,
        uploader_id MEDIUMINT,
        category    MEDIUMINT,
        remote_ip   BINARY(16),
        featured    TINYINT NOT NULL DEFAULT 0,
        views       INT      NOT NULL DEFAULT 0,
        clicks      INT      NOT NULL DEFAULT 0,
        upvotes     INT      NOT NULL DEFAULT 0,
        rating      INT      NOT NULL DEFAULT 0,
        rates       INT      NOT NULL DEFAULT 0,
        visibility  TEXT,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (image_id)    REFERENCES uploaded_images(id),
        FOREIGN KEY (uploader_id) REFERENCES accountmgr_accounts(id),
        FOREIGN KEY (category)    REFERENCES categories(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS book_views
        (
        remote_ip   BINARY(16)  NOT NULL,
        book_id     MEDIUMINT   NOT NULL,
        forwrd_ip   BINARY(16),
        date_posted datetime    NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (book_id) REFERENCES books(id),
        PRIMARY KEY (remote_ip,book_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
