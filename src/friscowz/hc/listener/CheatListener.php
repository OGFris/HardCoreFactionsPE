<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 24/10/2017
 * Time: 19:39
 */

namespace friscowz\hc\listener;

use friscowz\hc\MDPlayer;
use pocketmine\entity\Effect;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\SetPlayerGameTypePacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;
use friscowz\hc\Myriad;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;

class CheatListener implements Listener
{
    private $time = [];
    private $point = [];
    /**
     * CheatListener constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param MDPlayer $player
     */
    public static function seeBlocks(MDPlayer $player)
    {
        $start = microtime(true);
        $count = 0;
        for ($x = $player->x - 5; $x < $player->x + 5; ++$x) {
            for ($z = $player->z - 5; $z < $player->z + 5; ++$z) {
                for ($y = $player->y - 5; $y <= $player->y + 5; $y++) {
                    if (self::isOre($player->getLevel()->getBlockIdAt($x, $y, $z)) and self::canSee($player->getLevel()->getBlock(new Vector3($x, $y, $z)))) {
                        ++$count;
                        self::setFakeBlock($x, $y, $z, $player, $player->getLevel()->getBlock(new Vector3($x, $y, $z))->getId(), 0);
                    }
                }
            }
        }
        $end = microtime(true);
        $time = $end - $start;
    }

    /**
     * @param MDPlayer $player
     */
    public static function hideBlocks(MDPlayer $player)
    {
        $start = microtime(true);
        $count = 0;
        for ($x = $player->x - 16; $x < $player->x + 16; ++$x) {
            for ($z = $player->z - 16; $z < $player->z + 16; ++$z) {
                for ($y = 1; $y <= 32; $y++) {
                    if (self::isOre($player->getLevel()->getBlockIdAt($x, $y, $z))) {
                        ++$count;
                        $player->setFakeBlock(new Vector3($x, $y, $z), 0, 0);
                    }
                }
            }
        }
        $end = microtime(true);
        $time = $end - $start;
        echo "[AntiXray](Hide) Time: $time" . PHP_EOL;
        echo "[AntiXray](Hide) Blocks count: $count" . PHP_EOL;
    }

    /**
     * @param $block
     * @return bool
     */
    public static function isOre($block)
    {
        if ($block == 56 || $block == 73 || $block == 15) return true;
        return false;
    }

    /*
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof MDPlayer) {
            if ($event->getFrom()->x == $event->getTo()->x || $event->getFrom()->z == $event->getTo()->z) return;
            TODO: Fix the lag
            //self::seeBlocks($player);
            if(isset($this->time[$player->getName()])){
                $rn = time();
                if(($rn - $this->time[$player->getName()]) > 3){
                    $this->time[$player->getName()] = $rn;
                    //self::hideBlocks($player);
                }
            } else {
                $this->time[$player->getName()] = time();
                //self::hideBlocks($player);
            }
            foreach ($player->getLevel()->getTiles() as $tile) {
                if ($tile instanceof Chest || $tile instanceof EnderChest || $tile instanceof EnchantTable || $tile instanceof MobSpawner || $tile instanceof Furnace) {
                    if ($player->distance($tile->getBlock()) > 5) {
                        $block = $tile->getBlock();
                        $player->setFakeBlock(new Vector3($block->x, $block->y, $block->z), 0, 0);
                    }else {
                        $block = $tile->getBlock();
                        if (self::canSee($block)) $player->setFakeBlock(new Vector3($block->x, $block->y, $block->z), $block->getId(), 0);
                    }
                }
            }
        }
    }*/

    /**
     * @param $block
     * @return bool
     */
    public static function canSee($block)
    {
        foreach ([$block->getLevel()->getBlockIdAt($block->x, $block->y + 1, $block->z), $block->getLevel()->getBlockIdAt($block->x, $block->y - 1, $block->z), $block->getLevel()->getBlockIdAt($block->x, $block->y, $block->z + 1), $block->getLevel()->getBlockIdAt($block->x, $block->y, $block->z - 1), $block->getLevel()->getBlockIdAt($block->x + 1, $block->y, $block->z), $block->getLevel()->getBlockIdAt($block->x - 1, $block->y, $block->z)] as $block) {
            if($block == 0) true;
        }
        return false;
    }

