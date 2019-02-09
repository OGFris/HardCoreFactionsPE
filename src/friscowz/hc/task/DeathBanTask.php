<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 20:35
 */

namespace friscowz\hc\task;


use friscowz\hc\modules\ModulesManager;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;

class DeathBanTask extends PluginTask
{
    private $plugin;

    /**
     * DeathBanTask constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
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
        ModulesManager::get(ModulesManager::DEATHBAN)->check();
    }

    /**
     * @return mixed
     */
    public function getPlugin () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }
}