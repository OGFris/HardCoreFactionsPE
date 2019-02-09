<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 21:19
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\level\Position;
use pocketmine\scheduler\PluginTask;

class KickTask extends PluginTask
{
    private $message = "You got kicked by an admin !";
    private $plugin;
    private $player;
    private $spawn = false;

    /**
     * KickTask constructor.
     * @param Myriad $owner
     * @param string $message
     * @param MDPlayer $player
     * @param bool $spawn
     */
    public function __construct (Myriad $owner, string $message, MDPlayer $player, bool $spawn = false)
    {
        parent::__construct($owner);
        $this->setSpawn($spawn);
        $this->setPlugin($owner);
        $this->getPlugin()->getServer()->getScheduler()->scheduleDelayedTask($this, 20);
        $this->setPlayer($player);
        $this->setMessage($message);
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
        if($this->isSpawn()) {
            $this->getPlayer()->teleport(new Position(0, 66, 0, $this->getPlugin()->getServer()->getDefaultLevel()), 0, 0);
        }
        $this->getPlayer()->kick($this->getMessage(), false);
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
     * @return string
     */
    public function getMessage () : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage (string $message)
    {
        $this->message = $message;
    }

    /**
     * @return MDPlayer
     */
    public function getPlayer () : MDPlayer
    {
        return $this->player;
    }

    /**
     * @param MDPlayer $player
     */
    public function setPlayer (MDPlayer $player)
    {
        $this->player = $player;
    }

    /**
     * @return bool
     */
    public function isSpawn() : bool
    {
        return $this->spawn;
    }

    /**
     * @param bool $spawn
     */
    public function setSpawn(bool $spawn)
    {
        $this->spawn = $spawn;
    }
}