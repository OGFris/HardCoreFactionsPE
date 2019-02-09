<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 26/10/2017
 * Time: 13:42
 */

namespace friscowz\hc\task\async;


use pocketmine\scheduler\AsyncTask;

class SaveDataTask extends AsyncTask
{
    const ADD = 0;
    const REDUCE = 1;
    const SET = 2;

    private $player;
    private $data = "";
    private $value = 0;
    private $type = 2;

    /**
     * SaveDataTask constructor.
     * @param string $player
     * @param string $data
     * @param int $value
     * @param int $type
     */
    public function __construct(string $player, string $data, int $value, int $type = 2)
    {
        $this->setPlayer($player);
        $this->setData($data);
        $this->setValue($value);
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {
        $connection = @mysqli_connect("127.0.0.1", "root", "root", "hcf");
        $result = '0';
        if($this->getType() == self::SET){
            $result = $this->getValue();
            $connection->query("UPDATE Players SET $this->getData() = $result WHERE playername = '$this->player'");
            return;
        }

        if($this->getType() == self::ADD){
            $result = mysqli_fetch_assoc($connection->query("SELECT * FROM Players WHERE playername = '$this->player'"))[$this->getData()] + $this->getValue();
        }elseif($this->getType() == self::REDUCE){
            $result = mysqli_fetch_assoc($connection->query("SELECT * FROM Players WHERE playername = '$this->player'"))[$this->getData()] - $this->getValue();
        }

        $connection->query("UPDATE Players SET $this->getData() = $result WHERE playername = '$this->player'");
    }

    /**
     * @return int
     */
    public function getValue() : int
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value)
    {
        $this->value = $value;
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
     * @return int
     */
    public function getType() : int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }
}