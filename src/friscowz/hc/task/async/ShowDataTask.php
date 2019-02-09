<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 26/10/2017
 * Time: 14:21
 */

namespace friscowz\hc\task\async;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ShowDataTask extends AsyncTask
{

    private $player;
    private $data = "";
    private $message = "{value}";

    /**
     * ShowDataTask constructor.
     * @param string $player
     * @param string $data
     * @param string $message
     */
    public function __construct(string $player, string $data, string $message = "{value}")
    {
        $this->setPlayer($player);
        $this->setData($data);
        $this->setMessage($message);
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {
        $connection = @mysqli_connect("127.0.0.1", "root", "root", "hcf");

        $this->setResult(mysqli_fetch_assoc($connection->query('SELECT * FROM Players WHERE playername = ' . $this->getPlayer())));
    }

    /**
     * @param Server $server
     */
    public function onCompletion(Server $server)
    {
        if($this->getData() == "Banned"){
            if($this->getResult()[$this->getData()] == 1){
                $server->getPlayer($this->getPlayer())->kick(TextFormat::RED . "You are banned from Myriad Network!");
            }
            return;
        }
        $server->getPlayer($this->getPlayer())->sendMessage($this->translateMessage($this->getResult()[$this->getData()]));
    }

    /**
     * @return string
     */
    public function getPlayer() : string
    {
        return $this->player;
    }

    /**
     * @param string $player
     */
    public function setPlayer(string $player)
    {
        $this->player = $player;
    }

    /**
     * @param int $value
     */
    public function translateMessage(int $value)
    {
        $this->setMessage(str_replace("{value}", $value, $this->getMessage()));
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getData() : string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }
}