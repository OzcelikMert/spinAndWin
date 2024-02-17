<?php

namespace api\queries;

require "../myLibrary/php/autoLoader.php";

use config\DataKeys;
use myLibrary\php\api\ErrorCodes;
use myLibrary\php\api\Result;
use myLibrary\php\operations\User;
use myLibrary\php\operations\Variable;
use services\ItemService;

$echo = new Result();

if ($_POST) {
    $text = trim(User::post(DataKeys::columnText));
    $probability = trim(User::post(DataKeys::columnProbability));
    $qty = trim(User::post(DataKeys::columnQty));

    if (
        !Variable::isEmpty($text) ||
        !Variable::isEmpty($probability) ||
        !Variable::isEmpty($qty)
    ) {
        $echo->rows = ItemService::add($text, $probability, $qty);
        $echo->status = true;
    }
}

$echo->return();
