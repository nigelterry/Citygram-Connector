<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 3/27/16
 * Time: 9:19 PM
 */

namespace app\components;

define('LOCK_DIR', __DIR__ . '/../runtime/locks/');
define('LOCK_SUFFIX', '.lock');

class CronHelper
{
    private static $pid;

    private static function isrunning()
    {
        $pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
        if (in_array(self::$pid, $pids))
            return true;
        return false;
    }

    public static function lock($source)
    {
        if(!file_exists(LOCK_DIR)) {
            mkdir(LOCK_DIR);
        }
        $lock_file = LOCK_DIR . $source . LOCK_SUFFIX;
        if (file_exists($lock_file)) {
            self::$pid = file_get_contents($lock_file);
            if (self::isrunning()) {
                error_log("==" . self::$pid . "== Already in progress...");
                return FALSE;
            } else {
                error_log("==" . self::$pid . "== Previous job died abruptly...");
            }
        }
        self::$pid = getmypid();
        file_put_contents($lock_file, self::$pid);
        error_log("==" . self::$pid . "== Lock acquired, processing the job...");
        return self::$pid;
    }

    public static function unlock($source)
    {
        $lock_file = LOCK_DIR . $source . LOCK_SUFFIX;
        if (file_exists($lock_file)) {
            unlink($lock_file);
        }
        error_log("==" . self::$pid . "== Releasing lock...");
        return TRUE;
    }
}