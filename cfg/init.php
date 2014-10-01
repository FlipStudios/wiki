<?php

    Init::run();

    /**
     * This class handles all of our settings/preferences
     */
    class Init
    {

        const CONFIG_DIR = __DIR__;

        static public function run()
        {
            self::_calculateAndSetRootRepo();
            self::_loadConfigFiles();
            self::_includeFiles();
            self::_registerAutoloader();
            self::_initSession();
        }

        static protected function _calculateAndSetRootRepo()
        {
            define('ROOT_REPO', dirname(__DIR__));
        }


        static protected function _loadConfigFiles()
        {
            require_once dirname(dirname(__DIR__)) . '/private/wiki.config.php';
        }

        static protected function _includeFiles()
        {
            // include any files we want globally included
            require_once __DIR__ . '/functions.global.php';
        }

        static protected function _registerAutoloader()
        {
            spl_autoload_register(__CLASS__ . '::autoloader');
        }

        static protected function _initSession()
        {
            session_start();

            $signInUrl = '/util/sign-in';

            if(empty($_SESSION['user_id']) && strpos($_SERVER['REQUEST_URI'], $signInUrl) !== 0)
            {
                redirect($signInUrl);
            }
        }

        static public function autoloader($class)
        {
            $x = explode('\\', $class);

            if(count($x) == 2 || count($x) == 3)
            {
                $filename = dirname(__DIR__) . '/lib/' . implode('/', $x) . '.php';

                if(file_exists($filename))
                {
                    require_once $filename;
                }
            }

            // Redirect: namespace\namespace\classname -> /lib/namespace/namespace/classname
            // Redirect: namespace\classname -> /class/namespace/classname
            // e.g. Wiki\DB\DB --> /lib/Wiki/DB/DB.php

            $x = explode('\\', $class);

            switch(count($x))
            {
                case 3:
                    $filename = dirname(__DIR__) . '/lib/' . implode('/', $x) . '.php';
                    break;

                case 2:
                    $filename = dirname(__DIR__) . '/classes/' . implode('/', $x) . '.php';
                    break;

                default:
                    $filename = NULL;
                    break;
            }

            if(file_exists($filename))
            {
                require_once $filename;
            }
            else
            {
                echo 'Class "' . $filename . '" does not exist. (' . $class . ')' . "\n";
            }
        }
    }
