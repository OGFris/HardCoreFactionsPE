<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 14/08/2017
 * Time: 21:02
 */

namespace friscowz\hc\entities;


use pocketmine\entity\Vehicle;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\EntityEventPacket;
use pocketmine\Player;

class BetterBoat extends Vehicle
{
    const NETWORK_ID = 90;

    public $height = 0.7;
    public $width = 1.6;

    public $gravity = 0.5;
    public $drag = 0.1;

    /**
     * BetterBoat constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt){
        if(!isset($nbt->WoodID)){
            $nbt->WoodID = new IntTag("WoodID", 0);
        }
        parent::__construct($level, $nbt);
        $this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $this->getWoodID());
    }

    public function getWoodID() : int{
        return (int) $this->namedtag["WoodID"];
    }

    /**
     * @param Player $player
     */
    public function spawnTo(Player $player){
        $pk = new AddEntityPacket();
        $pk->eid = $this->getId();
        $pk->type = BetterBoat::NETWORK_ID;
        $pk->x = $this->x;
        $pk->y = $this->y;
        $pk->z = $this->z;
        $pk->speedX = 0;
        $pk->speedY = 0;
        $pk->speedZ = 0;
        $pk->yaw = 0;
        $pk->pitch = 0;
        $pk->metadata = $this->dataProperties;
        $player->dataPacket($pk);

        parent::spawnTo($player);
    }



    /**
     * @param EntityDamageEvent $source
     * @return bool|void
     */
    public function attack(EntityDamageEvent $source){
        parent::attack($source);

        if(!$source->isCancelled()){
            $pk = new EntityEventPacket();
            $pk->eid = $this->id;
            $pk->event = EntityEventPacket::HURT_ANIMATION;
            foreach($this->getLevel()->getPlayers() as $player){
                $player->dataPacket($pk);
            }
        }
    }

    /**
     * @param int $currentTick
     * @return bool
     */
    public function onUpdate(int $currentTick) : bool
    {
        if($this->closed){
            return false;
        }
        $tickDiff = $currentTick - $this->lastUpdate;
        if($tickDiff <= 0 and !$this->justCreated){
            return true;
        }

        $this->lastUpdate = $currentTick;

        $this->timings->startTiming();

        $hasUpdate = $this->entityBaseTick($tickDiff);

        if(!$this->level->getBlock(new Vector3($this->x,$this->y,$this->z))->getBoundingBox()==null or $this->isInsideOfWater()){
            $this->motionY = 0.1;
        }else{
            $this->motionY = -0.08;
        }

        $this->move($this->motionX, $this->motionY, $this->motionZ);
        $this->updateMovement();

        if($this->linkedEntity == null or $this->linkedType = 0){
            if($this->age > 1500){
                $this->close();
                $hasUpdate = true;
                //$this->scheduleUpdate();

                $this->age = 0;
            }
            $this->age++;
        }else $this->age = 0;

        $this->timings->stopTiming();


        return $hasUpdate or !$this->onGround or abs($this->motionX) > 0.00001 or abs($this->motionY) > 0.00001 or abs($this->motionZ) > 0.00001;
    }


    /**
     * @return array
     */
    public function getDrops(){
        return [
            Item::get(Item::BOAT, 0, 1)
        ];
    }

    /**
     * @return string
     */
    public function getSaveId(){
        $class = new \ReflectionClass(static::class);
        return $class->getShortName();
    }

}