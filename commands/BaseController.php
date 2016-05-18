<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use app\components\CronHelper;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BaseController extends Controller
{

    protected $whatAction;


    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($sourceModel, $days = 60)  // Action is default not Index
    {
        $source = ucwords($this->whatAction) . '_' . $sourceModel;
        if (($pid = CronHelper::lock($source)) !== FALSE) {
            $full_source = '\\app\\models\\' . $sourceModel;
            $action = $this->whatAction;
            $m = new $full_source;
            $data = $m->$action($days);
            echo $source . ' ' . $data['days'] . "\n";
            unset($data['days']);
            foreach ($data as $k => $v) {
                echo $k . ' is ' . $v . "\n";
            }
            CronHelper::unlock($source);
            echo $source . ' ran' . "\n";
        } else {
            echo $source . ' blocked' . "\n";
        }
    }
}