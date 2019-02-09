<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 10:17 PM
 */

namespace friscowz\hc\blocks;


use friscowz\hc\tiles\PotionSpawner;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class Observer extends Block
{
    protected $id = self::OBSERVER;

    /**
     * Observer constructor.
     * @param int $id
     * @param int $meta
     * @param null $name
     * @param null $itemId
     */
    public function __construct($id = BlockIds::OBSERVER, $meta = 0, $name = \null, $itemId = \null)
    {
        parent::__construct($id, $meta, $name, $itemId);
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return "PotionSpawner";
    }

    /**
     * @return int
     */
    public function getToolType() : int{
        return Tool::STONE_PICKAXE;
    }

    /**
     * @param Item $item
     * @param Block $blockReplace
     * @param Block $blockClicked
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     * @return bool
     */
    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool
    {
        $this->getLevel()->setBlock($blockReplace, $this, true, true);
        Tile::createTile("PotionSpawner", $this->getLevel(), PotionSpawner::createNBT($this->asVector3()));
        return true;
    }

}