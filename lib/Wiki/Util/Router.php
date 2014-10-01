<?php

    namespace Wiki\Util;

    /**
     * A class for handling the .htaccess rules to rewrite urls via apache mod_rewrite
     */
    class Router
    {

        /**
         * Create an instance of our class
         */
        function __construct()
        {
            $SLUG = self::getArrayOfPathFolders($_SERVER['REQUEST_URI']);

            if(!isset($SLUG) || $SLUG[0] == '')
            {
                $file_to_include = ROOT_REPO . '/app/index.php';
            }
            else if($SLUG[0] == 'ajax')
            {
                $file_to_include = ROOT_REPO . '/app/ajax/ajax.' . $SLUG[1];
            }
            else
            {
                $path            = implode('/', $SLUG);
                $file_to_include = ROOT_REPO . '/app/' . $path . '.php';
            }

//            echo $file_to_include;

            if(file_exists($file_to_include))
            {
                require_once($file_to_include);
                exit;
            }
            else
            {
                die('File not found in ' . basename(__FILE__));
            }
        }

        /**
         * Parses the requested path into a tokenized array
         *
         * @param $url  String  Something like: /library/inbox/?test=123
         *
         * @return array
         */
        static public function getArrayOfPathFolders($url)
        {
            //BREAK APART: Array ( [path] => /library/inbox/ [query] => test=123 )
            $SLUG = parse_url($url);

            //SEPARATE PATH: Array ( [0] => [1] => library [2] => inbox [3] => )
            $SLUG = explode('/', $SLUG['path']);

            //REMOVE BLANKS: Array ( [0] => library [1] => inbox )
            $tmp_array = array();
            foreach($SLUG as $key => $value)
            {
                $value = trim($value);
                if(strlen($value) > 0)
                {
                    $tmp_array[] = $value;
                }
            }
            $SLUG = $tmp_array;

            return $SLUG;
        }

        protected function _get_SLUG_command_line()
        {
            global $argv;

            $res = $argv;

            array_shift($res);

            return $res;
        }
    }