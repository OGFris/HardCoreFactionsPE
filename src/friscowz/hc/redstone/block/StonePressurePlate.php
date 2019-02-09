<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/17/2017
 * Time: 2:33 AM
 */

namespace friscowz\hc\redstone\block;

use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\CloseDoorTask;
use pocketmine\block\Door;
use pocketmine\block\Transparent;
use pocketmine\entity\Entity;
use pocketmine\item\Item;

class StonePressurePlate extends Transparent{

    protected $id = self::STONE_PRESSURE_PLATE;

    public function __construct(int $meta = 0)
    {
        parent::__construct($this->id, $meta, $this->getName(), $this->id);
    }

    /**
     * @param Entity $entity
     */
    public function onEntityCollide(Entity $entity): void
    {
        if($this->getDamage() == 0) {
            for ($x = $this->x - 1; $x < ($this->x + 1); ++$x) {
                for ($z = $this->z - 1; $z < ($this->z + 1); ++$z) {
                    $block = $this->getLevel()->getBlockAt($x, $this->y, $z);
                    if ($block instanceof Door) {
                        if ($entity instanceof MDPlayer) {
                            if (Myriad::getFactionsManager()->isFactionClaim($this->asVector3())) {
                                if ($entity->getFaction() == Myriad::getFactionsManager()->getClaimer($this->x, $this->z)) {
                                    $block->onActivate(Item::get(Item::AIR), $entity);
                                    new CloseDoorTask($block, $this);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getName() : string
    {
        return "Stone Pressure Plate";
    }

    public function isSolid() : bool
    {
        return false;
    }

    public function getHardness() : float
    {
        return 0.5;
    }

    public function getVariantBitmask() : int
    {
        return 0;
    }
}