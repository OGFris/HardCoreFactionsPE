<?php
namespace friscowz\hc\enchants;

use pocketmine\Player;

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;

class VanillaEnchant{

    const TYPE_INVALID = -1;
    const TYPE_ARMOR_PROTECTION = 0;
    const TYPE_ARMOR_FIRE_PROTECTION = 1;
    const TYPE_ARMOR_FALL_PROTECTION = 2;
    const TYPE_ARMOR_EXPLOSION_PROTECTION = 3;
    const TYPE_ARMOR_PROJECTILE_PROTECTION = 4;
    const TYPE_ARMOR_THORNS = 5;
    const TYPE_WATER_BREATHING = 6;
    const TYPE_WATER_SPEED = 7;
    const TYPE_WATER_AFFINITY = 8;
    const TYPE_WEAPON_SHARPNESS = 9;
    const TYPE_WEAPON_SMITE = 10;
    const TYPE_WEAPON_ARTHROPODS = 11;
    const TYPE_WEAPON_KNOCKBACK = 12;
    const TYPE_WEAPON_FIRE_ASPECT = 13;
    const TYPE_WEAPON_LOOTING = 14;
    const TYPE_MINING_EFFICIENCY = 15;
    const TYPE_MINING_SILK_TOUCH = 16;
    const TYPE_MINING_DURABILITY = 17;
    const TYPE_MINING_FORTUNE = 18;
    const TYPE_BOW_POWER = 19;
    const TYPE_BOW_KNOCKBACK = 20;
    const TYPE_BOW_FLAME = 21;
    const TYPE_BOW_INFINITY = 22;
    const TYPE_FISHING_FORTUNE = 23;
    const TYPE_FISHING_LURE = 24;

	/*
	 * Player $player
	 * Int $id
	 * @return Int 
	 */
	
	protected function getEnchantmentLevelOfArmors(Player $player, Int $id): Int{
		 $return = 0;
	    foreach($player->getArmorInventory()->getContents() as $armor){
	       if($armor->hasEnchantment($id)){
		      $return += $armor->getEnchantment($id)->getLevel();
		    }
	    }
	return $return;
	}
	
	/*
	 * Item $item
	 * Int $id
	 * @return Int 
	 */
	
	protected function getEnchantmentLevel(Item $item, Int $id): Int{
	    if($item->hasEnchantment($id)){
		   return $item->getEnchantment($id)->getLevel();
		 }
	return 0;
	}
	
	/*
	 * Int $id enchantment id
	 * Int $base
	 * Int $level
	 * @return Int|float
	 */
	
	protected function getExtraDamage(Int $id, Int $base, Int $level): float{
	     switch($id){
	        case Enchantment::SHARPNESS:
	           $dmg = (0.4 * $level) + 1;
	        break;
	        case Enchantment::SMITE:
	           $dmg = 2.5 * $level;
	        break;
	        case Enchantment::BANE_OF_ARTHROPODS:
	           $dmg = 2.5 * $level;
	        break;
	        case Enchantment::POWER:
	           $dmg = (($base * (25 / 100))  * $level) + 1;
	        break;    
	     }
	return isset($dmg) ? $dmg : 0.0;
	}
	
	/*
	 * Int $id
	 * Int $level
	 * Int $base
	 * @return Int|float
	 */
	
	protected function getReducedDamage(Int $id, Int $base, Int $level): float{
	     switch($id){
	        case Enchantment::FEATHER_FALLING:
	           $factor = (6 / 100);
              $factor *= $level;
	           $reduce = $base * $factor;
	        break;
	        case Enchantment::PROTECTION:
	          $factor = (3 / 100);
              $factor *= $level;
	           $reduce = $base * $factor;
	        break;
	        case Enchantment::PROJECTILE_PROTECTION:
	        case Enchantment::BLAST_PROTECTION:
		        $factor = (4 / 100);
              $factor *= $level;
	           $reduce = $base * $factor;
	        break;
	        case Enchantment::FIRE_PROTECTION:
		        $factor = (2 / 100);
              $factor *= $level;
	           $reduce = $base * $factor;
	        break;
	     }
	return isset($reduce) and $reduce <= $base ? $reduce : abs($base - $reduce);
	}
	
	/*
	 * @void addHelmetDurability
	 * Player $player
	 * Int $dur
	 */
	
	protected function addHelmetDurability(Player $player, Int $dur): void{
	    $inv = $player->getArmorInventory();
	    if($inv->getHelmet()->getId() == 0){
		   return;
		 }
		 $helmet = $inv->getHelmet();
		 $helmet->setDamage($helmet->getDamage() - $dur > 0 ? $helmet->getDamage() - $dur : 0);
		 $inv->setHelmet($helmet);
	}
	
	/*
	 * @void addChestplateDurability
	 * Player $player
	 * Int $dur
	 */
	
	protected function addChestplateDurability(Player $player, Int $dur): void{
	    $inv = $player->getArmorInventory();
	    if($inv->getChestplate()->getId() == 0){
		   return;
		 }
		 $chestplate = $inv->getChestplate();
		 $chestplate->setDamage($chestplate->getDamage() - $dur > 0 ? $chestplate->getDamage() - $dur : 0);
		 $inv->setChestplate($chestplate);
	}
	
	/*
	 * @void addLeggingsDurability
	 * Player $player
	 * Int $dur
	 */
	
	protected function addLeggingsDurability(Player $player, Int $dur): void{
	    $inv = $player->getArmorInventory();
	    if($inv->getLeggings()->getId() == 0){
		   return;
		 }
		 $leggings = $inv->getLeggings();
		 $leggings->setDamage($leggings->getDamage() - $dur > 0 ? $leggings->getDamage() - $dur : 0);
		 $inv->setLeggings($leggings);
	}
	
	/*
	 * @void addBootsDurability
	 * Player $player
	 * Int $dur
	 */
	
	protected function addBootsDurability(Player $player, int $dur): void{
	    $inv = $player->getArmorInventory();
	    if($inv->getBoots()->getId() == 0){
		   return;
		 }
		 $boots = $inv->getBoots();
		 $boots->setDamage($boots->getDamage() - $dur > 0 ? $boots->getDamage() - $dur : 0);
		 $inv->setBoots($boots);
	}
	
	/*
	 * @void useArmors
	 * Player $player
	 * Int $dmg
	 */
	
	public function useArmors(Player $player, int $dmg = 1): void{
		 abs($dmg); # Make sure no negative value comes in since it gets negative on this function
	    $this->addHelmetDurability($player, -$dmg);
	    $this->addChestplateDurability($player, -$dmg);
	    $this->addLeggingsDurability($player, -$dmg);
	    $this->addBootsDurability($player, -$dmg);
	}
}