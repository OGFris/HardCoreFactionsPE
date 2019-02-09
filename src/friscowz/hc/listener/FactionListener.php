<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:25
 */
namespace friscowz\hc\listener;

use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;


class FactionListener implements Listener {

    private $plugin;

    /**
     * FactionListener constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return mixed
     */
    public function getPlugin() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            if($player->isInFaction()){
                Myriad::getFactionsManager()->reduceDTR($player->getFaction());
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();
        if($player instanceof MDPlayer) {
            if ($player->isStaff()) return;
            switch ($block->getId()) {
                case BlockIds::FENCE_GATE:
                case BlockIds::ACACIA_FENCE_GATE:
                case BlockIds::BIRCH_FENCE_GATE:
                case BlockIds::DARK_OAK_FENCE_GATE:
                case BlockIds::SPRUCE_FENCE_GATE:
                case BlockIds::JUNGLE_FENCE_GATE:
                case BlockIds::IRON_TRAPDOOR:
                case BlockIds::WOODEN_TRAPDOOR:
                case BlockIds::TRAPDOOR:
                case BlockIds::OAK_FENCE_GATE:
                    if(Myriad::getFactionsManager()->isFactionClaim($block)) {
                        if (!$player->isInFaction()) {
                            $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                            $player->teleport($player->asVector3()->add(0, 0.1, 0));
                            $player->disableMovement(time() + 2);
                            $event->setCancelled(true);
                        } else {
                            if (Myriad::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {
                                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                                $event->setCancelled(true);
                                $player->teleport($player->asVector3()->add(0, 0.1, 0));
                                $player->disableMovement(time() + 2);
                            }
                        }
                    }
                break;

                case BlockIds::CHEST:
                case BlockIds::TRAPPED_CHEST:
                if(Myriad::getFactionsManager()->isFactionClaim($block)) {
                    if (!$player->isInFaction()) {
                        $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                        $event->setCancelled(true);
                    } else {
                        if (Myriad::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {
                            $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                            $event->setCancelled(true);
                        }
                    }
                }
                break;
            }
            switch($item->getId()){
                case ItemIds::BUCKET:
                case ItemIds::DIAMOND_HOE:
                case ItemIds::GOLD_HOE:
                case ItemIds::IRON_HOE:
                case ItemIds::STONE_HOE:
                case ItemIds::WOODEN_HOE:
                case ItemIds::DIAMOND_SHOVEL:
                case ItemIds::GOLD_SHOVEL:
                case ItemIds::IRON_SHOVEL:
                case ItemIds::STONE_SHOVEL:
                case ItemIds::WOODEN_SHOVEL:
                    if(Myriad::getFactionsManager()->isFactionClaim($block)) {
                        if (!$player->isInFaction()) {
                            $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                            $event->setCancelled(true);
                        } else {
                            if (Myriad::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()) {
                                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                                $event->setCancelled(true);
                            }
                        }
                    } elseif (Myriad::getFactionsManager()->isClaim($block)){
                        if(!$player->isOp()){
                            $event->setCancelled(true);
                        }
                    }
                break;
            }
        }
    }


    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer) {
            if ($player->isMovementDisabled()) {
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if(Myriad::getFactionsManager()->isFactionClaim($block)){
            if($player instanceof MDPlayer) {
                if ($player->isStaff()) return;
                if(!$player->isInFaction()){
                    $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't break blocks on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                    $event->setCancelled(true);
                    $player->teleport($player->asVector3()->add(0, 0.1, 0));
                    $player->disableMovement(time() + 2);
                } else {
                    if (Myriad::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()){
                        $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't break blocks on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                        $event->setCancelled(true);
                        $player->teleport($player->asVector3()->add(0, 0.1, 0));
                        $player->disableMovement(time() + 2);
                    }
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if(Myriad::getFactionsManager()->isFactionClaim($block)){
            if($player instanceof MDPlayer) {
                if ($player->isStaff()) return;
                if(!$player->isInFaction()){
                    $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't place blocks on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                    $event->setCancelled(true);
                    $player->disableMovement(time() + 2);
                } else {
                    if (Myriad::getFactionsManager()->getClaimer($block->x, $block->z) != $player->getFaction()){
                        $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't place blocks on " . Myriad::getFactionsManager()->getClaimer($block->x, $block->z) . "'s claim!");
                        $event->setCancelled(true);
                        $player->disableMovement(time() + 2);
                    }
                }
            }
        }
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent){
            $damager = $event->getDamager();
            if($entity instanceof MDPlayer) {
                if ($damager instanceof MDPlayer) {
                    if($damager->isInFaction() and $entity->isInFaction()) {
                        if ($damager->getFaction() == $entity->getFaction()) {
                            $event->setCancelled(true);
                            $damager->sendMessage(TextFormat::YELLOW . "You can not hurt " . TextFormat::GREEN . $entity->getName());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event)
    {
        if(!$event->isCancelled()){
            $player = $event->getPlayer();
            if($player instanceof MDPlayer){
                if($player->getChat() == MDPlayer::FACTION){
                    if($player->isInFaction()) {
                        $event->setCancelled(true);
                        foreach (Myriad::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
                            if($member instanceof MDPlayer){
                                $member->sendMessage(Utils::getPrefix() . TextFormat::DARK_GREEN . $player->getName() . ": " . TextFormat::GREEN . $event->getMessage());
                            }
                        }
                    } else {
                        $player->setChat(MDPlayer::PUBLIC);
                    }
                }
            }
        }
    }

}
