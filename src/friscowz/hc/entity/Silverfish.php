<?php

declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Monster;

class Silverfish extends Monster {
	const NETWORK_ID = self::SILVERFISH;

	public function getName(): string{
		return "Silverfish";
	}
}