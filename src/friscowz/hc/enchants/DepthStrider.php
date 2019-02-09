<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\enchantment\Enchantment;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

use friscowz\hc\Myriad;

class DepthStrider extends VanillaEnchant implements Listener{

    public function __construct(Myriad $plugin){
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }
	
	/*
	 * @void onMove
	 * @param PlayerMoveEvent $event
	 * @priority HIGHEST
	 * ignoreCancelled false
	 */
	
	public function onMove(PlayerMoveEvent $event): void{
	    $player = $event->getPlayer();
	    if($event->isCancelled()){
		   return;
		 }
	    if($player instanceof Player){ # Not sure why :]
		   $level = $this->getEnchantmentLevelOfArmors($player, Enchantment::DEPTH_STRIDER);
		   $attribute = $player->getAttributeMap()->getAttribute(5);
		   $block = $player->getLevel()->getBlock($player);
		   if($level > 0){
			  if(in_array($block->getId(), [8, 9])){
				 $attribute->setValue($attribute->getDefaultValue() + ($attribute->getDefaultValue() * (1/3) * $level), true, true);
			  }else{
			     if($attribute->getValue() == $attribute->getDefaultValue() + ($attribute->getDefaultValue() * (1/3) * $level)){
				    $attribute->setValue($attribute->getDefaultValue(), true, true);
				  }
			   }
		   }elseif($level <= 0){
		     if($attribute->getValue() == $attribute->getDefaultValue() + ($attribute->getDefaultValue() * (1/3) * $level)){
				 $attribute->setValue($attribute->getDefaultValue(), true, true);
				}
		   }
		}
	}
}
