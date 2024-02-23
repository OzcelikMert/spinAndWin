<?php

namespace api\queries;

require "../myLibrary/php/autoLoader.php";

use config\DataKeys;
use myLibrary\php\api\ErrorCodes;
use myLibrary\php\api\Result;
use myLibrary\php\operations\User;
use myLibrary\php\operations\Variable;
use services\ItemService;
use utils\ImageUtil;

$echo = new Result();

if ($_POST) {
    Variable::clearAllData($_POST);

    $itemId = User::post(DataKeys::columnId);

    if (
        !Variable::isEmpty($itemId)
    ) {
        $item = ItemService::get($itemId);
        $imageName = $item[DataKeys::columnImage];
        if(!Variable::isEmpty($imageName)) {
            ImageUtil::delete($imageName);
        }
        $echo->rows = ItemService::delete($itemId);
        $echo->status = true;
    }
}

$echo->return();
