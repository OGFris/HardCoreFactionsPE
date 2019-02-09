<?php

declare(strict_types = 1);

namespace friscowz\hc\entity\projectile;

use friscowz\hc\item\Potion;
use pocketmine\entity\{
	Entity, Living, projectile\Throwable
};

class SplashPotion extends Throwable {

	const NETWORK_ID = self::SPLASH_POTION;

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			$color = Potion::getColor($this->getPotionId());
			//$this->getLevel()->addParticle(new SpellParticle($this, $color[0], $color[1], $color[2]));
			$radius = 6;
			foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow($radius, $radius, $radius)) as $p){
				foreach(Potion::getEffectsById($this->getPotionId()) as $effect){
					if($p instanceof Living){
						$p->addEffect($effect);
					}
				}
			}
			$this->close();
		}

		return parent::onUpdate($currentTick);
	}

	public function getPotionId(): int{
		return (int)$this->namedtag["PotionId"];
	}

	public function onCollideWithEntity(Entity $entity){
		return;
	}
}