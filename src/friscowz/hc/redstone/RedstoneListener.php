<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/17/2017
 * Time: 1:11 AM
 */

namespace friscowz\hc\redstone;


use pocketmine\block\IronDoor;
use pocketmine\block\StonePressurePlate;
use pocketmine\block\WoodenDoor;
use pocketmine\block\WoodenPressurePlate;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\Item;

class RedstoneListener implements Listener
{

    /**
     * @param PlayerMoveEvent $event
     */
    /*
    public function onMove(PlayerMoveEvent $event){
        if($event->getTo()->getFloorX() != $event->getFrom()->getFloorX() or $event->getTo()->getFloorZ() != $event->getFrom()->getFloorZ()){
            $player = $event->getPlayer();
            $block = $event->getPlayer()->getLevel()->getBlockAt($player->getFloorX(), $player->getFloorY(), $player->getFloorZ());
            if($block instanceof StonePressurePlate){
                $block->setDamage(1);
                for($x = $block->getX() - 1; $x < ($x + 2); ++$x){
                    for ($z = $block->getZ() - 1; $z < ($z + 2); ++$z){
                        for ($y = $block->getY() -1; $y < ($y + 2); ++$y){
                            $test = $block->getLevel()->getBlockAt($x, $y, $z);
                            if($test instanceof IronDoor){
                                $test->onActivate(Item::get(Item::AIR), $player);
                                break;
                            }
                        }
                    }
                }
            } else
                if($block instanceof WoodenPressurePlate){
                    $block->setDamage(1);
                    for($x = $block->getX() - 1; $x < ($x + 2); ++$x){
                        for ($z = $block->getZ() - 1; $z < ($z + 2); ++$z){
                            for ($y = $block->getY() -1; $y < ($y + 2); ++$y){
                                $test = $block->getLevel()->getBlockAt($x, $y, $z);
                                if($block instanceof WoodenDoor){
                                    $block->onActivate(Item::get(Item::AIR), $player);
                                    break;
                                }
                            }
                        }
                    }
                }
        }
    }*/
}