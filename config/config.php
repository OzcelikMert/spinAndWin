<?php
namespace config;

use myLibrary\php\ProjectConfig;

Class Config {
    const jsonFileName = "data.json";

    public static function jsonFilePath() { return ProjectConfig::projectRootPath() . "/" . Config::jsonFileName; }
}

?>