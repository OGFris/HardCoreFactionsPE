<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 22:56
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class Teleport extends PluginTask
{
    private $plugin;
    private $time = 0;
    private $player;
    private $message = "You have been teleported !";
    private $pos;

    /**
     * Teleport constructor.
     * @param Myriad $plugin
     * @param MDPlayer $player
     * @param string $message
     * @param int $time
     * @param Vector3 $pos
     */
    public function __construct (Myriad $plugin, MDPlayer $player, string $message, int $time = 1, Vector3 $pos)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setPlayer($player);
        $this->setMessage($message);
        $this->setTime($time);
        $this->setPos($pos);
        $player->setTeleporting(true);
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
        $this->setTime($this->getTime() - 1);
        if(!$this->getPlayer()->isTeleporting()){
            $this->cancel();
            return;
        }
        if($this->getTime() == 0){
            $this->getPlayer()->teleport($this->getPos());
            $this->getPlayer()->sendMessage($this->getMessage());
            $this->getPlayer()->setTeleporting(false);
            $this->cancel();
        }
    }

    /**
     *
     */
    public function cancel()
    {
        if(!$this->getHandler()->isCancelled()) {
            $this->getHandler()->cancel();
        }
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
     * @return int
     */
    public function getTime (): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime (int $time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getMessage (): string
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

    /**
     * @return Vector3
     */
    public function getPos (): Vector3
    {
        return $this->pos;
    }

    /**
     * @param Vector3 $pos
     */
    public function setPos (Vector3 $pos)
    {
        $this->pos = $pos;
    }
}