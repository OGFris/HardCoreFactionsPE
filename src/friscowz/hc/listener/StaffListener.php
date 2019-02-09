<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/24/2017
 * Time: 1:40 AM
 */

namespace friscowz\hc\listener;

use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class StaffListener implements Listener
{

    /**
     * StaffListener constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInterct(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if($player instanceof MDPlayer){
            if($player->isStaff()){
                if($item->getId() == ItemIds::DYE){
                    switch ($item->getDamage()){
                        case 10:
                            $new = Item::get(ItemIds::DYE, 1, 1)->setCustomName(TextFormat::RESET . TextFormat::RED . "Unvanish");
                            $player->getInventory()->setItemInHand($new);
                            $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully Vanished yourself !");
                            $player->setVanish(true);
                            $player->checkVanish();
                        break;

                        case 1:
                            $new = Item::get(ItemIds::DYE, 10, 1)->setCustomName(TextFormat::RESET . TextFormat::GREEN . "Vanish");
                            $player->getInventory()->setItemInHand($new);
                            $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully Unvanished yourself !");
                            $player->setVanish(false);
                            $player->checkVanish();
                        break;
                    }
                } else
                    if($item->getId() == ItemIds::COMPASS) {
                        $closest = $player;
                        $lastSquare = - 1;
                        foreach($player->getLevel()->getPlayers() as $p) {
                            if ($p !== $player) {
                                $x = $p->x - $player->x;
                                $z = $p->z - $player->z;
                                $square = abs($x) + abs($z);
                                if ($lastSquare === - 1 or $lastSquare > $square) {
                                    $closest = $p;
                                    $lastSquare = round($square);
                                }
                            }
                        }
                        $player->teleport($closest);
                    } else
                        if($item->getId() == ItemIds::CLOCK) {
                            $array = [];
                            foreach ($player->getServer()->getOnlinePlayers() as $p){
                                $array[] = $p;
                            }
                            $count = count($array);
                            $random = $array[mt_rand(0, $count - 1)];
                            $player->teleport($random);
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
        if($player instanceof MDPlayer){
            if($player->isFreeze()){
                $event->setCancelled(true);
            }
            if($player->isStaff()){
                $event->setCancelled(true);
            }
            if($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof MDPlayer) {
                    if ($damager->isStaff()) {
                        if ($damager->getInventory()->getItemInHand()->getId() == ItemIds::FROSTED_ICE) {
                            $event->setCancelled(true);
                            if ($player->isFreeze()) {
                                $player->setFreeze(false);
                                $player->sendMessage(TextFormat::RED . "You got freezed by an admin. " . TextFormat::BOLD . "DON'T QUIT/RELOG OR YOU'LL GET BANNED!" . TextFormat::RESET);
                                $damager->sendMessage(TextFormat::GREEN . "You have successfully freezed " . $player->getName() . ".");
                            } else {
                                $damager->sendMessage(TextFormat::GREEN . "You have successfully unfreezed " . $player->getName() . ".");
                                $player->sendMessage(TextFormat::GREEN . "You have been unfreezed by an admin!");
                                $player->setFreeze(true);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            if($player->isFreeze()){
                $player->sendPopup(TextFormat::RED . "You can't move while you're freezed!");
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            foreach ($player->getServer()->getOnlinePlayers() as $p){
                if($p instanceof MDPlayer){
                    if($p->isStaff()){
                        $p->checkVanish();
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
            if($player->isStaff()){
                $player->removeStaff();
            }
            foreach ($player->getServer()->getOnlinePlayers() as $p){
                if($p instanceof MDPlayer){
                    if($p->isStaff()){
                        $p->checkVanish();
                    }
                }
            }
        }
    }
}