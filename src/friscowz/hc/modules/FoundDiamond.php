<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 15/08/2017
 * Time: 19:44
 */

namespace friscowz\hc\modules;


use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class FoundDiamond implements Listener
{
    private $plugin;
    private $blocks = [];

    public function __construct(Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return Myriad
     */
    public function getPlugin() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin(Myriad $plugin)
    {
        $this->plugin=$plugin;
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if($event->isCancelled()){
            return;
        }

        if($block->getId() == BlockIds::DIAMOND_ORE) {
            if (!isset($this->blocks[Utils::vector3AsString($block->asVector3())])) {
                $count = 0;
                for ($x = $block->getX() - 4; $x <= $block->getX() + 4; $x++) {
                    for ($z = $block->getZ() - 4; $z <= $block->getZ() + 4; $z++) {
                        for ($y = $block->getY() - 4; $y <= $block->getY() + 4; $y++) {
                            if ($player->getLevel()->getBlockIdAt($x, $y, $z) == BlockIds::DIAMOND_ORE) {
                                if(!isset($this->blocks[Utils::vector3AsString(new Vector3($x, $y, $z))])) {
                                    $this->blocks[Utils::vector3AsString(new Vector3($x, $y, $z))] = true;
                                    ++$count;
                                }
                            }
                        }
                    }
                }
                $this->getPlugin()->getServer()->broadcastMessage(TextFormat::AQUA . "[FD]" . TextFormat::WHITE . $player->getName() . " Found " . $count . " Diamonds.");
            }
        }

        /*
        if(!isset($this->last[$player->getName()]) ){
            $this->last[$player->getName()] = new Vector3(0, 0, 0);
        }

        if ($block->getId() == BlockIds::DIAMOND_ORE) {
            if ($block->distanceSquared($this->last[$player->getName()]) > 5) {
                for ($x = $block->getX() - 3; $x <= $block->getX() + 3; $x++) {
                    for ($z = $block->getZ() - 3; $z <= $block->getZ() + 3; $z++) {
                        for ($y = $block->getY() - 3; $y <= $block->getY() + 3; $y++) {
                            if ($player->getLevel()->getBlockIdAt($x, $y, $z) == BlockIds::DIAMOND_ORE) {
                                ++$count;
                            }
                        }
                    }
                }
                $this->last[$player->getName()] = $block;
                $this->getPlugin()->getServer()->broadcastMessage(TextFormat::AQUA . "[FD]" . TextFormat::WHITE . $player->getName() . " Found " . $count . " Diamonds.");
            }
        }*/
    }
}