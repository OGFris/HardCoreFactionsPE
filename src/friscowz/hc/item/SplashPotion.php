<?php


declare(strict_types = 1);

namespace friscowz\hc\item;

use pocketmine\entity\{
	Entity, projectile\Projectile
};
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\{
	Item, ProjectileItem
};
use pocketmine\level\sound\LaunchSound;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\block\Block;

class SplashPotion extends ProjectileItem {

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::SPLASH_POTION, $meta, $this->getNameByMeta($meta));
	}

	public function getNameByMeta(int $meta){
		return "Splash " . Potion::getNameByMeta($meta);
	}

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return bool
     */
    public function onClickAir(Player $player, Vector3 $directionVector): bool
    {
        return true;
    }

    public function getMaxStackSize(): int{
		return 1;
	}

	public function getProjectileEntityType(): string{
		return "SplashPotion";
	}

	public function getThrowForce(): float{
		return 1.2;
	}

}