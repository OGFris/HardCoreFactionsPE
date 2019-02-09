<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 16/08/2017
 * Time: 22:12
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class InviteTask extends PluginTask
{
    private $plugin;
    private $time = 30;
    private $player;

    public function __construct(Myriad $owner, MDPlayer $player)
    {
        parent::__construct($owner);
        $this->setPlugin($owner);
        $this->setPlayer($player);
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
        $this->setTime($this->getTime()-1);
        if($this->getTime() == 0){
            $this->cancel();
            $this->getPlayer()->setInvited(false);
            $this->getPlayer()->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your invitation to join the faction " . $this->getPlayer()->getLastinvite() . " has expired!");
        }
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
     * @return int
     */
    public function getTime() : int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time)
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