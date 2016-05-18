<?php
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 3/27/16
 * Time: 6:25 PM
 */

namespace app\models;

abstract class ZoningReport extends Report
{
    public function __construct()
    {
        parent::__construct();
        $this->config->messageType = 'ZoningMessage';
        $this->config->messageUrl = 'zoning-message';
    }
}