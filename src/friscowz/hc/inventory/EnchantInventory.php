<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/17/2017
 * Time: 3:08 AM
 */

namespace friscowz\hc\inventory;


use friscowz\hc\enchants\VanillaEnchant;
use pocketmine\inventory\ContainerInventory;
use pocketmine\item\EnchantedBook;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentEntry;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\EnchantTable;
use pocketmine\inventory\FakeBlockMenu;

class EnchantInventory extends ContainerInventory {
    private $bookshelfAmount = 0;
    private $levels = [];
    /** @var EnchantmentEntry[] */
    private $entries = null;
    /**
     * EnchantInventory constructor.
     *
     * @param Position $pos
     */
    public function __construct(Position $pos){
        parent::__construct(new FakeBlockMenu($this, $pos));
    }
    /**
     * @return EnchantTable
     */
    public function getHolder(){
        return $this->holder;
    }
    /**
     * @return int
     */
    public function getResultSlotIndex(){
        return -1; //enchanting tables don't have result slots, they modify the item in the target slot instead
    }


    /**
     * @param Player $who
     */
    public function onOpen(Player $who) : void
    {
        parent::onOpen($who);
        if($this->levels == null){
            $this->bookshelfAmount = $this->countBookshelf();
            if($this->bookshelfAmount < 0){
                $this->bookshelfAmount = 0;
            }
            if($this->bookshelfAmount > 15){
                $this->bookshelfAmount = 15;
            }
            $base = mt_rand(1, 8) + ($this->bookshelfAmount / 2) + mt_rand(0, $this->bookshelfAmount);
            $this->levels = [
                0 => max($base / 3, 1),
                1 => (($base * 2) / 3 + 1),
                2 => max($base, $this->bookshelfAmount * 2)
            ];
        }
    }

    public function onSlotChange(int $index, Item $before, bool $send) : void
    {
        parent::onSlotChange($index, $before, $send);
        if($index === 0){
            $item = $this->getItem(0);
            if($item->getId() === Item::AIR){
                $this->entries = null;
            }elseif($before->getId() == Item::AIR and !$item->hasEnchantments()){
                //before enchant
                if($this->entries === null){
                    $enchantAbility = Enchantment::getEnchantAbility($item);
                    $this->entries = [];
                    for($i = 0; $i < 3; $i++){
                        $result = [];
                        $level = $this->levels[$i];
                        $k = $level + mt_rand(0, round(round($enchantAbility / 4) * 2)) + 1;
                        $bonus = ($this->randomFloat() + $this->randomFloat() - 1) * 0.15 + 1;
                        $modifiedLevel = ($k * (1 + $bonus) + 0.5);
                        $possible = EnchantmentLevelTable::getPossibleEnchantments($item, $modifiedLevel);
                        $weights = [];
                        $total = 0;
                        for($j = 0; $j < count($possible); $j++){
                            $id = $possible[$j]->getId();
                            $weight = Enchantment::getEnchantWeight($id);
                            $weights[$j] = $weight;
                            $total += $weight;
                        }
                        $v = mt_rand(1, $total + 1);
                        $sum = 0;
                        for($key = 0; $key < count($weights); ++$key){
                            $sum += $weights[$key];
                            if($sum >= $v){
                                $key++;
                                break;
                            }
                        }
                        $key--;
                        if(!isset($possible[$key])) return;
                        $enchantment = $possible[$key];
                        $result[] = $enchantment;
                        unset($possible[$key]);
                        //Extra enchantment
                        while(count($possible) > 0){
                            $modifiedLevel = round($modifiedLevel / 2);
                            $v = mt_rand(0, 51);
                            if($v <= ($modifiedLevel + 1)){
                                $possible = $this->removeConflictEnchantment($enchantment, $possible);
                                $weights = [];
                                $total = 0;
                                for($j = 0; $j < count($possible); $j++){
                                    $id = $possible[$j]->getId();
                                    $weight = Enchantment::getEnchantWeight($id);
                                    $weights[$j] = $weight;
                                    $total += $weight;
                                }
                                $v = mt_rand(1, $total + 1);
                                $sum = 0;
                                for($key = 0; $key < count($weights); ++$key){
                                    $sum += $weights[$key];
                                    if($sum >= $v){
                                        $key++;
                                        break;
                                    }
                                }
                                $key--;
                                $enchantment = $possible[$key];
                                $result[] = $enchantment;
                                unset($possible[$key]);
                            }else{
                                break;
                            }
                        }
                        $this->entries[$i] = new EnchantmentEntry($result, $level, Enchantment::getRandomName());
                    }
                    $this->sendEnchantmentList();
                }
            }
        }
    }

