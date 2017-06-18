ALTER TABLE books ADD video_url VARCHAR(256);
ALTER TABLE books ADD video_url_off TINYINT NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS books_categories
        (
        book_id     MEDIUMINT   NOT NULL,
        cat_id      MEDIUMINT   NOT NULL,
        FOREIGN KEY (book_id) REFERENCES books(id),
        FOREIGN KEY (cat_id)  REFERENCES categories(id),
        PRIMARY KEY (book_id,cat_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

ALTER TABLE vote_videos ADD video_image MEDIUMINT;
ALTER TABLE vote_videos ADD CONSTRAINT fk_video_image FOREIGN KEY(video_image) REFERENCES uploaded_images(id);
