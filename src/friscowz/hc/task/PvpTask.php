<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 02:11
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;

class PvpTask extends PluginTask
{
    private $plugin;
    private $player;

    /**
     * PvpTask constructor.
     * @param Myriad $plugin
     * @param MDPlayer $player
     */
    public function __construct (Myriad $plugin, MDPlayer $player)
    {
        parent::__construct($plugin);
        $this->setPlayer($player);
        $this->setPlugin($plugin);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
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
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun (int $currentTick)
    {
        $player = $this->getPlayer();
        if ($player instanceof MDPlayer) {
            if ($player->isPvp() and !Myriad::getFactionsManager()->isSpawnClaim($player)) {
                if ($player->getPvptime() <= 0) {
                    $player->setPvp(false);
                    return;
                }
                $player->setPvptime($player->getPvptime() - 1);
            }
        } else {
            $this->cancel();
        }
    }

    public function cancel()
    {
        $this->getHandler()->cancel();
    }

    /**
     * @return MDPlayer
     */
    public function getPlayer() : MDPlayer
    {
        return $this->player;
    }

    /**
     * @param MDPlayer $player
     */
    public function setPlayer(MDPlayer $player)
    {
        $this->player = $player;
    }
}