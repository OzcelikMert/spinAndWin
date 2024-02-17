<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "myLibrary/php/autoLoader.php";

try {
    $page = basename($_SERVER['REQUEST_URI']);
    $page = \myLibrary\php\operations\Variable::clear($page);

    if($page == "" || $page == "spinAndWin"){
        $page = "index";
    }
    
    $page = $page.".php";
    if(file_exists("./pages/$page")){
        include_once "./pages/$page";
    }else {
        header("Location: 404");
    }
} catch (Exception $e) {
    header("Location: 404");
}