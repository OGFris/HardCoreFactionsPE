<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:45
 */

namespace friscowz\hc;

use pocketmine\{
    block\Block, item\ItemFactory, math\Vector3, utils\Config, utils\TextFormat
};

class Manager
{
    private $plugin;
    private $restarting = false;

    public static $servers = [
        "lobby" => [
            "ip" => "localhost",
            "port" => 19134
        ]
    ];
    private $blocks = [
        "3:2" => "243:0",
        "31:1" => "2:0",
        "43:7" => "43:6",
        "44:7" => "44:6",
        "82:0" => "337:0",
        "112:0" => "405:0",
        "115:0" => "372:0",
        "125:5" => "157:0",
        "126:5" => "158:0",
        "126:13" => "158:0",
        "157:0" => "126:0",
        "158:0" => "125:0",
        "188:0" => "85:1",
        "189:0" => "85:2",
        "190:0" => "85:3",
        "191:0" => "85:5",
        "192:0" => "85:4",
        "198:0" => "208:0",
        "199:0" => "240:0",
        "202:0" => "201:2",
        "205:0" => "44:7",
        "207:0" => "244:0",
        "208:0" => "198:0",
        "214:0" => "115:0",
        "322:1" => "466:0",
        "410:0" => "422:0",
        "434:0" => "457:0",
        "435:0" => "458:0",
        "436:0" => "459:0",
        "450:0" => "445:0"
    ];


