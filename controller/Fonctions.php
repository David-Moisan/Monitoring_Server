<?php

function check_recordset(?object $result, ?string $sql = null)
{
    global $config;
    global $db;

    if ($result === false) {
        if ($config['DEBUG']) {
            display_SQL_errors($sql);
        }

        $result = NULL;
        return false;
    } else {
        if (is_object($result)) {
            return $result;
        }

        if ($result != 0) {
            return $result;
        } else {
            $result = NULL;
            return 0;
        }
    }
}

function display_SQL_errors(?string $sql)
{
    echo '<br><br>##########################################<br>';
    echo 'an error occurred in the SQL query : ' . $sql;
    echo '<br><br>##########################################<br>';
}

function hr(string $str = '')
{
    echo $str . '<hr>';
}

function br(string $str = '')
{
    echo $str . '<br/>';
}

function dump(string $str = '')
{
    var_dump($str);
}
