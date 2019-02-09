<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 25/10/2017
 * Time: 14:55
 */

namespace friscowz\hc\task\async;


use friscowz\hc\MDPlayer;
use pocketmine\scheduler\AsyncTask;

class SpawnTagTask extends AsyncTask
{
    private $player;

    /**
     * SpawnTagTask constructor.
     * @param MDPlayer $player
     */
    public function __construct(MDPlayer $player)
    {
        $this->setPlayer($player);
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {
        // TODO: Implement onRun() method.
    }

    /**
     * @return MDPlayer
     */
    public function getPlayer() : MDPlayer
    {
        return $this->player;
    }

    /**
     * @param MDPlayer $player
     */
    public function setPlayer(MDPlayer $player)
    {
        $this->player = $player;
    }
}