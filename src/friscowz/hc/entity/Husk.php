<?php


declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Zombie;

class Husk extends Zombie {
	const NETWORK_ID = self::HUSK;

	public function getName(): string{
		return "Husk";
	}
}
