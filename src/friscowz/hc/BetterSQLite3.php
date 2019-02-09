<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/16/2017
 * Time: 11:14 PM
 */

namespace friscowz\hc;


use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class BetterSQLite3 extends \SQLite3
{
    public static $filename = "";

    /**
     * BetterSQLite3 constructor.
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct($filename);
        self::$filename = $filename;
        $this->busyTimeout(5000);
        $this->exec('PRAGMA journal_mode = wal;', true);
    }

    public function exec($query, $force = false)
    {
        if ($force === true){
            parent::exec($query);
        } else {
            Server::getInstance()->getScheduler()->scheduleAsyncTask(new ExecQuery($query, self::$filename));
        }
    }
}

class ExecQuery extends AsyncTask{

    private $query, $filename;

    public function __construct($query, $filename)
    {
        $this->query = $query;
        $this->filename = $filename;
    }

    public function onRun()
    {
        $sqlite = new \SQLite3($this->filename);
        $sqlite->exec($this->query);
    }
}