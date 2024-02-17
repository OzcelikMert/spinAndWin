<?php

namespace api\queries;

require "../myLibrary/php/autoLoader.php";

use config\DataKeys;
use myLibrary\php\api\Result;
use myLibrary\php\operations\User;
use myLibrary\php\operations\Variable;
use services\ItemService;

$echo = new Result();

if ($_POST) {
    $itemId = User::post(DataKeys::columnId);
    $qty = trim(User::post(DataKeys::columnQty));

    if (
        !Variable::isEmpty($itemId) ||
        !Variable::isEmpty($qty)
    ) {
        $echo->rows = ItemService::updateQty($itemId, $qty);
        $echo->status = true;
    }
}

$echo->return();
