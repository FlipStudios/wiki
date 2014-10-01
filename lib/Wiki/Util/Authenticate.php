<?php

    namespace Wiki\Util;

    /*

    CREATE TABLE `User` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `username` varchar(32) NOT NULL DEFAULT '',
      `password` varchar(255) NOT NULL DEFAULT '',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    */

    class Authenticate
    {

        /**
         * Fetch a row from the `users` table based on the provided username
         *
         * @param $username
         *
         * @return array|bool
         */
        static public function getUserRowByUsername($username)
        {
            $username = trim($username);

            if(empty($username))
            {
                return false;
            }

            $db = new \Wiki\DB\DB2;

            $sql = "	SELECT  *
                        FROM    `User`
                        WHERE   username = :username
                        AND     active= 1 ";

            $params             = array();
            $params['username'] = $username;

            $row = $db->fetchRow($sql, $params);

            if(empty($row['user_id']))
            {
                $res = false;
            }
            else
            {
                $res = $row;
            }

            return $res;
        }

        /**
         * @param      $hashedPassword
         * @param      $password
         * @param null $user_id
         *
         * @return bool
         */
        static public function authenticatePassword($hashedPassword, $password, $user_id = NULL)
        {
            if($hashedPassword == $password)
            {
                // old UNHASHED password, update the database with a hashed password, and then reauthenticate
                $newHashedPassword = self::setPasswordForUser($user_id, $password);

                return self::authenticatePassword($newHashedPassword, $password);
            }

            return password_verify($password, $hashedPassword);
        }

        static public function setPasswordForUser($user_id, $rawPassword)
        {
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);

            $db = new \Wiki\DB\DB2;

            $sql = 'UPDATE  `User`
                    SET     `password` = :password
                    WHERE   `id` = :user_id ; ';

            $params             = array();
            $params['user_id']  = $user_id;
            $params['password'] = $hashedPassword;

            $db->query($sql, $params);

            return $hashedPassword;
        }
    }