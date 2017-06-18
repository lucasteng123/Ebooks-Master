CREATE TABLE IF NOT EXISTS ticker_messages
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        msg TEXT,
        linkurl TEXT,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;


CREATE TABLE IF NOT EXISTS vote_videos
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        video_title TEXT,
        youtube_id  TEXT,
        votecount   INT NOT NULL DEFAULT 0,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS video_vote_pairs
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        title  TEXT,
        vid_a MEDIUMINT,
        vid_b MEDIUMINT,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (vid_a) REFERENCES vote_videos(id),
        FOREIGN KEY (vid_b) REFERENCES vote_videos(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS base_categories
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        name  TEXT,
        video_pair_id MEDIUMINT,
        date_posted datetime NOT NULL default '0000-00-00 00:00:00',
        FOREIGN KEY (video_pair_id) REFERENCES video_vote_pairs(id),
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
