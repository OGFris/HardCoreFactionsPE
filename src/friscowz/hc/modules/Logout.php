<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 23/08/2017
 * Time: 03:22
 */

namespace friscowz\hc\modules;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\ZombieTask;
use friscowz\hc\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Zombie;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;

class Logout implements Listener
{
    private $plugin;
    private $zombies = [];

    /**
     * Logout constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return Myriad
     */
    public function getPlugin(): Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin(Myriad $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return array
     */
    public function getZombies(): array
    {
        return $this->zombies;
    }

    /**
     * @param string $zombie
     * @return bool
     */
    public function isZombie(string $zombie) : bool
    {
        return isset($this->zombies[$zombie]);
    }


    /**
     * @param MDPlayer $player
     */
    public function spawnZombie(MDPlayer $player)
    {
        $nbt = new CompoundTag("", ["Pos" => new ListTag("Pos", [new DoubleTag("", $player->x), new DoubleTag("", $player->y + 1), new DoubleTag("", $player->z)]), "Motion" => new ListTag("Motion", [new DoubleTag("", 0), new DoubleTag("", 0), new DoubleTag("", 0)]), "Rotation" => new ListTag("Rotation", [new FloatTag("", mt_rand() / mt_getrandmax() * 360), new FloatTag("", 0)]),]);
        $entity = Entity::createEntity("Zombie", $player->getLevel(), $nbt);
        $entity->setMaxHealth(20);
        $entity->setHealth(20);
        $entity->setNameTagVisible(true);
        $entity->setNameTagAlwaysVisible(true);
        $entity->setNameTag($player->getName());
        $entity->spawnToAll();
        new ZombieTask($this->getPlugin(), $entity, $player);
        if($player->isInFaction()){
            $this->zombies[$player->getName()] = ["inv" => $player->getInventory()->getContents(), "faction" => $player->getFaction()];
        } else $this->zombies[$player->getName()] = ["inv" => $player->getInventory()->getContents(), "faction" => "hfnsidhajjfhfnwjdnfwjdcigjej"];
    }
    /**
     * @param string $name
     * @return array
     */
    public function getDrops(string $name) : array
    {
        return $this->getZombies()[$name]["inv"];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getFaction(string $name) : string
    {
        return $this->getZombies()[$name]["faction"];
    }

    /**
     * @param string $name
     */
    public function removeZombie(string $name)
    {
        unset($this->zombies[$name]);
    }

    /**
     * @param EntityDeathEvent $event
     */
    public function onEntityDeath(EntityDeathEvent $event)
    {
        $entity = $event->getEntity();
        if($entity instanceof Zombie){
            $name = $entity->getNameTag();
            if($this->isZombie($name)){
                $this->removeZombie($name);
                $event->setDrops($this->getDrops($name));
                $this->getPlugin()->getServer()->broadcastMessage(Utils::getPrefix() . TextFormat::RED . $name . " got killed!");
            }
        }
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if($entity instanceof Zombie and $event instanceof EntityDamageByEntityEvent and $this->isZombie($entity->getNameTag())){
            if($player = $event->getDamager() instanceof MDPlayer){
                if($player->getFaction() == $this->getFaction($entity->getNameTag())){
                    $event->setCancelled(true);
                }
            }
        }
    }

}