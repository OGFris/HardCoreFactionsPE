<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 01:18
 */

namespace friscowz\hc\task;


use friscowz\hc\modules\SOTW;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;

class SotwTask extends PluginTask
{

    private $plugin;
    private $time;

    /**
     * SotwTask constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
        $this->setTime(60*90);
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun (int $currentTick)
    {
        $this->setTime($this->getTime()-1);
        if($this->getTime() == 0){
            SOTW::stop();
        }
    }

    /**
     * @return Myriad
     */
    public function getPlugin () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return mixed
     */
    public function getTime () : int
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime (int $time)
    {
        $this->time = $time;
    }

    /**
     *
     */
    public function cancel()
    {
        $this->getHandler()->cancel();
    }
}