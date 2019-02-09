<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;

use friscowz\hc\Myriad;

class Protection extends VanillaEnchant implements Listener{

    public function __construct(Myriad $plugin){
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }
	
	/*
	 * @void onDamage
	 * @param EntityDamageEvent $event
	 * @priority HIGHEST
	 * ignoreCancelled false
	 */
	
	public function onDamage(EntityDamageEvent $event): void{
	    $player = $event->getEntity();
	    $cause = $event->getCause();
	    if($event->isCancelled() or $cause == $event::CAUSE_STARVATION or $cause == $event::CAUSE_MAGIC){
		   return;
		 }
       if($player instanceof Player) $this->useArmors($player);
	    if($player instanceof Player){
		   $level = $this->getEnchantmentLevelOfArmors($player, Enchantment::PROTECTION);
		   $base = $event->getDamage();
		   $reduce = $this->getReducedDamage(Enchantment::PROTECTION, $base, $level);
		   if($reduce > 0){
			  $event->setDamage($base - $reduce);
         }
		}
	}
}
