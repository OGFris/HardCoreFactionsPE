<?php

declare(strict_types = 1);

namespace friscowz\hc\item;

use pocketmine\item\{
	Armor, Item
};

class Elytra extends Armor {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::ELYTRA, $meta, "Elytra Wings");
	}
}