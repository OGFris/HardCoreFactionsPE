<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 02/11/2017
 * Time: 22:02
 */

namespace friscowz\hc\crate;


use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Crate
{
    const IRON_CRATE = 0;
    const GOLD_CRATE = 1;
    const DIAMOND_CRATE = 2;
    const EMERALD_CRATE = 3;
    const REDSTONE_CRATE = 4;

    private $plugin;
    private $crates;

    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->crates = [
            self::IRON_CRATE => [
                "name" => "§l§fIron Crate§r",
                "key" => "§r§fIron Key§r",
                "block" => BlockIds::IRON_BLOCK
            ],
            self::GOLD_CRATE => [
                "name" => "§l§6Gold Crate§r",
                "key" => "§r§6Gold Key§r",
                "block" => BlockIds::GOLD_BLOCK
            ],
            self::DIAMOND_CRATE => [
                "name" => "§l§bDiamond Crate§r",
                "key" => "§r§bDiamond Key§r",
                "block" => BlockIds::DIAMOND_BLOCK
            ],
            self::EMERALD_CRATE => [
                "name" => "§l§aEmerald Crate§r",
                "key" => "§r§aEmerald Key§r",
                "block" => BlockIds::EMERALD_BLOCK
            ],
            self::REDSTONE_CRATE => [
                "name" => "§l§4Redstone Crate§r",
                "key" => "§r§4Redstone Key§r",
                "block" => BlockIds::REDSTONE_BLOCK
            ],
        ];
    }

    /**
     * @param int $crate
     * @return array
     */
    public function getItems(int $crate) : array
    {
        switch($crate){
            case self::IRON_CRATE:
                return [
                    Item::get(ItemIds::NETHER_WART, 0, 16),
                    Item::get(ItemIds::GLISTERING_MELON, 0, 16),
                    Item::get(ItemIds::BLAZE_ROD, 0, 1),
                    Item::get(ItemIds::MELON_SEEDS, 0, 4),
                    Item::get(ItemIds::GOLDEN_APPLE, 0, 1),
                    Item::get(ItemIds::SUGAR, 0, 8),
                    Item::get(ItemIds::POTATO, 0, 8),
                    Item::get(ItemIds::GLASS_BOTTLE, 0, 8),
                    Item::get(ItemIds::GUNPOWDER, 0, 16),
                    Item::get(ItemIds::GLOWSTONE_DUST, 0, 16),
                    Item::get(ItemIds::BOTTLE_O_ENCHANTING, 0, 16),
                    Item::get(ItemIds::SPIDER_EYE, 0, 16),
                    Item::get(ItemIds::ENDER_PEARL, 0, 4),
                    Item::get(ItemIds::COBWEB, 0, 4),
                ];
            break;

            case self::GOLD_CRATE:
                return [
                    Item::get(ItemIds::DIAMOND_HELMET, 0, 1),
                    Item::get(ItemIds::DIAMOND_CHESTPLATE, 0, 1),
                    Item::get(ItemIds::DIAMOND_LEGGINGS, 0, 1),
                    Item::get(ItemIds::DIAMOND_BOOTS, 0, 1),
                    Item::get(ItemIds::ENDER_PEARL, 0, 2),
                    Item::get(ItemIds::GLOWSTONE_DUST, 0, 32),
                    Item::get(ItemIds::GOLDEN_APPLE, 0, 3),
                    Item::get(ItemIds::DIAMOND_PICKAXE, 0, 1),
                    Item::get(ItemIds::DIAMOND_AXE, 0, 1),
                    Item::get(ItemIds::DIAMOND_SWORD, 0, 1),
                    Item::get(ItemIds::DIAMOND_BLOCK, 0, 4),
                    Item::get(ItemIds::GOLD_BLOCK, 0, 4),
                    Item::get(ItemIds::IRON_BLOCK, 0, 4),
                    Item::get(ItemIds::COAL_BLOCK, 0, 8)
                ];
            break;
        }
        return [];
    }

    /**
     * @param int $tier
     * @return array
     */
    public function getRandItems(int $tier) : array
    {
        $items = $this->getItems($tier);
        $count = count($items) - 1;

        $item1 = $items[mt_rand(0, $count)];
        $item2 = $items[mt_rand(0, $count)];
        $item3 = $items[mt_rand(0, $count)];

        return [$item1, $item2, $item3];
    }

    /**
     * @param Vector3 $pos
     * @return bool
     */
    public function isCrate(Vector3 $pos)
    {
        $id = $this->getPlugin()->getServer()->getDefaultLevel()->getBlockIdAt($pos->getX(), $pos->getY() - 1, $pos->getZ());
        if ($id == BlockIds::IRON_BLOCK or $id == BlockIds::GOLD_BLOCK or $id == BlockIds::DIAMOND_BLOCK or $id == BlockIds::EMERALD_BLOCK or $id == BlockIds::REDSTONE_BLOCK) {
            return true;
        }
        return false;
    }

    public function addCrateText(MDPlayer $player)
    {

    }

    /**
     * @param Vector3 $pos
     * @return int|null
     */
    public function getCrateType(Vector3 $pos)
    {
        $id = $this->getPlugin()->getServer()->getDefaultLevel()->getBlockIdAt($pos->getX(), $pos->getY() - 1, $pos->getZ());
        switch($id){
            case BlockIds::IRON_BLOCK:
                return self::IRON_CRATE;
            break;

            case BlockIds::GOLD_BLOCK:
                return self::GOLD_CRATE;
            break;

            case BlockIds::DIAMOND_BLOCK:
                return self::DIAMOND_CRATE;
            break;

            case BlockIds::EMERALD_BLOCK:
                return self::EMERALD_CRATE;
            break;

            case BlockIds::REDSTONE_BLOCK:
                return self::REDSTONE_CRATE;
            break;

            default:
                return null;
            break;
        }
    }

    /**
     * @param Player $player
     * @param Vector3 $pos
     */
    public function openCreate(Player $player, Vector3 $pos)
    {
        if($this->isCrate($pos)){
            $type = $this->getCrateType($pos);
            $name = $this->crates[$type]["name"];
            $key = $this->crates[$type]["key"];
            $block = $this->crates[$type]["block"];
            $item = $player->getInventory()->getItemInHand();

            if($item->getCustomName() == $key){
                $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
                $player->getLevel()->addParticle(new DestroyBlockParticle($pos->add(0, 1, 0), Block::get($block)), [$player]);
                $this->getPlugin()->getServer()->broadcastMessage(Utils::getPrefix() . TextFormat::GRAY . $player->getName() . " opened a " . $name . "!");
                $rand = $this->getRandItems($type);
                $player->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "You have received:");
                foreach ($rand as $item){
                    $player->getInventory()->addItem($item);
                    $player->sendMessage(TextFormat::GRAY . "- " . TextFormat::GREEN . $item->getName());
                }
            } else {
                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "Please hold your " . $name . TextFormat::RED . " Key in your hand!");
            }
        }
    }

    /**
     * @param Player $player
     * @param int $tier
     * @param int $count
     */
    public function giveKey(Player $player, int $tier, int $count)
    {
        $name = $this->crates[$tier-1]["key"];
        for($i = $count; $i > 0; $i--){
            $key = new Item(ItemIds::TRIPWIRE_HOOK, 0);
            $key->setCustomName($name);
            $player->getInventory()->addItem($key);
        }
    }

    public function getCrateName(int $tier) : string
    {
        return $this->crates[$tier]["name"];
    }

    public function getKeyName(int $tier) : string
    {
        return $this->crates[$tier ]["key"];
    }

    /**
     * @return Myriad
     */
    public function getPlugin () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }
}