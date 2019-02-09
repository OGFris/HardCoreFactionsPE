<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 03/11/2017
 * Time: 02:01
 */

namespace friscowz\hc\crate;


use friscowz\hc\Myriad;
use pocketmine\block\Chest;
use pocketmine\block\TripwireHook;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;

class CrateListener implements Listener
{
    private $plugin;

    /**
     * CrateListener constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if($block instanceof Chest and $block->distance(new Vector3(0, 66, 0)) < 40){
            $event->setCancelled(true);
            Myriad::getCrate()->openCreate($player, $block);
        }
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
}