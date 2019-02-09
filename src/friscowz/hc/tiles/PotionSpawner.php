<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 10:27 PM
 */

namespace friscowz\hc\tiles;

use friscowz\hc\item\SplashPotion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Tile;

class PotionSpawner extends Tile
{
    private $time = 20*60;
    /**
     * PotionSpawner constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt)
    {
        parent::__construct($level, $nbt);
    }

    /*
    public function onUpdate() : bool
    {
        $this->getLevel()->dropItem($this->add(0, 1), new SplashPotion(22, mt_rand(1, 2)));
        if ($this->time == 0){
            $this->time = 20*60;
        } else {
            --$this->time;
        }
        return true;
    }*/

    /**
     * @return string
     */
    public function getName() : string
    {
        return "PotionSpawner";
    }
}