<?php

use myLibrary\php\operations\User;
use myLibrary\php\page\creator;

class PageIndex extends Creator {
    private string $v;

    public function __construct() {
        $this->v = "?v=".date("YmdHis");
        $this->page_title = "Anasayfa";
        parent::__construct();
    }

    protected function customData(): array {
        return [];
    }

    protected function pageBody(): string {
        return static::setInclude(array(
            "./components/pages/index/main.php"
        ));
    }

    protected function customScripts(): string {
        return '
            <script src="assets/js/plugins/canvas-confetti.js" charset="utf-8"></script>
            <script src="assets/js/confetti.js"></script>
            <script src="assets/js/plugins/d3.v3.min.js" charset="utf-8"></script>
            <script src="assets/js/pages/index.js'.$this->v.'"></script>
        ';

    }

    protected function customLinks(): string {
        return '
            <link rel="stylesheet" href="assets/css/confetti.css">
            <link rel="stylesheet" href="assets/css/pages/index.css'.$this->v.'">
        ';

    }
}

$index = new PageIndex();