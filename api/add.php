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
    $text = trim(User::post(DataKeys::columnText));
    $fileImage = User::files(DataKeys::columnImage);
    $probability = trim(User::post(DataKeys::columnProbability));
    $qty = trim(User::post(DataKeys::columnQty));

    if (
        !Variable::isEmpty($text) &&
        !Variable::isEmpty($probability) &&
        !Variable::isEmpty($qty)
    ) {
        $imageName = "";
        if($fileImage && !Variable::isEmpty($fileImage["name"]) && ImageUtil::checkType($fileImage)) {
            $imageName = ImageUtil::upload($fileImage);
        }
        $echo->rows = ItemService::add($text, $imageName, $probability, $qty);
        $echo->status = true;
    }
}

$echo->return();
