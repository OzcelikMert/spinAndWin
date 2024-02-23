<?php

namespace utils;

use config\Config;
use myLibrary\php\operations\User;
use myLibrary\php\ProjectConfig;

class ImageUtil {
    private static string $dir = "../uploads/";
    private static function getImageType($file): string {
        return strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    }
    private static function createName($file): string {
        $today = date('YmdHis');
        $random = rand(1, 999999);
        return $today."-".$random.".".ImageUtil::getImageType($file);
    }
    private static function nameWithDir(string $name) { return ImageUtil::$dir.$name; }
    private static function checkDir(): bool {
        if(!is_dir(ImageUtil::$dir)) {
            return mkdir(ImageUtil::$dir, 0770, true);
        }
        return true;
    }
    public static function checkType($file): bool {
        $imageFileType = ImageUtil::getImageType($file);
        return ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif" || $imageFileType == "webp");
    }

    public static function upload($file): string {
        if(ImageUtil::checkDir()){
            $name = ImageUtil::createName($file);
            move_uploaded_file($file["tmp_name"], ImageUtil::nameWithDir($name));
            return $name;
        }
        return "";
    }

    public static function delete(string $name): bool {
        $fileNameWithDir = ImageUtil::nameWithDir($name);
        if(file_exists($fileNameWithDir)){
            return unlink($fileNameWithDir);
        }
        return false;
    }
}