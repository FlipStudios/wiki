<?php

    /**
     * Redirect the page to a new page. You can use "BACK" as an option for redirecting to the previous url.
     *
     * @param $url
     */
    function redirect($url)
    {
        if(strtoupper($url) == 'BACK')
        {
            //allow shortcut for "back" to take user to last page
            $url = $_SERVER['HTTP_REFERER'];
        }

        header('location:' . $url);
        exit;
    }