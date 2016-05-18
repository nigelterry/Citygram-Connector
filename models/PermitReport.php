<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 3/27/16
 * Time: 6:25 PM
 */

namespace app\models;

abstract class PermitReport extends Report
{

    public function __construct()
    {
        parent::__construct();
        $this->config->messageType = 'PermitMessage';
        $this->config->messageUrl = 'permit-message';
    }
}