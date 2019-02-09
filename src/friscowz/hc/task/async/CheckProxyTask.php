<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/11/2017
 * Time: 16:54
 */

namespace friscowz\hc\task\async;


use friscowz\hc\utils\Utils;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class CheckProxyTask extends AsyncTask
{
    private $email = "legacyhcfs@gmail.com";
    private $name = "";
    private $ip;

    /**
     * CheckProxyTask constructor.
     * @param string $name
     * @param string $ip
     */
    public function __construct(string $name, string $ip)
    {
        $this->setName($name);
        $this->setIP($ip);
        $this->setEmail("email".mt_rand(0, 10).mt_rand(0, 10).mt_rand(0, 10).mt_rand(0, 10)."@".["gmail.com", "yahoo.com", "hotmail.com"][mt_rand(0, 2)]);
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {
        $api = "http://legacy.iphub.info/api.php?ip=" . $this->getIP() . "&showtype=4&email=" . $this->getEmail();
        $api = json_decode(file_get_contents($api));
        $check = $api->proxy == 1;
        $this->setResult(
            [
                "check" => $check,
                "name" => $this->getName()
            ]
        );
    }

    /**
     * @param Server $server
     */
    public function onCompletion(Server $server)
    {
        if($this->getResult()["check"]){
            $player = $server->getPlayerExact($this->getResult()["name"]);
            if($player){
                $msg = Utils::getPrefix() . TextFormat::RED . "Please disable your VPN to play on the server.";
                $player->kick($msg, false);
                $server->broadcastMessage(Utils::getPrefix() . TextFormat::RED . $player->getName() . " couldn't join the server because he was using a VPN!");
            }
        }
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIP() : string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIP(string $ip)
    {
        $this->ip = $ip;
    }
}