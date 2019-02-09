<?php

declare(strict_types = 1);

namespace friscowz\hc\item;

use pocketmine\item\{
	Item, ProjectileItem
};
use pocketmine\math\Vector3;
use pocketmine\Player;

class EnderPearl extends ProjectileItem {

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::ENDER_PEARL, $meta, "Ender Pearl");
	}

	public function onClickAir(Player $player, Vector3 $directionVector): bool
    {
        return true;
    }

    public function getProjectileEntityType(): string{
		return "EnderPearl";
	}

	public function getThrowForce(): float{
		return 1.1;
	}

	/**
	 * @return int
	 */
	public function getMaxStackSize(): int{
		return 16;
	}

}