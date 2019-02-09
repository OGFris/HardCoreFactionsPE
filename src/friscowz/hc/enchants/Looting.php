<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use friscowz\hc\Myriad;

class Looting extends VanillaEnchant implements Listener{

    public function __construct(Myriad $plugin){
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }
	
	/*
	 * @void onDeath
	 * @param EntityDeathEvent $event
	 * @priority LOWESR
	 * ignoreCancelled true
	 */
	
	public function onDeath(EntityDeathEvent $event): void{
		  $player = $event->getEntity();
		  if($player instanceof Player){
			 return;
	     }
		  $cause = $player->getLastDamageCause();
		  if($cause instanceof EntityDamageByEntityEvent){
			 $damager = $cause->getDamager();
			 if(!$damager instanceof Player){
				return;
			 }else{
			   $item = $damager->getInventory()->getItemInHand();
			 }
		  }else{
		   return;
		  }
		  if($item->hasEnchantment(Enchantment::LOOTING)){
			 $drops = [];
		    foreach($event->getDrops() as $drop){
			   $rand = rand(1, $this->getEnchantmentLevel($item, Enchantment::LOOTING) + 1);
		      $drop->setCount($drop->getCount() + $rand);
		      $drops[] = $drop;
		    }
		    $event->setDrops($drops);
		}
	}
}