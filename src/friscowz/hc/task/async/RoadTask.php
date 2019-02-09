<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 24/10/2017
 * Time: 19:20
 */

namespace friscowz\hc\task\async;


use friscowz\hc\Manager;
use pocketmine\scheduler\AsyncTask;

class RoadTask extends AsyncTask
{

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {
        Manager::buildRoad(2000);
    }
}