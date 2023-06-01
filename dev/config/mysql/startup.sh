#!/bin/bash

# Змінюємо пароль на стандартний 1234 менеджеру admin
# Якщо менеджера ще нема, він буде створений
mysql --user="$MYSQL_USER" --password="$MYSQL_PASSWORD" --database="$MYSQL_DATABASE" --execute="
DELIMITER \$$
CREATE PROCEDURE managerDefaultPass()
BEGIN
DECLARE managerId INT(11) DEFAULT 0;
SELECT id INTO managerId FROM ok_managers WHERE login = 'admin';
IF managerId > 0 THEN
    UPDATE ok_managers SET password = '\$apr1\$8m1u0cp4\$MYUZf5fVcidsoTaFb0P9P1' WHERE id = managerId;
ELSE
    INSERT INTO ok_managers
    SET
        login = 'admin',
        password = '\$apr1\$8m1u0cp4\$MYUZf5fVcidsoTaFb0P9P1',
        email = 'support@demo.com',
        lang = 'ua',
        menu_status = 1;
END IF;
END\$$
DELIMITER ;
CALL managerDefaultPass();
DROP PROCEDURE managerDefaultPass;
"