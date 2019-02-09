<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 21/08/2017
 * Time: 20:22
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class LogoutTask extends PluginTask
{
    private $plugin;
    private $player;
    /**
     * LogoutTask constructor.
     * @param Myriad $plugin
     * @param MDPlayer $player
     */
    public function __construct(Myriad $plugin, MDPlayer $player)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setPlayer($player);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));

    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        if($this->getPlayer()->isLogout()){
            if($this->getPlugin()->getServer()->getTicksPerSecondAverage() > 10) {
                $this->getPlayer()->setLogoutTime($this->getPlayer()->getLogoutTime() - 1);
            } else {
                $this->getPlayer()->setLogoutTime($this->getPlayer()->getLogoutTime() - 2);
            }
        } else {
            $this->cancel();
            return;
        }

        if($this->getPlayer()->getLogoutTime() <= 0){
            $this->cancel();
            if($this->getPlayer()->isOnline()) $this->getPlayer()->kick(\Utils::getPrefix() . TextFormat::GREEN . "You have successfully logged out!", false);
        }
    }

    /**
     *
     */
    public function cancel()
    {
        $this->getHandler()->cancel();
        $this->getPlayer()->setLogoutTime(300);
    }

    /**
     * @return Myriad
     */
    public function getPlugin() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin(Myriad $plugin)
    {
        $this->plugin = $plugin;
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