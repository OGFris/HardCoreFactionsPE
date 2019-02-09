<?php

declare(strict_types = 1);

namespace friscowz\hc\entity\projectile;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class EnderPearl extends Throwable {
	const NETWORK_ID = self::ENDER_PEARL;

	public function onUpdate(int $currentTick): bool{
		$p = $this->getOwningEntity();
		if($this->isCollided || $this->age > 1200){
			if($this->y > 0 && $p instanceof Player){
				$pk1 = new LevelEventPacket();
				$pk1->data = 0;
				$pk1->evid = 2010; // Portal particles n stuff.
				$pk1->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
				$p->getServer()->broadcastPacket($p->getLevel()->getPlayers(), $pk1);

				$p->teleport($this->getPosition());
				$p->attack(new EntityDamageEvent($p, EntityDamageEvent::CAUSE_FALL, Main::$ePearlDamage));

				$pk2 = new LevelEventPacket();
				$pk2->data = 0;
				$pk2->evid = 2010;
				$pk2->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
				$p->getServer()->broadcastPacket($p->getLevel()->getPlayers(), $pk2);

				$pk3 = new LevelEventPacket();
				$pk3->data = 0;
				$pk3->evid = LevelEventPacket::EVENT_SOUND_ENDERMAN_TELEPORT;
				$pk3->position = new Vector3($p->getX(), $p->getY(), $p->getZ());
				$p->getServer()->broadcastPacket($p->getLevel()->getPlayers(), $pk3);
			}

			$this->close();
		}

		return parent::onUpdate($currentTick);
	}

	public function onCollideWithEntity(Entity $entity){
		return;
	}
}