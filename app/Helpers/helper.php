<?php

/**
 * Проверка строки на тип JSON
 *
 * @param $string
 * @return bool
 */
function isJson($string): bool
{
    json_decode($string, true);

    return (json_last_error() === JSON_ERROR_NONE);
}
