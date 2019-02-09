<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 18/08/2017
 * Time: 00:01
 */

namespace friscowz\hc\shop;


use friscowz\hc\Myriad;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Shop
{
    const SELL = 0;
    const BUY = 1;

    private $plugin;
    private $signs;

    /**
     * Sell constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->setSigns(new Config($this->getPlugin()->getDataFolder() . "signs.json", Config::JSON));
    }

    /**
     * @return Myriad
     */
    public function getPlugin() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin(Myriad $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return mixed
     */
    public function getSigns() : Config
    {
        return $this->signs;
    }

    /**
     * @param mixed $signs
     */
    public function setSigns(Config $signs)
    {
        $this->signs = $signs;
    }

    /**
     * @param int $type
     * @param int $id
     * @param int $damage
     * @param int $amount
     * @param int $price
     * @param Vector3 $pos
     * @param string $name
     */
    public function createSign(int $type, int $id, int $damage, int $amount, int $price, Vector3 $pos, string $name = null)
    {
        $array = [
            "name" => $name,
            "type" => $type,
            "id" => $id,
            "damage" => $damage,
            "amount" => $amount,
            "price" => $price
        ];
        $tile = $this->getPlugin()->getServer()->getDefaultLevel()->getTile($pos);
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        if ($type == self::SELL) {
            $text1 = TextFormat::GREEN . "[SELL]";
        } else {
            $text1 = TextFormat::RED . "[BUY]";
        }
        if ($name != null) {
            $text2 = $name;
        } else {
            $text2 = Item::get($id, $damage)->getName() . " x" . $amount;
        }
        $text3 = $price . "$";
        $tile->setText($text1, $text2, $text3);
        if($tile instanceof Sign){
            $tile->setText($text1, $text2, $text3);
        }
        $this->getSigns()->set($pos, $array);
        $this->getSigns()->save();
    }

    /**
     * @param Vector3 $pos
     */
    public function deleteSign(Vector3 $pos)
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        $this->getSigns()->remove($pos);
    }

    /**
     * @param Vector3 $pos
     * @return bool
     */
    public function isSign(Vector3 $pos) : bool
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        return $this->getSigns()->exists($pos);
    }

    /**
     * @param Vector3 $pos
     * @return int
     */
    public function getPrice(Vector3 $pos) : int
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        return $this->getSigns()->get($pos)["price"];
    }

    /**
     * @param Vector3 $pos
     * @return int
     */
    public function getType(Vector3 $pos) : int
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        return $this->getSigns()->get($pos)["type"];
    }

    /**
     * @param Vector3 $pos
     * @return int
     */
    public function getId(Vector3 $pos) : int
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        return $this->getSigns()->get($pos)["id"];
    }

    /**
     * @param Vector3 $pos
     * @return int
     */
    public function getDamage(Vector3 $pos) : int
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        return $this->getSigns()->get($pos)["damage"];
    }

    /**
     * @param Vector3 $pos
     * @return int
     */
    public function getAmount(Vector3 $pos) : int
    {
        $pos = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        return $this->getSigns()->get($pos)["amount"];
    }

    /**
     * @param Vector3 $pos
     * @return string
     */
    public function getName(Vector3 $pos) : string
    {
        $post = $pos->getX() . ":" . $pos->getX() . ":" . $pos->getZ();
        if(!$this->isCustomName($pos)){
            return "";
        }
        return $this->getSigns()->get($post)["name"];
    }

    /**
     * @param Vector3 $pos
     * @return bool
     */
    public function isCustomName(Vector3 $pos) : bool
    {
        return $this->getName($pos) != null;
    }
}