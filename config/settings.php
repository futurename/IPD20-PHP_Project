<?php
declare(strict_types=1);

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel

$log = new Logger('main');
$log->pushHandler(new StreamHandler('logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));


// MeekroDB Setting

if  (strpos($_SERVER['PHP_SELF'],"phpunit")){
    //add test DB here

} else if (strpos($_SERVER['HTTP_HOST'], "ipd20.com") !== false) {
    // hosting on ipd20.com
    DB::$user = 'cp4966_heok';
    DB::$password = 'UzXoLgOfibQ1Nk7n';
    DB::$dbName = 'cp4966_heok';
} else { // local computer
    DB::$user = 'carrental';
    DB::$password = 'sRPJwMOei4Y8lquD';
    DB::$dbName = 'carrental';
    DB::$port = 3333;
}


DB::$error_handler = 'db_error_handler'; // runs on mysql query errors 
DB::$nonsql_error_handler = 'db_error_handler'; // runs on library errors (bad syntax, etc)

function db_error_handler($params)
{
    header("Location: /errors/internal",true, 500);

    global $log;
    $log->error("Database erorr[Connection]: " . $params['error']);

    if ($params['query']) {
        $log->error("Database error[Query]: " . $params['query']);
    }
    die();
}