<?php

declare(strict_types = 1);

namespace friscowz\hc\item;

use pocketmine\item\{
	Item, ItemFactory
};

class ItemManager {
	public static function init(){
		//ItemFactory::registerItem(new Boat(), true);
		ItemFactory::registerItem(new EnchantingBottle(), true);
		ItemFactory::registerItem(new EnderPearl(), true);
		ItemFactory::registerItem(new Potion(), true);
		ItemFactory::registerItem(new LingeringPotion(), true);
		ItemFactory::registerItem(new SplashPotion(), true);
		//ItemFactory::registerItem(new FlintSteel(), true);
		ItemFactory::registerItem(new FireCharge(), true);
		ItemFactory::registerItem(new TotemOfUndying(), true);
		ItemFactory::registerItem(new Elytra(), true);
		ItemFactory::registerItem(new FireworkRocket(), true);
		ItemFactory::registerItem(new ChorusFruit(), true);



		Item::addCreativeItem(Item::get(Item::ENDER_PEARL));
		Item::addCreativeItem(Item::get(Item::ENDER_CHEST));
		Item::addCreativeItem(Item::get(Item::BOTTLE_O_ENCHANTING));
		Item::addCreativeItem(Item::get(Item::FIRE_CHARGE));
		Item::addCreativeItem(Item::get(Item::TOTEM));
		Item::addCreativeItem(Item::get(Item::ELYTRA));
		Item::addCreativeItem(Item::get(Item::FIREWORKS));
		Item::addCreativeItem(Item::get(Item::CHORUS_FRUIT));

		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::SPLASH_POTION, $i));
		}

		for($i = 0; $i <= 36; $i++){
			Item::addCreativeItem(Item::get(Item::LINGERING_POTION, $i));
		}

		for($i = 0; $i <= 5; $i++){
			//Item::addCreativeItem(Item::get(Item::BOAT, $i));
		}
	}
}
