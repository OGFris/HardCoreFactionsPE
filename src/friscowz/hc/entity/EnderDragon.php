<?php

declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Monster;

class EnderDragon extends Monster {
	const NETWORK_ID = self::ENDER_DRAGON;

	public function getName(): string{
		return "Ender Dragon";
	}

	public function initEntity(){
		$this->setMaxHealth(200);
		parent::initEntity();
	}
}
