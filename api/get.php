<?php

namespace api\queries;

require "../myLibrary/php/autoLoader.php";

use myLibrary\php\api\ErrorCodes;
use myLibrary\php\api\Result;
use myLibrary\php\operations\User;
use myLibrary\php\operations\Variable;
use services\ItemService;

$echo = new Result();

if ($_GET) {
    Variable::clearAllData($_GET);

    $echo->rows = ItemService::get();
    $echo->status = true;
}

$echo->return();
