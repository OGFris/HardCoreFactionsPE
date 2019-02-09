<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use friscowz\hc\Myriad;

class Knockback extends VanillaEnchant implements Listener{

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
	    if($event instanceof EntityDamageByEntityEvent){
		   $damager = $event->getDamager();
		   if(!$damager instanceof Player or $event->isCancelled()){
			  return;
			}
		  $item = $damager->getInventory()->getItemInHand();
		  if($item->hasEnchantment(Enchantment::KNOCKBACK)){
			 $level = $this->getEnchantmentLevel($item, Enchantment::KNOCKBACK);
		    $event->setKnockBack($event->getKnockBack() + 0.3 * $level);
			}
		}
	}
}