    /**
     * PlayersManager constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        //$this->initDb();
        //$this->checkPCBlocks();
        self::$servers["lobby"]["ip"] = Myriad::getInstance()->getServer()->getIp();
    }

    /**
     * @return mixed
     */
    public function getPlugin () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin ($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return array
     */
    public function getLobbyServer() : array
    {
        return self::$servers["lobby"];
    }


    /**
     *
     */
    public function initDb()
    {
        $db = "hcf";
        $connection = @mysqli_connect('127.0.0.1:3306', 'root', 'root');

        if($connection) {
            $table = "CREATE TABLE IF NOT EXISTS Players(playername TEXT PRIMARY KEY, Lives INT DEFAULT 0, DeathBanned INT DEFAULT 0, Banned INT DEFAULT 0);";
            if (!mysqli_select_db($connection, $db)) {
                Myriad::getInstance()->getLogger()->debug(TextFormat::RED . "Can't find the Database " . $db . "! " . mysqli_error($connection));
                if(mysqli_query($connection, 'CREATE DATABASE ' . $db)){
                    Myriad::getInstance()->getLogger()->debug(TextFormat::GREEN . "Successfully created the Database " . $db . ".");
                    if(mysqli_query($connection, $table)){
                        Myriad::getInstance()->getLogger()->debug(TextFormat::GREEN . "Successfully created the Table.");
                    } else {
                        Myriad::getInstance()->getLogger()->critical(TextFormat::RED . "Failed to create the Table! " . mysqli_error($connection));
                    }
                } else {
                    Myriad::getInstance()->getLogger()->critical(TextFormat::RED . "Failed to create the Database" . $db . "! " . mysqli_error($connection));
                }
            } else {
                if(mysqli_query($connection, $table)){
                    Myriad::getInstance()->getLogger()->debug(TextFormat::GREEN . "Successfully Checked the Table.");
                }
            }
            mysqli_close($connection);
        } else {
            Myriad::getInstance()->getLogger()->critical(TextFormat::RED . "Can't connect to the Server. " . mysqli_connect_error());
        }
    }

    /**
     * @param int $radius
     */
    public function setSpawnRadius(int $radius)
    {
        if(Myriad::$data instanceof Config){
            Myriad::$data->set("SpawnRadius", $radius);
            Myriad::$data->save();
        }
    }

    /**
     * @return int
     */
    public static function getSpawnRadius() : int
    {
        return Myriad::getData("SpawnRadius");
    }

    /**
     * @param int $border
     */
    public static function buildRoad($border)
    {
        $positive = self::getSpawnRadius();
        $negative = $positive - ($positive * 2);
        $y = Myriad::getInstance()->getServer()->getDefaultLevel()->getHighestBlockAt(0, 0);

        $time = microtime(true);
        $blocks = 0;
        echo "[Road]started ! " . PHP_EOL;
        for ($x = $positive; $x < $border; ++$x) {
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, -2), Block::get(Block::WOOL, 14));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, -1), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, 0), Block::get(Block::GLASS));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, 1), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, 2), Block::get(Block::WOOL, 14));
            //Bedrock
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, -2), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, -1), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, 0), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, 1), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, 2), Block::get(7));

            $blocks += 10;
        }
        $time = microtime(true) - $time;
        echo "[Road] Finished the road n1 in " . $time . "sec(s)!" . $blocks . "Blocks." . PHP_EOL;

        $time = microtime(true);
        $blocks = 0;
        echo "[Road] started ! " . PHP_EOL;
        for ($x = $negative; $x > ($border - ($border * 2)); --$x) {
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, -2), Block::get(Block::WOOL, 14));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, -1), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, 0), Block::get(Block::GLASS));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, 1), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y, 2), Block::get(Block::WOOL, 14));
            //Bedrock
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, -2), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, -1), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, 0), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, 1), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3($x, $y - 1, 2), Block::get(7));

            $blocks += 10;
        }
        $time = microtime(true) - $time;
        echo "[Road] Finished the road n2 in " . $time . "sec(s)!" . $blocks . "Blocks." . PHP_EOL;


        $time = microtime(true);
        $blocks = 0;
        echo "[Road] started ! " . PHP_EOL;
        for ($z = $positive; $z < $border; ++$z) {
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-2, $y, $z), Block::get(Block::WOOL, 14));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-1, $y, $z), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(0, $y, $z), Block::get(Block::GLASS));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(1, $y, $z), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(2, $y, $z), Block::get(Block::WOOL, 14));
            //Bedrock
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-2, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-1, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(0, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(1, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(2, $y - 1, $z), Block::get(7));

            $blocks += 10;
        }
        $time = microtime(true) - $time;
        echo "[Road] Finished the road n3 in " . $time . "sec(s)!" . $blocks . "Blocks." . PHP_EOL;


        $time = microtime(true);
        $blocks = 0;
        echo "[Road] started ! " . PHP_EOL;
        for ($z = $negative; $z > ($border - ($border * 2)); --$z) {
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-2, $y, $z), Block::get(Block::WOOL, 14));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-1, $y, $z), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(0, $y, $z), Block::get(Block::GLASS));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(1, $y, $z), Block::get(Block::WOOD));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(2, $y, $z), Block::get(Block::WOOL, 14));
            //Bedrock
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-2, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(-1, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(0, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(1, $y - 1, $z), Block::get(7));
            Myriad::getInstance()->getServer()->getDefaultLevel()->setBlock(new Vector3(2, $y - 1, $z), Block::get(7));

            $blocks += 10;
        }
        $time = microtime(true) - $time;
        echo "[Road] Finished the road n4 in " . $time . "sec(s)!" . $blocks . "Blocks." . PHP_EOL;

    }

    /**
     * @return bool
     */
    public function isRestarting() : bool
    {
        return $this->restarting;
    }

    /**
     * @param bool $restarting
     */
    public function setRestarting(bool $restarting)
    {
        $this->restarting = $restarting;
    }

    /**
     * @param Block $block
     * @return bool
     */
    public function isPCBlock(Block $block) : bool
    {
        $id = $block->getId();
        $meta = $block->getDamage();
        /*
        if($id == 125){
            $block->getLevel()->setBlock($block->asVector3(), Block::get(157));
            echo "+1 125" . PHP_EOL;
            return false;
        }
        if($id == 126){
            $block->getLevel()->setBlock($block->asVector3(), Block::get(158));
            echo "+1 126" . PHP_EOL;
            return false;
        }*/
        return isset($this->blocks["$id:$meta"]);
    }

    /**
     * @param Block $block
     */
    public function convertBlock(Block $block)
    {
        $id = $block->getId();
        $meta = $block->getDamage();
        $new = ItemFactory::fromString($this->blocks["$id:$meta"]);
        $block->getLevel()->setBlock($block->asVector3(), Block::get($new->getId(), $new->getDamage()),true, true);
    }

    public function checkPCBlocks()
    {
        $start = microtime(true);
        $count = 0;
        for ($x = -100; $x < 100; ++$x){
            for($z = -100; $z < 100; ++$z){
                for ($y = 70; $y < 80; ++$y){
                    if(!Myriad::getInstance()->getServer()->getDefaultLevel()->isChunkLoaded($x, $z)){
                        Myriad::getInstance()->getServer()->getDefaultLevel()->loadChunk($x, $z);
                    }
                    $block = Myriad::getInstance()->getServer()->getDefaultLevel()->getBlockAt($x, $y, $z);

                    if($this->isPCBlock($block)){
                        $this->convertBlock($block);
                        ++$count;
                    }
                    //Myriad::getInstance()->getServer()->getDefaultLevel()->unloadChunk($x, $z);
                }
            }
        }

        for ($x = -1000; $x < 1000; ++$x){
            for ($z = -10; $z < 11; ++$z){
                for ($y = 70; $y < 80; ++$y){
                    if(!Myriad::getInstance()->getServer()->getDefaultLevel()->isChunkLoaded($x, $z)){
                        Myriad::getInstance()->getServer()->getDefaultLevel()->loadChunk($x, $z);
                    }
                    $block = Myriad::getInstance()->getServer()->getDefaultLevel()->getBlockAt($x, $y, $z);

                    if($this->isPCBlock($block)){
                        $this->convertBlock($block);
                        ++$count;
                    }
                    //Myriad::getInstance()->getServer()->getDefaultLevel()->unloadChunk($x, $z);
                }
            }
        }

        for ($z = -1000; $z < 1000; ++$z){
            for ($x = -10; $x < 11; ++$x){
                for ($y = 70; $y < 85; ++$y){
                    if(!Myriad::getInstance()->getServer()->getDefaultLevel()->isChunkLoaded($x, $z)){
                        Myriad::getInstance()->getServer()->getDefaultLevel()->loadChunk($x, $z);
                    }
                    $block = Myriad::getInstance()->getServer()->getDefaultLevel()->getBlockAt($x, $y, $z);

                    if($this->isPCBlock($block)){
                        $this->convertBlock($block);
                        ++$count;
                    }
                    //Myriad::getInstance()->getServer()->getDefaultLevel()->unloadChunk($x, $z);
                }
            }
        }

        Myriad::getInstance()->getServer()->getDefaultLevel()->save(true);

        $time = microtime(true) - $start;
        echo "[PCBlocksConverter] Finished in " . $time . " second(s)," . $count ." blocks!" . PHP_EOL;
    }


}