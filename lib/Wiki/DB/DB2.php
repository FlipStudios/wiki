<?php

    namespace Wiki\DB;

    require_once __DIR__ . '/Elegance.php';

    class DB2
    {
        /**
         * @var \Elegance_Db    Instance of the Elegance DB class
         */
        protected $edb;

        /**
         * @var \PDO    Instance of the PDO class
         */
        protected $driver;

        protected $stmt;

        public function __construct()
        {
            $num_args = func_num_args();
            $argv     = func_get_args();

            if($num_args == 0)
            {
                $this->dbhost = DB_PROFILE_HOST_DEFAULT;
                $this->dbuser = DB_PROFILE_USER_DEFAULT;
                $this->dbpass = DB_PROFILE_PASSWORD_DEFAULT;
                $this->dbname = DB_PROFILE_DATABASE_DEFAULT;
            }
            else if($num_args == 1)
            {
                $name         = strtoupper($argv[0]);
                $this->dbhost = constant('DB_PROFILE_HOST_' . $name . '');
                $this->dbuser = constant('DB_PROFILE_USER_' . $name . '');
                $this->dbpass = constant('DB_PROFILE_PASSWORD_' . $name . '');
                $this->dbname = constant('DB_PROFILE_DATABASE_' . $name . '');
            }
            else if($num_args == 4)
            {
                //override default db connection values
                $this->dbhost = $argv[0];
                $this->dbuser = $argv[1];
                $this->dbpass = $argv[2];
                $this->dbname = $argv[3];
            }

            $this->_connect();
        }

        public function fetchAll($sql, $params = array(), $pdoFetchStyle = \PDO::FETCH_ASSOC)
        {
            return $this->_fetch($sql, $params, $pdoFetchStyle, $returnOneRow = false);
        }

        public function fetchRow($sql, $params = array(), $pdoFetchStyle = \PDO::FETCH_ASSOC)
        {
            return $this->_fetch($sql, $params, $pdoFetchStyle, $returnOneRow = true);
        }

        public function query($sql, $params = array())
        {
            return $this->_fetch($sql, $params);
        }

        protected function _fetch($sql, $params = array(), $pdoFetchStyle = \PDO::FETCH_ASSOC, $returnOneRow = false)
        {
            if(empty($params)) // Plain SQL
            {
                $this->stmt = $this->driver->query($sql);

                if($this->stmt === false)
                {
                    die('Query failed: ' . $sql);
                    return false;
                }

                $this->stmt->execute();
            }
            else // Prepared Statement
            {
                $this->stmt = $this->driver->prepare($sql);

                foreach($params AS $paramName => $paramValue)
                {
                    //only bind the param if it is in our sql statement (aka. ignore extraneous params)
                    if(strpos($sql, ':' . $paramName) === false)
                    {
                        unset($params[$paramName]);
                    }
                }

                $this->stmt->execute($params);
            }

            if($returnOneRow)
            {
                return $this->stmt->fetch($pdoFetchStyle);
            }
            else
            {
                return $this->stmt->fetchAll($pdoFetchStyle);
            }
        }

        public function rowsAffected()
        {
            if($this->stmt)
            {
                return $this->stmt->rowCount();
            }

            return false;
        }

        public function update($table, $data, $where, $limit = 0)
        {
            return $this->edb->update($table, $data, $where, $limit);
        }

        public function insert($table, $data, $isIgnore = false)
        {
            return $this->edb->insert($table, $data, $isIgnore);
        }

        protected function _connect()
        {
            $this->driver = new \PDO("mysql:host=" . $this->dbhost . ";dbname=" . $this->dbname . "", $this->dbuser, $this->dbpass);
            $this->edb    = new \Elegance_Db($this->driver);
        }
    }