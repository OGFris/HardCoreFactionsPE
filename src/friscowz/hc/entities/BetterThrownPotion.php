<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 22:55
 */

namespace friscowz\hc\entities;

use pocketmine\entity\projectile\Throwable;
use pocketmine\level\particle\HeartParticle;
use friscowz\hc\item\Potion;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\Player;

class BetterThrownPotion extends Throwable
{
    const NETWORK_ID = 86;

    const DATA_POTION_ID = 16;

    public $width = 0.25;
    public $length = 0.25;
    public $height = 0.25;

    protected $gravity = 0.1;
    protected $drag = 0.05;

    private $hasSplashed = false;

    /**
     * BetterThrownPotion constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     * @param Entity|null $shootingEntity
     */
    public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
        if(!isset($nbt["PotionId"])){
            $nbt["PotionId"] = new ShortTag("PotionId", Potion::HEALING_TWO);
        }

        parent::__construct($level, $nbt, $shootingEntity);

        //unset($this->dataProperties[self::DATA_SHOOTER_ID]);
        //$this->setDataProperty(self::DATA_POTION_ID, self::DATA_TYPE_SHORT, $this->getPotionId());
    }

    public function getPotionId() : int{
        return (int) $this->namedtag["PotionId"];
    }

    public function splash(int $type = 0){
        if($type == 0) {
            if (!$this->hasSplashed) {
                $this->hasSplashed = true;
                $color = Potion::getColor($this->getPotionId());
                //$this->getLevel()->addParticle(new SpellParticle($this, $color[0], $color[1], $color[2]));
                $this->getLevel()->addParticle(new HeartParticle($this));
                $radius = 6;
                foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow($radius, $radius, $radius)) as $p){
                    foreach(Potion::getEffectsById($this->getPotionId()) as $effect){
                        if($p instanceof Player){
                            $p->addEffect($effect);
                        }
                    }
                }

                $this->close();
            }
        } else {
            if(!$this->hasSplashed) {
                $this->hasSplashed = true;
                $this->getLevel()->addParticle(new HeartParticle($this));
                $radius = 6;
                foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow($radius, $radius, $radius)) as $p){
                    foreach(Potion::getEffectsById($this->getPotionId()) as $effect){
                        if($p instanceof Player){
                            $p->addEffect($effect);
                        }
                    }
                }
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

        $this->timings->startTiming();

        $hasUpdate = parent::onUpdate($currentTick);

        $this->age++;

        if($this->age > 1200 or $this->isCollided){
            $this->splash();
            $hasUpdate = true;
        }

        $this->timings->stopTiming();

        return $hasUpdate;
    }

}