<?php
namespace myLibrary\php\db\mysql;

class Results {
    public array $rows = array();
    public bool $status = false;
    public string $message = "";
    public string $sql = "";
    public int $insertId = 0;
}