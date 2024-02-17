<?php

namespace api\queries;

require "../myLibrary/php/autoLoader.php";

use myLibrary\php\api\ErrorCodes;
use myLibrary\php\api\Result;
use myLibrary\php\operations\User;
use myLibrary\php\operations\Variable;
use services\ItemService;

$echo = new Result();

if ($_POST) {
    Variable::clearAllData($_POST);

    $wordId = User::post("itemId");

    if (
        !Variable::isEmpty($wordId)
    ) {
        $echo->rows = ItemService::delete($wordId);
        $echo->status = true;
    }
}

$echo->return();
