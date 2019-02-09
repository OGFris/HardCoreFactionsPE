<?php


declare(strict_types = 1);

namespace friscowz\hc\entity;

use pocketmine\entity\Monster;

class Vex extends Monster {
	const NETWORK_ID = self::VEX;

	public $width = 0.6;
	public $length = 0.6;
	public $height = 0;

	public function getName(): string{
		return "Vex";
	}

	public function initEntity(){
		$this->setMaxHealth(14);
		parent::initEntity();
	}
}