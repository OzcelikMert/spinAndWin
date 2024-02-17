<?php
namespace myLibrary\php\api;

abstract class ErrorCodes {
    const SUCCESS = 0x0000,
        INCORRECT_DATA = 0x0001,
        EMPTY_VALUE = 0x0002,
        WRONG_VALUE = 0x0003,
        REGISTERED_VALUE = 0x0004,
        SQL_SYNTAX = 0x0005,
        NOT_FOUND = 0x0006,
        UPLOAD_ERROR = 0x0007,
        NOT_LOGGED_IN = 0x0008,
        NO_PERM = 0x0009,
        IP_BLOCK = 0x0010,
        NO_QUERY_PERM = 0x0011,
        UNKNOWN_ERROR = 0x0012;
}