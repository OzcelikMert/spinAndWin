<?php

namespace utils;

use config\Config;
use myLibrary\php\operations\User;
use myLibrary\php\ProjectConfig;

class ItemUtil {
    private static function initJsonFile(): bool {
        if(file_exists(Config::jsonFilePath())){
            return true;
        }
        return file_put_contents(Config::jsonFilePath(), json_encode([]));
    }

    public static function writeJsonFile(string $string): bool {
        ItemUtil::initJsonFile();
        return file_put_contents(Config::jsonFilePath(), $string);
    }

    public static function readJsonFile(): array {
        ItemUtil::initJsonFile();
        return json_decode(file_get_contents(Config::jsonFilePath()), true);
    }
}
