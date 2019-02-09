<?php


declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Living;

class MagmaCube extends Living {
	const NETWORK_ID = self::MAGMA_CUBE;

	public function getName(): string{
		return "Magma Cube";
	}
}