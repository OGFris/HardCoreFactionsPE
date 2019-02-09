<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:26
 */
namespace friscowz\hc\modules;

use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use friscowz\hc\utils\Utils;

class PVPTimer implements Listener
{
    private $plugin;

    /**
     * PVPTimer constructor.
     * @param Myriad $plugin
     */
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
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        if($player instanceof MDPlayer and $event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if($damager instanceof MDPlayer) {
                if ($damager->isPvp()) {
                    $event->setCancelled(true);
                    $damager->sendMessage(TextFormat::RED . "You are in PvPTimer !");
                    return;
                }
                if ($player->isPvp()) {
                    $event->setCancelled(true);
                    $damager->sendMessage(TextFormat::RED . "This player have PvP Disabled for " . Utils::intToString($player->getPvptime()) . " time left!");
                }
            }
        }
    }
}