    /*
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            if ($event->getFrom()->x == $event->getTo()->x || $event->getFrom()->z == $event->getTo()->z) return;
            if($player->isFlying() or !$player->isSurvival()) return;
            $block = $player->getLevel()->getBlock(new Vector3($player->getFloorX(), $player->getFloorY(), $player->getFloorZ()));
            if($block->getId() === 0){
                if($player->getFlyHack() <= 5) {
                    $player->addFlyHack();
                } else {
                    $player->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] FlyHack.", false);
                    Myriad::getInstance()->getServer()->broadcastMessage(TextFormat::RED . TextFormat::BOLD . "[MDCheat]" . TextFormat::RESET . TextFormat::RED . $player->getName() . " got banned for using a FlyHack!");
                    $player->setBanned(true);
                }
            } else {
                $player->setFlyHack(0);
            }
        }
    }*/

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event){
        $player = $event->getPlayer();
        $oldPos = $event->getFrom();
        $newPos = $event->getTo();
        if($player instanceof MDPlayer) {
            if (!$player->isCreative() && !$player->isSpectator() && !$player->isOp() && !$player->getAllowFlight() && $player->getDeviceOS() == MDPlayer::OS_ANDROID) {
                $value = [
                    "fly" => 0,
                    "distance" => 0
                ];
                $FlyMove = (float)round($newPos->getY() - $oldPos->getY(), 3);
                $DistanceMove = (float)round(sqrt(($newPos->getX() - $oldPos->getX()) ** 2 + ($newPos->getZ() - $oldPos->getZ()) ** 2), 2);
                if ($FlyMove === (float)-0.002 || $FlyMove === (float)-0.003) {
                    $value["distance"] += 3;
                }
                $value["fly"] += $FlyMove;
                $value["distance"] += $DistanceMove;

                $key = $player->getName();

                if ((float)$value["distance"] > (float)7.6) {
                    $this->point[$key]["distance"] += (float)1;
                    if ((float)$this->point[$key]["distance"] > (float)3 and !$player->hasEffect(Effect::SPEED)) {
                        if ($player instanceof Player) {
                            $player->kick(TextFormat::RED . "Please disable your hacks to play on Legacy!", false);
                        }
                    }
                } else {
                    $this->point[$key]["distance"] = (float)0;
                }
                if ((float)$value["fly"] > (float)7.4) {
                    $this->point[$key]["fly"] += (float)1;
                    if ((float)$this->point[$key]["fly"] > (float)3) {
                        if ($player instanceof Player) {
                            $player->kick(TextFormat::RED . "Please disable your hacks to play on Legacy!", false);
                        }
                    }
                } else {
                    $this->point[$key]["fly"] = (float)0;
                }
            }
        }
    }


    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {

        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent and $entity instanceof MDPlayer){
            $damager = $event->getDamager();
            if($damager instanceof MDPlayer){
                if($damager->getGamemode() == Player::CREATIVE or $damager->getGamemode() == Player::SPECTATOR or $damager->isOp()) return;
                $xmin = min($damager->getFloorX(), $entity->getFloorX());
                $xmax = max($damager->getFloorX(), $entity->getFloorX());
                $zmin = min($damager->getFloorZ(), $entity->getFloorZ());
                $zmax = max($damager->getFloorZ(), $entity->getFloorZ());
                /*

                if(Myriad::getFactionsManager()->isFactionClaim($entity->asVector3()) and $entity->isInFaction()) {
                    if(Myriad::getFactionsManager()->getClaimer($entity->x, $entity->z) == $entity->getFaction()) {
                        for ($x = $xmin; $x <= $xmax; $x++) {
                            for ($z = $zmin; $z <= $zmax; $z++) {
                                $bool = false;
                                if ($entity->getLevel()->getBlockAt($x, $entity->getY(), $z)->isSolid()) {
                                    $bool = true;
                                }

                                if ($entity->getLevel()->getBlockAt($x, $entity->getY(), $z)->isSolid() and $bool == true) {
                                    $event->setCancelled(true);
                                    return;
                                }
                            }
                        }
                    }
                }*/


                $distance = $damager->distance($entity);
                $max = 4;
                switch ($damager->getDeviceOS()){
                    case MDPlayer::OS_WIN10:
                    case MDPlayer::OS_WIN32:
                        if(!$damager->canInteract($entity, $max)){
                            $event->setCancelled(true);
                        }
                    break;

                    case MDPlayer::OS_IOS:
                        //Nothing
                    break;

                    default:
                        if(!$damager->canInteract($entity, $max)){
                            $event->setCancelled(true);
                        }
                    break;
                }
                /*
                Myriad::getInstance()->getLogger()->debug($damager->getName() . " Hits " . $entity->getName() . " with " . $damager->getAim($entity) . " of Aim and " . $damager->distance($entity) . " of Reach!");
                if($damager->getAim($entity) == 0.0){
                    $damager->addAimhack();
                    if($damager->getAimhack() == 10){
                        $damager->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] Aimbot.", false);
                        Myriad::getInstance()->getServer()->broadcastMessage(TextFormat::RED . TextFormat::BOLD . "[MDCheat]" . TextFormat::RESET . $damager->getName() . " got banned for using an Aimbot!");
                        $damager->setBanned(true);
                        return;
                    }
                } else {
                    $damager->setAimhack(0);
                }*/

                /*
                if($distance > 5){
                    $damager->addReach();
                    if($damager->getReach() == 3){
                        $damager->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] Reach.", false);
                        Myriad::getInstance()->getServer()->broadcastMessage(TextFormat::RED . TextFormat::BOLD . "[MDCheat]" . TextFormat::RESET . $damager->getName() . " got banned for using a Reach hack!");
                        $damager->setBanned(true);
                    }
                } else {
                    $damager->setReach(0);
                }*/

                if($damager->getLastclick() != 0){
                    $rn = microtime(true);
                    $last = $damager->getLastclick();

                    $time = $rn - $last;

                    if($time < 0.2){
                        $event->setCancelled(true);
                    } else {
                        $damager->setLastclick($rn);
                    }
                } else {
                    $damager->setLastclick(microtime(true));
                }
            }
        }
    }


    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onRecieve(DataPacketReceiveEvent $event) {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if($packet instanceof UpdateAttributesPacket){
            //$player->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] Game Editing.", false);
            $event->setCancelled(true);
            //$player->setBanned(true);
            return;
        }
        if($packet instanceof SetPlayerGameTypePacket){
            //$player->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] Game Editing.", false);
            //$player->setBanned(true);
            $event->setCancelled(true);
            return;
        }
        if($packet instanceof AdventureSettingsPacket){
            if(!$player->isCreative() && !$player->isSpectator() && !$player->isOp() && !$player->getAllowFlight()){
                switch ($packet->flags){
                    case 614:
                    case 615:
                    case 103:
                    case 102:
                    case 38:
                    case 39:
                    //$player->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] Flying/NoClip.", false);
                    //$player->setBanned(true);
                    $event->setCancelled(true);
                    return;
                        break;
                    default:
                        break;
                }
                if((($packet->flags >> 9) & 0x01 === 1) || (($packet->flags >> 7) & 0x01 === 1) || (($packet->flags >> 6) & 0x01 === 1)){
                    //$player->kick(TextFormat::RED . "You got banned from Myriad Network!" . PHP_EOL . "Reason: [MDCheat] Flying/NoClip.", false);
                    //$player->setBanned(true);
                    $event->setCancelled(true);
                    return;
                }
            }
        }
    }

}