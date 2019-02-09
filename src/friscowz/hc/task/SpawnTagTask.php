<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 18:01
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;

class SpawnTagTask extends PluginTask
{
    private $plugin;
    private $player;

    public function __construct(Myriad $plugin, MDPlayer $player)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setPlayer($player);
        $this->getPlayer()->setTagtime(30);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        if($this->getPlayer()->getTagtime() <= 0){
            $this->getPlayer()->setTagged(false);
            $this->getPlayer()->setTagtime(0);
            $this->cancel();
            return;
        } else {
            $time = $this->getPlayer()->getTagtime();
            $this->getPlayer()->setTagtime($time - 1);
        }
    }

    /**
     *
     */
    public function cancel()
    {
        $this->getHandler()->cancel();
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

    /**
     * @return mixed
     */
    public function getPlayer () : MDPlayer
    {
        return $this->player;
    }

    /**
     * @param mixed $player
     */
    public function setPlayer (MDPlayer $player)
    {
        $this->player = $player;
    }
}