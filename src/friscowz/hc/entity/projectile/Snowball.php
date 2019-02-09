<?php

declare(strict_types = 1);

namespace friscowz\hc\entity\projectile;

use pocketmine\block\Block;
use pocketmine\entity\projectile\Throwable;
use pocketmine\level\particle\DestroyBlockParticle;

class Snowball extends Throwable {
	const NETWORK_ID = self::SNOWBALL;

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			$this->getLevel()->addParticle(new DestroyBlockParticle($this, Block::get(Block::SNOW))); // Realistic aye?
			$this->close();
		}

		return parent::onUpdate($currentTick);
	}
}
