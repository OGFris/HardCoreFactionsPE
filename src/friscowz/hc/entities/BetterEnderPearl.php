<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 14/08/2017
 * Time: 23:49
 */

namespace friscowz\hc\entities;


use friscowz\hc\Myriad;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\Item;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class BetterEnderPearl extends Throwable
{
    const NETWORK_ID = 87;

    public $width = 0.25;
    public $length = 0.25;
    public $height = 0.25;

    protected $gravity = 0.03;
    protected $drag = 0.01;

    private $hasTeleportedShooter = false;
    private $shootingEntity;
    private $last;
    /**
     * BetterEnderPearl constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     * @param Entity|null $shootingEntity
     */
    public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
        $this->shootingEntity = $shootingEntity;
        if($this->getPosition()->getLevel()->getBlockIdAt($this->x, $this->y, $this->z) != 0){
            $shootingEntity->teleport($shootingEntity);
            $this->kill();
        }
        $this->setLast(new Vector3(0, 0, 0));
    }

    /**
     *
     */
    public function teleportShooter(){

        if(Myriad::getFactionsManager()->isSpawnClaim($this->getPosition())){
            $this->kill();
            if($this->shootingEntity instanceof MDPlayer){
                $this->shootingEntity->sendPopup(TextFormat::RED . "You can't pearl to spawn!");
                $this->shootingEntity->getInventory()->addItem(Item::get(ItemIds::ENDER_PEARL, 0, 1));
            }
        }
        if(!$this->hasTeleportedShooter){
            $this->hasTeleportedShooter = true;
            if($this->y > 0){
                $this->shootingEntity->attack(new EntityDamageEvent($this->shootingEntity, EntityDamageEvent::CAUSE_FALL, 5));
                $this->shootingEntity->teleport($this->getLast());
                $this->shootingEntity->getLevel()->addSound(new EndermanTeleportSound($this->getPosition()->asVector3()), [$this->shootingEntity]);
            }

            $this->kill();
        }
    }

    /**
     * @return bool
     */
    public function isGoingToColide() : bool
    {
        for($x = ($this->x - 1); $x < ($this->x + 1); ++$x){
            for ($z = ($this->z - 1); $z < ($this->z + 1); ++$z){
                if($this->getLevel()->getBlockAt($x, $this->y, $z)->isSolid()){
                    return true;
                }
            }
        }
        return false;
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

        $this->checkLast();

        $this->timings->startTiming();

        $hasUpdate = parent::onUpdate($currentTick);

        if($this->age > 1200 or $this->isCollided){
            $this->teleportShooter();
            $hasUpdate = true;
        }

        $this->timings->stopTiming();

        return $hasUpdate;
    }

    public function checkLast()
    {
        $x = $this->getPosition()->getFloorX();
        $y = $this->getPosition()->getFloorY();
        $z = $this->getPosition()->getFloorZ();

        $new = $this->getPosition();

        if($new->distanceSquared($this->getLast()) > 1){
            $this->setLast(new Vector3($x, $y, $z));
        }
    }

    /**
     * @return Vector3
     */
    public function getLast () : Vector3
    {
        return $this->last;
    }

    /**
     * @param Vector3 $Last
     */
    public function setLast (Vector3 $Last)
    {
        $this->last = $Last;
    }
}