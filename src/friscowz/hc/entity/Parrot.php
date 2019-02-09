<?php

declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Animal;
use pocketmine\item\Item;

class Parrot extends Animal {
	const NETWORK_ID = self::PARROT;

	public function getName(): string{
		return "Parrot";
	}

	public function getDrops(): array{
		return [Item::get(Item::FEATHER, 0, mt_rand(1, 2))];
	}
}