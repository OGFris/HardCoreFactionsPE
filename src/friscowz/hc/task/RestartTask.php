<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 17:00
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class RestartTask extends PluginTask
{
    private $plugin;
    private $time = 60*240*2;

    /**
     * RestartTask constructor.
     * @param Myriad $owner
     */
    public function __construct (Myriad $owner)
    {
        parent::__construct($owner);
        $this->setPlugin($owner);
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
        if($this->getTime() == 1) {
            Myriad::getManager()->setRestarting(true);
        }
        if($this->getTime() == 0) {
            $this->getHandler()->cancel();

            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player){
                $player->save(true);
                if($player instanceof MDPlayer){
                    $player->kick(TextFormat::RED . "Server restarting!", false);
                }
            }
            foreach ($this->getPlugin()->getServer()->getLevels() as $level){
                $level->save();
            }
            $this->getPlugin()->getServer()->shutdown();
        }
        $this->setTime($this->getTime()-1);
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
     *
     */
    public function cancel()
    {
        $this->getHandler()->cancel();
    }
}