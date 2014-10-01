<?php

//    echo basename(__FILE__);
//
//    $tmp = parse_url($_SERVER['REQUEST_URI']);
//
//    echo '<pre>' . print_r($tmp, true) . '</pre>';

    // Fake a passed authentication
    if(empty($_SESSION))
    {
        $_SESSION['user_id'] = 999;
        $_SESSION['username'] = 'fake_username';

        echo 'You have been logged in (fake)';

//        $Authenticate = new Wiki\Util\Authenticate;
//        $Authenticate->...
    }
