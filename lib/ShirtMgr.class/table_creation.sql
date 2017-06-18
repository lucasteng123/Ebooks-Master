CREATE TABLE IF NOT EXISTS tshirts
        (
        id              MEDIUMINT   NOT NULL AUTO_INCREMENT,
        title           TEXT,
        contents        TEXT,
        image_id        MEDIUMINT,

        date_posted     datetime    NOT NULL default '0000-00-00 00:00:00',

        FOREIGN KEY (image_id)    REFERENCES uploaded_images(id),
        PRIMARY KEY (id)
        )
