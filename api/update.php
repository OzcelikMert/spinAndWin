<?php

namespace api\queries;

require "../myLibrary/php/autoLoader.php";

use config\DataKeys;
use myLibrary\php\api\Result;
use myLibrary\php\operations\User;
use myLibrary\php\operations\Variable;
use services\ItemService;
use utils\ImageUtil;

$echo = new Result();

if ($_POST) {
    Variable::clearAllData($_POST);
    $itemId = User::post(DataKeys::columnId);
    $text = trim(User::post(DataKeys::columnText));
    $fileImage = User::files(DataKeys::columnImage);
    $probability = trim(User::post(DataKeys::columnProbability));
    $qty = trim(User::post(DataKeys::columnQty));

    if (
        !Variable::isEmpty($itemId) &&
        !Variable::isEmpty($text) &&
        !Variable::isEmpty($probability) &&
        !Variable::isEmpty($qty)
    ) {
        $item = ItemService::get($itemId);
        $imageName = $item[DataKeys::columnImage];
        if($fileImage && !Variable::isEmpty($fileImage["name"]) && ImageUtil::checkType($fileImage)) {
            if(!Variable::isEmpty($imageName)) {
                ImageUtil::delete($imageName);
            }
            $imageName = ImageUtil::upload($fileImage);
        }
        $echo->rows = ItemService::update($itemId, $text, $imageName, $probability, $qty);
        $echo->status = true;
    }
}

$echo->return();
