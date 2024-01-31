import MySQLdb

conn = MySQLdb.connect(host = 'localhost', user = 'root', db = 'order_platform', charset = 'utf8')

cursor = conn.cursor()

SQL = "CREATE TABLE IF NOT EXISTS User(\
        UID int(5) AUTO_INCREMENT, \
        Account varchar(256) NOT NULL UNIQUE, \
        Password varchar(256) NOT NULL, \
        First_name varchar(256) NOT NULL, \
        Last_name varchar(256) NOT NULL, \
        Identity varchar(10) DEFAULT 'User', \
        Location geometry NOT NULL, \
        Phone_number char(10) NOT NULL, \
        Balance int(10) DEFAULT '0', \
        PRIMARY KEY (UID), \
        SPATIAL KEY `idx_location` (`Location`))"
cursor.execute(SQL)
conn.commit()

SQL = "CREATE TABLE IF NOT EXISTS Shop(\
        SID int(5) AUTO_INCREMENT, \
        Name varchar(256) NOT NULL UNIQUE, \
        Location geometry NOT NULL, \
        Phone_number char(10), \
        Type varchar(256) NOT NULL, \
        UID int(5), \
        PRIMARY KEY (SID), \
        FOREIGN KEY (UID) REFERENCES User (UID))"

cursor.execute(SQL)
conn.commit()

SQL = "CREATE TABLE IF NOT EXISTS Meal(\
        PID int(5) AUTO_INCREMENT, \
        Name varchar(256), \
        Price int(10), \
        Amount int(10), \
        Image MEDIUMBLOB, \
        SID int(5), \
        Is_Deleted boolean DEFAULT false, \
        PRIMARY KEY (PID), \
        FOREIGN KEY (SID) REFERENCES Shop(SID))"

cursor.execute(SQL)
conn.commit()

sql = "CREATE TABLE IF NOT EXISTS `Order`(\
        OID int(5) AUTO_INCREMENT, \
        Status varchar(256) DEFAULT 'Not Finished', \
        Create_time datetime DEFAULT CURRENT_TIMESTAMP, \
        Finish_time datetime, \
        Distance double, \
        Type varchar(256), \
        Subtotal int(10), \
        Delivery_fee int(10), \
        SID int(5), \
        UID int(5), \
        PRIMARY KEY (OID), \
        FOREIGN KEY (SID) REFERENCES shop(SID), \
        FOREIGN KEY (UID) REFERENCES user(UID))"

cursor.execute(sql)
conn.commit()

sql = "CREATE TABLE IF NOT EXISTS `Order_item`(\
        OID int(5), \
        PID int(5), \
        Quantity int(5), \
        Price int(10), \
        PRIMARY KEY (OID, PID), \
        FOREIGN KEY (OID) REFERENCES `Order`(OID), \
        FOREIGN KEY (PID) REFERENCES `Meal`(PID))"

cursor.execute(sql)
conn.commit()

sql = "CREATE TABLE IF NOT EXISTS `Transaction`(\
        TID int(5) AUTO_INCREMENT, \
        UID int(5), \
        Amount int(10), \
        Time datetime DEFAULT CURRENT_TIMESTAMP, \
        Behavior varchar(256), \
        Trader varchar(256), \
        PRIMARY KEY (TID), \
        FOREIGN KEY (UID) REFERENCES `User`(UID))"

cursor.execute(sql)
conn.commit()

cursor.close()
conn.close()