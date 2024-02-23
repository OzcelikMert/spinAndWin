<?php
namespace services;

use config;
use config\DataKeys;
use utils\ItemUtil;
use myLibrary\php\operations\Variable;

class ItemService {
    public static function get($id = "") {
        $rows = ItemUtil::readJsonFile();

        if(!Variable::isEmpty($id)){
            foreach($rows as &$row) {
                if($row[DataKeys::columnId] == $id) {
                    return $row;
                }
            }
        }

        return $rows;
    }

    public static function add($text, $image, $probability, $qty) {
        $rows = ItemUtil::readJsonFile();

        $results = [
            DataKeys::columnId => uniqid(),
            DataKeys::columnText => $text,
            DataKeys::columnImage => $image,
            DataKeys::columnProbability => $probability,
            DataKeys::columnQty => $qty,
        ];

        $rows[] = $results;

        ItemUtil::writeJsonFile(json_encode($rows));

        return $results;
    }

    public static function update($id, $text, $image, $probability, $qty) {
        $rows = ItemUtil::readJsonFile();

        $results = [
            DataKeys::columnText => $text,
            DataKeys::columnImage => $image,
            DataKeys::columnProbability => $probability,
            DataKeys::columnQty => $qty,
        ];

        foreach($rows as &$row) {
            if($row[DataKeys::columnId] == $id) {
                $row = [
                    ...$row,
                    ...$results
                ];
                break;
            }
        }

        ItemUtil::writeJsonFile(json_encode($rows));

        return $results;
    }

    public static function updateQty($id, $qty) {
        $rows = ItemUtil::readJsonFile();

        $results = [
            DataKeys::columnQty => $qty,
        ];

        foreach($rows as &$row) {
            if($row[DataKeys::columnId] == $id) {
                $row = [
                    ...$row,
                    ...$results
                ];
                break;
            }
        }

        ItemUtil::writeJsonFile(json_encode($rows));

        return $results;
    }

    public static function delete($id) {
        $rows = ItemUtil::readJsonFile();
        $results = [];

        foreach($rows as $key => $row) {
            if($row[DataKeys::columnId] == $id) {
                $results = $row;
                unset($rows[$key]);
                break;
            }
        }

        $rows = array_values($rows);

        ItemUtil::writeJsonFile(json_encode($rows));

        return $results;
    }

    public static function deleteAll() {
        return ItemUtil::writeJsonFile(json_encode([]));
    }
}