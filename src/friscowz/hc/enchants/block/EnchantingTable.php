<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/17/2017
 * Time: 3:06 AM
 */

namespace friscowz\hc\enchants\block;

use friscowz\hc\inventory\EnchantInventory;
use pocketmine\item\Item;
use pocketmine\item\tool\pickaxe\Pickaxe;
use pocketmine\item\tool\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\block\Transparent;
use pocketmine\block\Block;

class EnchantingTable extends Transparent
{

    protected $id = self::ENCHANTING_TABLE;

    public function __construct(int $meta = 0)
    {
        $this->meta = $meta;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $facePos, Player $player = null): bool
    {
        $this->getLevel()->setBlock($blockReplace, $this, true, true);
        $nbt = new CompoundTag("", [
            new StringTag("id", Tile::ENCHANT_TABLE),
            new IntTag("x", $this->x),
            new IntTag("y", $this->y),
            new IntTag("z", $this->z),
        ]);
        if ($item->hasCustomName()) {
            $nbt->CustomName = new StringTag("CustomName", $item->getCustomName());
        }
        if ($item->hasCustomBlockData()) {
            foreach ($item->getCustomBlockData() as $key => $v) {
                $nbt->{$key} = $v;
            }
        }
        Tile::createTile(Tile::ENCHANT_TABLE, $this->getLevel(), $nbt);
        return true;
    }

    public function getHardness(): float
    {
        return 5;
    }

    public function getBlastResistance(): float
    {
        return 6000;
    }

    public function getName(): string
    {
        return "Enchanting Table";
    }

    public function onActivate(Item $item, Player $player = null): bool
    {
        if ($player instanceof Player) {
            //TODO lock
            $player->addWindow(new EnchantInventory($this));
        }
        return true;
    }

    public function getDrops(Item $item): array
    {
        if ($item instanceof Pickaxe and $item->getTier() >= ToolTier::TIER_WOODEN) {
            return parent::getDrops($item);
        }
        return [];
    }
}