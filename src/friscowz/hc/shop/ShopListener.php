<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 02/11/2017
 * Time: 12:11
 */

namespace friscowz\hc\shop;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use friscowz\hc\Myriad;
use pocketmine\block\BlockIds;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\Player;
use friscowz\hc\MDPlayer;
use pocketmine\utils\TextFormat;
use friscowz\hc\utils\Utils;
use pocketmine\item\Item;
use pocketmine\event\player\PlayerInteractEvent;

class ShopListener implements Listener
{
    /**
     * ShopListener constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
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

        if($player instanceof MDPlayer) {
            if ($block->getId() == BlockIds::SIGN_POST || $block->getId() == BlockIds::WALL_SIGN) {
                $vector3 = new Vector3($block->getX(), $block->getY(), $block->getZ());
                if($player->getGamemode() == Player::CREATIVE){
                    $player->setSign($vector3);
                    return;
                }
                if (Myriad::getShop()->isSign($vector3)) {
                    switch (Myriad::getShop()->getType($vector3)) {
                        case Shop::SELL:
                            if ($player->getInventory()->contains($item = Item::get(Myriad::getShop()->getId($vector3), Myriad::getShop()->getDamage($vector3), Myriad::getShop()->getAmount($vector3)))) {
                                $player->getInventory()->removeItem($item);
                                $player->addMoney(Myriad::getShop()->getPrice($vector3));
                                $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully sold " . TextFormat::GRAY . $item->getName() . "x" . Myriad::getShop()->getAmount($vector3) . TextFormat::GREEN . " For" . TextFormat::GRAY . Myriad::getShop()->getPrice($vector3) . TextFormat::GREEN . ".");
                            } else $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have that item!");
                            break;

                        case Shop::BUY:
                            if($player->getMoney() >= Myriad::getShop()->getPrice($vector3)){
                                $item = new Item(Myriad::getShop()->getId($vector3), Myriad::getShop()->getDamage($vector3), Myriad::getShop()->getAmount($vector3));
                                /*
                                if(Myriad::getShop()->isCustomName($vector3)){
                                    $item->setCustomName(Myriad::getShop()->getName($vector3));
                                }*/
                                $player->getInventory()->addItem($item);
                                $player->reduceMoney(Myriad::getShop()->getPrice($vector3));
                                $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully purchased " . TextFormat::GRAY . $item->getName() . "x" . Myriad::getShop()->getAmount($vector3) . TextFormat::GREEN . " For" . TextFormat::GRAY . Myriad::getShop()->getPrice($vector3) . TextFormat::GREEN . ".");
                            } else $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have enough of money!");
                            break;
                    }
                }
            }
        }
    }


    /**
     * @param SignChangeEvent $event
     */
    public function onSignChange(SignChangeEvent $event)
    {
        $player = $event->getPlayer();
        if($player->isOp()){
            $sign = $event->getBlock();
            $item = ItemFactory::fromString($event->getLine(1));
            if(strtolower($event->getLine(0)) == "-buy-"){
                Myriad::getShop()->createSign(Shop::BUY, $item->getId(), $item->getDamage(), intval($event->getLine(2)), intval($event->getLine(3)), $sign->asVector3());
            } else
                if(strtolower($event->getLine(0)) == "-sell-"){
                    Myriad::getShop()->createSign(Shop::SELL, $item->getId(), $item->getDamage(), intval($event->getLine(2)), intval($event->getLine(3)), $sign->asVector3());
                }
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onSignBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if($player->isOp()){
            if(Myriad::getShop()->isSign($block->asVector3())){
                Myriad::getShop()->deleteSign($block->asVector3());
                $player->sendMessage(TextFormat::GREEN . "Successfully removed the Sign shop!");
            }
        }
    }

}