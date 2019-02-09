<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 14:04
 */

namespace friscowz\hc\modules;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\BardTask;
use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;

class Sets implements Listener
{
    private $plugin;

    /**
     * Sets constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        new BardTask($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return mixed
     */
    public function getPlugin () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin ($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            if($event->getTo()->getFloorX() != $event->getFrom()->getFloorX() or $event->getTo()->getFloorZ() != $event->getTo()->getFloorZ()) {
                $player->checkSets();
            }
        }
    }

    /*
    public function onDamage(EntityDamageEvent $event)
    {
        if($event instanceof EntityDamageByEntityEvent and $event->getCause() == EntityDamageByEntityEvent::CAUSE_PROJECTILE and $player = $event->getEntity() instanceof MDPlayer and $damager = $event->getDamager() instanceof MDPlayer){
            if($player->getFaction() == $damager->getFaction()) return;
            if($event->isCancelled()) return;
            //TODO: ArcherTag
        }
    }*/

    /*Blaze Powder Held Strenght 1 (10 seconds) Tap Strenght 2 (5 Sec)
    Sugar Speed 1 held Speed. 2 tap
    Feather Jump boost 2 held Jump boost 4 tap
    Iron Ingot Resistance 1 held Resistance 3 tap
    Ghast Tear Regeneration 1 held Regeneration 3 tap*/

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if($player instanceof MDPlayer){
            if ($player->isBard()) {
                switch ($item->getId()) {
                    case ItemIds::SUGAR:
                        if($player->getBardEnergy() >= 20){
                            $player->addEffect(Effect::getEffect(Effect::SPEED)->setDuration(20 * 6)->setAmplifier(1));
                            $player->setBardEnergy($player->getBardEnergy() - 20);
                            $player->getInventory()->removeItem(Item::get($item->getId()));
                        }
                    break;

                    case ItemIds::FEATHER:
                        if($player->getBardEnergy() >= 20){
                            $player->addEffect(Effect::getEffect(Effect::JUMP)->setDuration(20 * 6)->setAmplifier(3));
                            $player->setBardEnergy($player->getBardEnergy() - 20);
                            $player->getInventory()->removeItem(Item::get($item->getId()));
                        }
                    break;

                    case ItemIds::IRON_INGOT:
                        if($player->getBardEnergy() >= 20){
                            $player->addEffect(Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setDuration(20 * 8)->setAmplifier(2));
                            $player->setBardEnergy($player->getBardEnergy() - 20);
                            $player->getInventory()->removeItem(Item::get($item->getId()));
                        }
                    break;

                    case ItemIds::GHAST_TEAR:
                        if($player->getBardEnergy() >= 30){
                            $player->addEffect(Effect::getEffect(Effect::REGENERATION)->setDuration(20 * 5)->setAmplifier(1));
                            $player->setBardEnergy($player->getBardEnergy() - 30);
                            $player->getInventory()->removeItem(Item::get($item->getId()));
                        }
                    break;

                    case ItemIds::BLAZE_POWDER:
                        if($player->getBardEnergy() >= 50){
                            $player->addEffect(Effect::getEffect(Effect::STRENGTH)->setDuration(20 * 4)->setAmplifier(1));
                            $player->setBardEnergy($player->getBardEnergy() - 40);
                            $player->getInventory()->removeItem(Item::get($item->getId()));
                        }
                    break;
                }
            }
        }
    }
}