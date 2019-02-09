<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 14/08/2017
 * Time: 19:11
 */

namespace friscowz\hc\modules;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\SpawnTagTask;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\utils\TextFormat;

class SpawnTag implements Listener
{
    private $plugin;

    private $maxX, $minX, $maxZ, $minZ;

    /**
     * SpawnTag constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
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
     * @param $player
     */
    public function buildSpawnBorder($player)
    {
        $y = 66;

        for($x = $this->minX; $x <= $this->maxX; ++$x){
            for($z = $this->minZ; $z <= $this->maxZ; ++$z) {
                $pk = new UpdateBlockPacket();
                $pk->blockId = 95;
                $pk->blockData = 14;
                $pk->x = (int)$x;
                $pk->y = (int)$y;
                $pk->z = (int)$z;
                $pk->flags = UpdateBlockPacket::FLAG_NONE;
                $player->dataPacket($pk);

                $pk = new UpdateBlockPacket();
                $pk->blockId = 95;
                $pk->blockData = 14;
                $pk->x = (int)$x;
                $pk->y = (int)$y + 1;
                $pk->z = (int)$z;
                $pk->flags = UpdateBlockPacket::FLAG_NONE;
                $player->dataPacket($pk);

                $pk = new UpdateBlockPacket();
                $pk->blockId = 95;
                $pk->blockData = 14;
                $pk->x = (int)$x;
                $pk->y = (int)$y + 2;
                $pk->z = (int)$z;
                $pk->flags = UpdateBlockPacket::FLAG_NONE;
                $player->dataPacket($pk);
            }
        }
    }

    /**
     * @param int $x
     * @param int $z
     * @return bool
     */
    public function isSpawnBorder(int $x, int $z) : bool
    {
        return $x >= $this->minX and $x <= $this->maxX and $z >= $this->minZ and $z <= $this->maxZ;
    }


    /**
     * @param $x
     * @param $z
     * @return bool
     */
    public function insideSpawn($x, $z) : bool
    {
        return $x > $this->minX and $x < $this->maxX and $z > $this->minZ and $z < $this->maxZ;
    }

    /**
     *
     */
    public function loadSpawn()
    {

        $spawn = $this->getSpawn();
        if(is_array($spawn)) {
            if (isset($spawn["x1"])) {
                $this->maxX = $spawn["x1"];
            }
            if (isset($spawn["z1"])) {
                $this->maxZ = $spawn["z1"];
            }

            if (isset($spawn["x2"])) {
                $this->minX = $spawn["x2"];
            }
            if (isset($spawn["z2"])) {
                $this->minZ = $spawn["z2"];
            }
        }
    }

    /**
     *
     */
    public function getSpawn() : string
    {
        $result = Myriad::getFactionsManager()->getDb()->query("SELECT * FROM claims WHERE faction = 'Spawn';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return $array;
    }

    /**
     * @param DataPacketSendEvent $event
     */
    public function onPacketSend(DataPacketSendEvent $event)
    {
        $packet = $event->getPacket();

        if($packet instanceof UpdateBlockPacket){
            if($packet->blockId == 0){
                if($this->isSpawnBorder($packet->x, $packet->z)){
                    $event->setCancelled(true);
                }
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent) {
            if (!$event->isCancelled()) {
                $damager = $event->getDamager();
                if($player instanceof MDPlayer) {
                    if (!$player->isTagged()) {
                        $player->setTagged(true);
                        new SpawnTagTask($this->getPlugin(), $player);
                    } else {
                        $player->setTagtime(30);
                    }
                }
                if($damager instanceof MDPlayer){
                    if(!$damager->isTagged()) {
                        $damager->setTagged(true);
                        new SpawnTagTask($this->getPlugin(), $damager);
                    } else {
                        $damager->setTagtime(30);
                    }
                }
            }
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            if($player->isTagged()){
                $player->kill();
                $player->setTagtime(0);
                $player->setTagged(false);
                $player->getInventory()->clearAll();
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            $player->setTagged(false);
            $player->setTagtime(0);
        }
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            if($player->isTagged()){
                if(Myriad::getFactionsManager()->isSpawnClaim($event->getTo())){
                    $event->setCancelled(true);
                    $player->teleport($player->getLast());
                    $player->sendPopup(TextFormat::BOLD . TextFormat::RED . "You can't walk into spawn in CombatTag!" . TextFormat::RESET);
                }
            }
        }
    }

}