CREATE TABLE IF NOT EXISTS uploaded_images
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        filename TEXT,
        date_uploaded datetime NOT NULL default '0000-00-00 00:00:00',
        friendly_name TEXT,
        image_feed TEXT,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
