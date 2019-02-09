<?php

declare(strict_types = 1);

namespace friscowz\hc\item;

use pocketmine\block\Lava;
use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\item\Food;
use pocketmine\item\Item;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\Player;

class ChorusFruit extends Food {

	const RAND_POS_X = [-8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2, -1, 0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8];
	const RAND_POS_Y = [-8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2, -1, 0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8];
	const RAND_POS_Z = [-8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2, -1, 0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8];

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::CHORUS_FRUIT, $meta, "Chorus Fruit");
	}

	public function getMaxStackSize(): int{
		return 64;
	}

	public function canBeConsumedBy(Entity $entity): bool{
		return $entity instanceof Human;
	}

	public function getFoodRestore(): int{
		return 4;
	}

	public function getSaturationRestore(): float{
		return 0; // todo: check
	}

    public function onConsume(Living $consumer){
		parent::onConsume($consumer);

		if($consumer instanceof Player){
			$tries = 0;
			$pos = $consumer->getPosition();
			while($tries < 100){
				$tries++;

				$randpos = $pos->add(
					self::RAND_POS_X[array_rand(self::RAND_POS_X)],
					self::RAND_POS_Y[array_rand(self::RAND_POS_Y)],
					self::RAND_POS_Z[array_rand(self::RAND_POS_Z)]
				);
				$b = $consumer->getLevel()->getBlock($randpos);
				$below = $consumer->getLevel()->getBlock($randpos->subtract(0, 1, 0));
				if(!($b instanceof Solid) && !($b instanceof Lava) && !($below instanceof Lava) && $below instanceof Solid){
					$consumer->teleport($randpos, $consumer->getYaw(), $consumer->getPitch());

					$consumer->getLevel()->addSound(new EndermanTeleportSound($randpos), $consumer->getLevel()->getPlayers());
					break;
				}
			}
		}
	}
}