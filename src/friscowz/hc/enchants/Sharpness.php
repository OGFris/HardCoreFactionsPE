<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use friscowz\hc\Myriad;

class Sharpness extends VanillaEnchant implements Listener{

    public function __construct(Myriad $plugin){
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }
	
	/*
	 * @void onDamage
	 * @param EntityDamageEvent $event
	 * @priority HIGH
	 * ignoreCancelled true
	 */
	
	public function onDamage(EntityDamageEvent $event): void{
	    $player = $event->getEntity();
	    if($event instanceof EntityDamageByEntityEvent){
		   $damager = $event->getDamager();
		   if(!$damager instanceof Player){
			  return;
			}
		  $item = $damager->getInventory()->getItemInHand();
		  if($item->hasEnchantment(Enchantment::SHARPNESS)){
			 $level = $this->getEnchantmentLevel($item, Enchantment::SHARPNESS);
		    $base = $event->getDamage();
		    $add = $this->getExtraDamage(Enchantment::SHARPNESS, $base, $level);
		    $event->setDamage($base + $add);
			}
		}
	}
}