    /**
     * @param Player $who
     */
    public function onClose(Player $who) : void
    {
        parent::onClose($who);
        $level = $this->getHolder()->getLevel();
        for($i = 0; $i < 2; ++$i){
            if($level instanceof Level) $level->dropItem($this->getHolder()->add(0.5, 0.5, 0.5), $this->getItem($i));
            $this->clear($i);
        }
        if(count($this->getViewers()) === 0){
            $this->levels = null;
            $this->entries = null;
            $this->bookshelfAmount = 0;
        }
    }
    /**
     * @param Enchantment[] $ent1
     * @param Enchantment[] $ent2
     *
     * @return bool
     */
    public function checkEnts(array $ent1, array $ent2){
        foreach($ent1 as $enchantment){
            $hasResult = false;
            foreach($ent2 as $enchantment1){
                if($enchantment->equals($enchantment1)){
                    $hasResult = true;
                    continue;
                }
            }
            if(!$hasResult){
                return false;
            }
        }
        return true;
    }
    /**
     * @param Player $who
     * @param Item   $before
     * @param Item   $after
     */
    public function onEnchant(Player $who, Item $before, Item $after){
        $result = ($before->getId() === Item::BOOK) ? new EnchantedBook() : $before;
        if(!$before->hasEnchantments() and $after->hasEnchantments() and $after->getId() == $result->getId() and
            $this->levels != null and $this->entries != null
        ){
            $enchantments = $after->getEnchantments();
            for($i = 0; $i < 3; $i++){
                if($this->checkEnts($enchantments, $this->entries[$i]->getEnchantments())){
                    $lapis = $this->getItem(1);
                    $level = $who->getXpLevel();
                    $cost = $this->entries[$i]->getCost();
                    if($lapis->getId() == Item::DYE and $lapis->getDamage() == 4 and $lapis->getCount() > $i and $level >= $cost){
                        foreach($enchantments as $enchantment){
                            $result->addEnchantment($enchantment);
                        }
                        $this->setItem(0, $result);
                        $lapis->setCount($lapis->getCount() - $i - 1);
                        $this->setItem(1, $lapis);
                        $who->takeXpLevel($i + 1);
                        break;
                    }
                }
            }
        }
    }
    /**
     * @return int
     */
    public function countBookshelf() : int{
        /*
        if($this->getHolder()->getLevel()->getServer()->coun){
            $count = 0;
            $pos = $this->getHolder();
            $offsets = [[2, 0], [-2, 0], [0, 2], [0, -2], [2, 1], [2, -1], [-2, 1], [-2, 1], [1, 2], [-1, 2], [1, -2], [-1, -2]];
            for($i = 0; $i < 3; $i++){
                foreach($offsets as $offset){
                    if($pos->getLevel()->getBlockIdAt($pos->x + $offset[0], $pos->y + $i, $pos->z + $offset[1]) == Block::BOOKSHELF){
                        $count++;
                    }
                    if($count >= 15){
                        break 2;
                    }
                }
            }
            return $count;
        }else{*/
        return mt_rand(0, 15);
    }
    /**
     * @param Enchantment   $enchantment
     * @param Enchantment[] $enchantments
     *
     * @return Enchantment[]
     */
    public function removeConflictEnchantment(Enchantment $enchantment, array $enchantments){
        if(count($enchantments) > 0){
            foreach($enchantments as $e){
                $id = $e->getId();
                if($id == $enchantment->getId()){
                    unset($enchantments[$id]);
                    continue;
                }
                if($id >= 0 and $id <= 4 and $enchantment->getId() >= 0 and $enchantment->getId() <= 4){
                    //Protection
                    unset($enchantments[$id]);
                    continue;
                }
                if($id >= 9 and $id <= 14 and $enchantment->getId() >= 9 and $enchantment->getId() <= 14){
                    //Weapon
                    unset($enchantments[$id]);
                    continue;
                }
                if(($id === VanillaEnchant::TYPE_MINING_SILK_TOUCH and $enchantment->getId() === VanillaEnchant::TYPE_MINING_FORTUNE) or ($id === VanillaEnchant::TYPE_MINING_FORTUNE and $enchantment->getId() === VanillaEnchant::TYPE_MINING_SILK_TOUCH)){
                    //Protection
                    unset($enchantments[$id]);
                    continue;
                }
            }
        }
        $result = [];
        if(count($enchantments) > 0){
            foreach($enchantments as $enchantment){
                $result[] = $enchantment;
            }
        }
        return $result;
    }

    public function getName(): string
    {
        return "Enchantment Table";
    }

    public function getDefaultSize(): int
    {
        return 2;
    }

    /**
     * Returns the Minecraft PE inventory type used to show the inventory window to clients.
     * @return int
     */
    public function getNetworkType() : int
    {
        return WindowTypes::ENCHANTMENT;
    }
}