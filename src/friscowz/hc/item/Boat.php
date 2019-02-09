<?php

declare(strict_types = 1);

namespace friscowz\hc\item;

use friscowz\hc\entities\BetterBoat as BoatEntity;
use pocketmine\block\Block;
use pocketmine\item\Item as ItemPM;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\{
	CompoundTag, DoubleTag, FloatTag, IntTag, ListTag
};
use pocketmine\Player;

class Boat extends ItemPM {

	public function __construct($meta = 0){
		parent::__construct(self::BOAT, $meta, "Oak Boat");
		if($this->meta === 1){
			$this->name = "Spruce Boat";
		}elseif($this->meta === 2){
			$this->name = "Birch Boat";
		}elseif($this->meta === 3){
			$this->name = "Jungle Boat";
		}elseif($this->meta === 4){
			$this->name = "Acacia Boat";
		}elseif($this->meta === 5){
			$this->name = "Dark Oak Boat";
		}
	}

	public function getMaxStackSize(): int{
		return 1;
	}

	public function canBeActivated(){
		return true;
	}

	public function onActivate(Player $player, Block $block, Block $target, int $face, Vector3 $facepos): bool{
		$realPos = $target->getSide($face)->add(0.5, 0.4, 0.5);
		$boat = new BoatEntity($player->getLevel(), new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $realPos->getX()),
				new DoubleTag("", $realPos->getY()),
				new DoubleTag("", $realPos->getZ()),
			]),
			new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0),
			]),
			new ListTag("Rotation", [
				new FloatTag("", 0),
				new FloatTag("", 0),
			]),
			new IntTag("WoodID", $this->getDamage()),
		]));
		$boat->spawnToAll();
		if($player->isSurvival()){
			--$this->count;
		}

		return true;
	}

	public function getFuelTime(): int{
		return 1200; //400 in PC
	}
	//TODO
}