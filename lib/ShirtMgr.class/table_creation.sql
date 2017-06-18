CREATE TABLE IF NOT EXISTS tshirts
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
		image TEXT,
        name TEXT,
		colors TEXT,
		active TINYINT DEFAULT 1,
		price FLOAT DEFAULT 15.99,
		size TEXT,
		description TEXT,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS orders
        (
        order_id MEDIUMINT NOT NULL AUTO_INCREMENT,
		pretty_id char(10),
        tshirt_id MEDIUMINT,
        quantity MEDIUMINT,
        state TINYINT,
		tracking TEXT,
		chosen_size TEXT,
		FOREIGN KEY (tshirt_id) REFERENCES tshirts(id),
        PRIMARY KEY (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
		
CREATE TABLE IF NOT EXISTS pay_list
        (
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        copy TEXT,
		price FLOAT,
		active TINYINT,
        PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
