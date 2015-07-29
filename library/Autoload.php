<?php
spl_autoload_register(function ($class) {
    // a partial filename
    $part = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    $exp_part = explode(DIRECTORY_SEPARATOR, $part);
    if ('hubble' == strtolower($exp_part[0])) {
        // 移除项目名
        array_shift($exp_part);
    }
    $script_name = array_pop($exp_part);

    $file = WORK_PATH . '/' . strtolower(implode(DIRECTORY_SEPARATOR, $exp_part)) . '/' . $script_name;
    //echo $file , PHP_EOL; exit;
    if (is_readable($file)) {
        require_once($file);
        return;
    }
});
