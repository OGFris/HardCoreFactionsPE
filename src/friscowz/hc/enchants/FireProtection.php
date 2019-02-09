<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;

use friscowz\hc\Myriad;

class FireProtection extends VanillaEnchant implements Listener{

    public function __construct(Myriad $plugin){
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }
	
	/*
	 * @void onDamage
	 * @param EntityDamageEvent $event
	 * @priority MEDIUM
	 * ignoreCancelled false
	 */

	public function onDamage(EntityDamageEvent $event): void{
	    $player = $event->getEntity();
	    $cause = $event->getCause();
	    if($event->isCancelled() and $cause !== $event::CAUSE_FIRE and $cause !== $event::CAUSE_FIRE_TICK and $cause !== $event::CAUSE_LAVA){
		   return;
        }

	    if($player instanceof Player){
		   $level = $this->getEnchantmentLevelOfArmors($player, Enchantment::FIRE_PROTECTION);
		   $base = $event->getDamage();
		   $reduce = $this->getReducedDamage(Enchantment::FIRE_PROTECTION, $base, $level);
		   if($reduce > 0){
               if($cause != $event::CAUSE_LAVA){
                   $event->setDamage($base - $reduce);
               }
			}
		}
	}
}
