<?php

declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Monster;

class Endermite extends Monster {
	const NETWORK_ID = self::ENDERMITE;

	public function getName(): string{
		return "Endermite";
	}
}