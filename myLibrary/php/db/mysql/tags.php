<?php
namespace myLibrary\php\db\mysql;

abstract class Tags {
    const LIKE = "LIKE",
        NOT_LIKE = "NOT LIKE",
        GREATER_THAN = ">",
        LESS_THAN = "<",
        EQUALS = "=",
        IS_NULL = "IS NULL",
        AND = "AND",
        OR = "OR",
        ASC = "ASC",
        DESC = "DESC",
        WHERE = "WHERE",
        GROUP_BY = "GROUP BY",
        ORDER_BY = "ORDER BY",
        JOIN = "JOIN",
        LEFT = "LEFT",
        RIGHT = "RIGHT",
        INNER = "INNER",
        LIMIT = "LIMIT",
        DISTINCT = "DISTINCT",
        BETWEEN = "BETWEEN";
}