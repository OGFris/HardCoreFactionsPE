<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 28/10/2017
 * Time: 00:53
 */

namespace friscowz\hc\listener;

use friscowz\hc\FactionsManager;
use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class ClaimListener implements Listener
{
    /**
     * ClaimListener constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if($player instanceof MDPlayer){
            if($player->isClaiming() and $player->isInFaction()){
                if($block->distance($block->getLevel()->getSpawnLocation()) < 300){
                    $event->setCancelled(true);
                    $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't claim near the spawn");
                    return;
                }
                switch($player->getStep()){
                    case MDPlayer::FIRST:
                        $player->setPos1($block);
                        $player->buildWall($block->x, $block->y, $block->z);
                        $player->sendMessage(Utils::getPrefix() . "You have successfully set the first position! now please set the second one.");
                        $player->setStep(MDPlayer::SECOND);
                    break;

                    case MDPlayer::SECOND:
                        if($player->getPos1()->distance($block) > 4) {
                            if($player->getPos1()->distance($block) < 100) {
                                $player->setPos2($block);
                                $player->buildWall($block->x, $block->y, $block->z);
                                $player->sendMessage(Utils::getPrefix() . "You have successfully set the second position! checking your claim...");
                                $player->checkClaim();
                            } else $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You claim is too big !");
                        } else $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "the claim must be at least 4x4 !");
                    break;

                    case MDPlayer::CONFIRM:
                        if($player->isSneaking()){
                            if(Myriad::getFactionsManager()->getBalance($player->getFaction()) >= $player->getClaimCost()){
                                Myriad::getFactionsManager()->reduceBalance($player->getFaction(), $player->getClaimCost());

                                $x1 = $player->getPos1()->getFloorX();
                                $z1 = $player->getPos1()->getFloorZ();
                                $x2 = $player->getPos2()->getFloorX();
                                $z2 = $player->getPos2()->getFloorZ();

                                $player->setStep(MDPlayer::FIRST);
                                $player->setClaiming(false);
                                $player->setClaim(false);

                                Myriad::getFactionsManager()->claim($player->getFaction(), $player->getPos1(), $player->getPos2(), FactionsManager::CLAIM);
                                $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed the land!");
                            } else {
                                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your faction's balance doesn't have enough of money to claim!");
                            }
                        }
                    break;
                }
            }
        }
    }


    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $message = strtolower($event->getMessage());

        if($message == "cancel" || $message == "'cancel'"){
            if($player instanceof MDPlayer) {
                $event->setCancelled(true);
                $player->setStep(MDPlayer::FIRST);
                $player->setClaiming(false);
                $player->setClaim(false);
                $player->sendMessage(Utils::getPrefix() . TextFormat::YELLOW . "Cancelled the claiming process.");
            }
        }
    }

}