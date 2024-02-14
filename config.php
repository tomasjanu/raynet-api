<?php
function get_env_variable($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

$dotenv = parse_ini_file('.env', true);
foreach ($dotenv as $key => $value) {
    putenv("$key=$value");
}