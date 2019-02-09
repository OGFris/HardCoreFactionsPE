<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/30/2017
 * Time: 10:52 PM
 */

namespace friscowz\hc\task;


use friscowz\hc\Myriad;
use pocketmine\block\Door;
use pocketmine\item\Item;
use pocketmine\scheduler\PluginTask;

class CloseDoorTask extends PluginTask
{
    private $door;
    private $plate;
    private $time = 4;
    /**
     * CloseDoorTask constructor.
     * @param Door $door
     * @param $plate
     */
    public function __construct(Door $door, $plate)
    {
        parent::__construct(Myriad::getInstance());
        $this->setDoor($door);
        $this->plate = $plate;
        $this->setHandler(Myriad::getInstance()->getServer()->getScheduler()->scheduleRepeatingTask($this, 10));
    }

    /**
     * @return Door
     */
    public function getDoor(): Door
    {
        return $this->door;
    }

    /**
     * @param Door $door
     */
    public function setDoor(Door $door)
    {
        $this->door = $door;
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
        if($this->time == 0){
            $this->getHandler()->cancel();
            $this->getDoor()->onActivate(new Item(0), null);
            $this->plate->setDamage(0);
            return;
        }
        --$this->time;
    }
}