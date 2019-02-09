<?php

/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 02:29
 */
namespace friscowz\hc\end;

use friscowz\hc\Myriad;
use pocketmine\block\{
    Air, Block
};
use pocketmine\event\block\{
    BlockBreakEvent, BlockPlaceEvent
};
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\{
    Item, ItemIds
};
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class Portal implements Listener
{
    private $plugin;

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
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $block = $event->getBlock();
        $spawn = new Vector3(0, 50, 0);
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($event->isCancelled()){
            return;
        }
        if($item->getId() == ItemIds::DIAMOND_HOE and $item->getName() == TextFormat::RESET . TextFormat::GOLD . "Crowbar"){
            if($spawn->distance($block) < 200 and !$player->isOp()){
                $player->sendMessage(TextFormat::RED . "You can't build near the spawn!");
                $event->setCancelled(true);
                return;
            }
            if($block->getId() == 120){
                $player = $event->getPlayer();
                $player->getInventory()->setItemInHand(new Item(0, 0, 0));
                $block->getLevel()->dropItem($block, new Item($block->getId(), 0, 1));
                $block->getLevel()->addParticle(new DestroyBlockParticle($block, $block));
                for($x = $block->getX()-4; $x <= $block->getX()+4; $x++){
                    for($z = $block->getZ()-4; $z <= $block->getZ()+4; $z++){
                        if($block->getLevel()->getBlockIdAt($x, $block->getY(), $z) == 120 || $block->getLevel()->getBlockIdAt($x, $block->getY(), $z) == 119){
                            $block->getLevel()->setBlock(new Vector3($x, $block->getY(), $z), new Air());
                            $block->getLevel()->addParticle(new DestroyBlockParticle($block, $block));
                        }
                    }
                }
            }
        }

        if($player->isOp() and $player->isCreative()){
            if($block->getId() == 120 and $item->getId() == ItemIds::DIAMOND_HOE){
                $player = $event->getPlayer();
                $block->getLevel()->dropItem($block, new Item($block->getId(), 0, 1));
                $block->getLevel()->addParticle(new DestroyBlockParticle($block, $block));
                for($x = $block->getX()-4; $x <= $block->getX()+4; $x++){
                    for($z = $block->getZ()-4; $z <= $block->getZ()+4; $z++){
                        if($block->getLevel()->getBlockIdAt($x, $block->getY(), $z) == 120 || $block->getLevel()->getBlockIdAt($x, $block->getY(), $z) == 119){
                            $block->getLevel()->setBlock(new Vector3($x, $block->getY(), $z), new Air());
                            $block->getLevel()->addParticle(new DestroyBlockParticle($block, $block));
                        }
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
        $block = $event->getBlock();
        $level = $block->getLevel();
        $spawn = new Vector3(0, 50, 0);
        $player = $event->getPlayer();
        if($event->isCancelled()){
            return;
        }
        if($block->getId() == 120){
            if($spawn->distance($block) < 500 and !$player->isOp()){
                $player->sendMessage(TextFormat::RED . "You can't build near the spawn!");
                $event->setCancelled(true);
                return;
            }
            $event->setCancelled(true);
            $player->getInventory()->remove(new Item(120, 0, 1));
            //END_PORTAL
            $level->setBlock($block, Block::get(119));
            $level->setBlock($block->add(1, 0, 0), Block::get(119));
            $level->setBlock($block->add(-1, 0, 0), Block::get(119));
            $level->setBlock($block->add(1, 0, 1), Block::get(119));
            $level->setBlock($block->add(-1, 0, 1), Block::get(119));
            $level->setBlock($block->add(1, 0, -1), Block::get(119));
            $level->setBlock($block->add(-1, 0, -1), Block::get(119));
            $level->setBlock($block->add(0, 0, 1), Block::get(119));
            $level->setBlock($block->add(0, 0, -1), Block::get(119));
            $level->setBlock($block, Block::get(119));
            //END_PORTAL_FRAME
            $level->setBlock($block->add(2, 0, -1), Block::get(120));
            $level->setBlock($block->add(2, 0, 0), Block::get(120));
            $level->setBlock($block->add(2, 0, 1), Block::get(120));
            $level->setBlock($block->add(-2, 0, -1), Block::get(120));
            $level->setBlock($block->add(-2, 0, 0), Block::get(120));
            $level->setBlock($block->add(-2, 0, 1), Block::get(120));
            $level->setBlock($block->add(-1, 0, 2), Block::get(120));
            $level->setBlock($block->add(0, 0, 2), Block::get(120));
            $level->setBlock($block->add(1, 0, 2), Block::get(120));
            $level->setBlock($block->add(-1, 0, -2), Block::get(120));
            $level->setBlock($block->add(0, 0, -2), Block::get(120));
            $level->setBlock($block->add(1, 0, -2), Block::get(120));
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event)
    {
        switch($event->getBlock()->getId()){
            case 119:
            case 120:
                $event->setCancelled(true);
            break;
        }
    }
}