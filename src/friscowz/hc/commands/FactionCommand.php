<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 21:27
 */

namespace friscowz\hc\commands;

use friscowz\hc\FactionsManager;
use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\InviteTask;
use friscowz\hc\task\Teleport;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class FactionCommand extends PluginCommand
{
    private $plugin;
    /**
     * FactionCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        parent::__construct("f", $plugin);
        $this->setPlugin($plugin);
        $this->setAliases(["faction", "hcf", "fac", "factions", "team", "t", "hardcorefaction", "hardcorefactions"]);
    }

    /**
     * @return Myriad
     */
    public function getMyriad () : Myriad
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

    /**
     * @return array
     */
    public function getHelp() : array
    {
        $help = [
            "/f create " . "<name>",
            "/f disband",
            "/f leave",
            "/f claim",
            "/f sethome",
            "/f home",
            "/f stuck",
            "/f balance",
            "/f who <player>",
            "/f info <faction>",
            "/f withdraw <amount>",
            "/f deposit <amount>",
            "/f invite <name>",
            "/f kick  <name>",
            "/f map",
            "/f leader <name>",
            "/f members"
        ];
        return $help;
    }


    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute (CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof MDPlayer){
            if(!isset($args[0])){
                $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid argument ! use the command /f help to see the commands.");
            return;
            }
            switch (strtolower($args[0])){
                case "freeze":
                    if(isset($args[1]) and $sender->isOp()){
                        if(Myriad::getFactionsManager()->isFaction($args[1])){
                            Myriad::getFactionsManager()->setFrozenTime($args[1], time() + 60);
                        }
                    }
                break;

                case "help":
                case "?":
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Faction commands list!");
                    foreach ($this->getHelp() as $help){
                        $sender->sendMessage(TextFormat::GREEN . "-" . TextFormat::GRAY . $help);
                    }
                break;

                case "setdtr":
                    if($sender->isOp()){
                        if(isset($args[1]) and isset($args[2])){
                            if(Myriad::getFactionsManager()->isFaction($args[1])){
                                Myriad::getFactionsManager()->setDTR($args[1], $args[2]);
                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully set " . $args[1] . "'s DTR to $args[2] !");
                            }
                        }
                    }
                break;

                case "claim":
                    if($sender->isInFaction()){
                        if(Myriad::getFactionsManager()->getDTR($sender->getFaction()) < 1){
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't claim when your faction is raidable!");
                            return;
                        }
                        if(Myriad::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()){
                            $sender->setClaim(false);
                            $sender->setClaiming(true);
                            $sender->setStep(MDPlayer::FIRST);
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "Please tap on the first position!");
                        }
                    }
                break;

                case "create":
                case "make":
                    if($sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You are already in a faction! to leave it do /f leave.");
                        return;
                    }
                    if(!isset($args[1])){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have to chose a name for your faction! Usage: /f create <YourName>");
                        return;
                    }
                    if(!ctype_alnum($args[1])){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can only use numbers and letters!");
                        return;
                    }
                    if(strlen($args[1]) > 10){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "the name is too long!");
                        return;
                    }
                    Myriad::getFactionsManager()->createFaction($args[1], $sender);
                break;

                case "disband":
                case "delete":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do that!");
                        return;
                    }
                    if(Myriad::getFactionsManager()->getLeader($sender->getFaction()) != $sender->getName()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be the leader to do that!");
                        return;
                    }
                    Myriad::getFactionsManager()->disbandFaction($sender->getFaction());
                break;

                case "leave":
                case "quit":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do that!");
                        return;
                    }
                    foreach (Myriad::getFactionsManager()->getOnlineMembers($sender->getFaction()) as $member){
                        $member->sendMessage(Utils::getPrefix() . TextFormat::GRAY . $sender->getName() . " left the faction!");
                    }
                    $sender->removeFromFaction();
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully left your faction!");
                break;

                case "home":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do that!");
                        return;
                    }
                    if(!Myriad::getFactionsManager()->isHome($sender->getFaction())){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your faction doesn't have a home! do /f sethome to set your Faction home.");
                        return;
                    }
                    if(Myriad::getFactionsManager()->isFactionClaim($sender)){
                        if(Myriad::getFactionsManager()->getClaimer($sender->x, $sender->z) != $sender->getFaction()){
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "you are in an enemy claim! Please use /f stuck to get out.");
                            return;
                        }
                    }
                    if($sender->isPvp()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do /f home when you have PvPTimer!");
                        return;
                    }
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "Teleporting you to your faction's home in 10 secs...DON'T MOVE! ");
                    $sender->setTeleport(MDPlayer::HOME);
                    $sender->setTeleportTask(new Teleport($this->getMyriad(), $sender, Utils::getPrefix() . TextFormat::GREEN . "You have been teleported to your faction's home successfully!", 10, Myriad::getFactionsManager()->getHome($sender->getFaction())));

                break;

                case "stuck":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do that!");
                        return;
                    }
                    if(!Myriad::getFactionsManager()->isHome($sender->getFaction())){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your faction doesn't have a home! do /f sethome to set your Faction home.");
                        return;
                    }
                    if($sender->isPvp()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do /f stuck when you have PvPTimer!");
                        return;
                    }
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "Teleporting you to your faction's home in 60 secs...DON'T MOVE! ");
                    $sender->setTeleport(MDPlayer::STUCK);
                    $sender->setTeleportTask(new Teleport($this->getMyriad(), $sender, Utils::getPrefix() . TextFormat::GREEN . "You have been teleported to your faction's home successfully!", 60, Myriad::getFactionsManager()->getHome($sender->getFaction())));

                break;

                case "sethome":
                    if($sender->isInFaction()){
                        if(Myriad::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()){
                            if(!$sender->isPvp()) {
                                if($sender->isInClaim()){
                                    if(Myriad::getFactionsManager()->getClaimer($sender->x, $sender->z) == $sender->getFaction()){
                                        Myriad::getFactionsManager()->setHome($sender->getFaction(), $sender->getPosition());
                                        $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully updated your faction home.");
                                    } else {
                                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can set your faction's home only on your own claim!");
                                    }
                                } else {
                                    $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can set your faction's home only on your own claim!");
                                }
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't do that on PvPTimer!");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be leader to do that!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do that!");
                break;

                case "balance":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You aren't in a faction!");
                        return;
                    }
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Your faction balance is: " . TextFormat::YELLOW . Myriad::getFactionsManager()->getBalance($sender->getFaction()) . "$");
                break;

                case "deposit":
                    if($sender->isInFaction()){
                        if(isset($args[1])){
                            if(is_numeric($args[1])) {
                                if ($sender->getMoney() >= $args[1]) {
                                    $sender->reduceMoney($args[1]);
                                    Myriad::getFactionsManager()->addBalance($sender->getFaction(), $args[1]);
                                    $sender->sendMessage(Utils::getPrefix() . "You have successfully donated to your Faction Balance " . TextFormat::YELLOW . $args[1] . "$");
                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have that much of money!");
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid number!");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /f deposit <amount>");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do this!");
                break;
/*
                case "map":
                    Myriad::getFactionsManager()->showMap($sender);
                break;
*/
                case "info":
                    if(isset($args[1])){
                        if(Myriad::getFactionsManager()->isFaction($args[1])){
                            $fac = $args[1];
                            $count = count(Myriad::getFactionsManager()->getMembers($fac));
                            $online = Myriad::getFactionsManager()->getOnlineMembers($fac);
                            $onlines = "";
                            if(count($online) > 0){
                                foreach ($online as $player){
                                    $onlines .= $player->getName() . ", ";
                                }
                            } else {
                                $onlines = "none";
                            }
                            $dtr = Myriad::getFactionsManager()->getDTR($fac);
                            $coords = "not set";
                            if(Myriad::getFactionsManager()->isHome($fac)){
                                $home = Myriad::getFactionsManager()->getHome($fac);
                                $coords = " : " . $home->getX() . " : " . $home->getY() . " : " . $home->getZ();
                            }
                            $sender->sendMessage(TextFormat::YELLOW . "Faction: " . TextFormat::BLUE . $fac . TextFormat::GRAY . " [" . $count . "/10]");
                            $sender->sendMessage(TextFormat::YELLOW . "Online: " . TextFormat::GREEN . $onlines . TextFormat::BLUE . "(" . count($online) . "/" . $count . ")");
                            $sender->sendMessage(TextFormat::YELLOW . "Leader: " . TextFormat::GREEN . Myriad::getFactionsManager()->getLeader($fac));
                            $sender->sendMessage(TextFormat::YELLOW . "DTR: " . TextFormat::GREEN . $dtr . "/" . Myriad::getFactionsManager()->getMaxDTR($fac));
                            $sender->sendMessage(TextFormat::YELLOW . "Home: " . TextFormat::GREEN . $coords);
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This faction doesn't exist!");
                    } else {
                        if ($sender->isInFaction()) {
                            $fac = $sender->getFaction();
                            $count = count(Myriad::getFactionsManager()->getMembers($fac));
                            $online = Myriad::getFactionsManager()->getOnlineMembers($fac);
                            $onlines = "";
                            if (count($online) > 0) {
                                foreach ($online as $player) {
                                    $onlines .= $player->getName() . ", ";
                                }
                            } else {
                                $onlines = "none";
                            }
                            $dtr = Myriad::getFactionsManager()->getDTR($fac);
                            $coords = "not set";
                            $checkfrozen = false;
                            $frozentime = 0;
                            if (Myriad::getFactionsManager()->isFrozen($fac)) {
                                $checkfrozen = true;
                                $frozentime = Myriad::getFactionsManager()->getFrozenTimeLeft($fac);
                            }
                            if (Myriad::getFactionsManager()->isHome($fac)) {
                                $home = Myriad::getFactionsManager()->getHome($fac);
                                $coords = $home->getX() . ":" . $home->getY() . ":" . $home->getZ();
                            }
                            $sender->sendMessage(TextFormat::YELLOW . "Faction: " . TextFormat::BLUE . $fac . TextFormat::GRAY . " [" . $count . "/10]");
                            //$sender->sendMessage(TextFormat::YELLOW . "Members: " . TextFormat::GREEN . count($online) . "/" . $count);
                            $sender->sendMessage(TextFormat::YELLOW . "Online: " . TextFormat::GREEN . $onlines . TextFormat::BLUE . "(" . count($online) . "/" . $count . ")");
                            $sender->sendMessage(TextFormat::YELLOW . "Leader: " . TextFormat::GREEN . Myriad::getFactionsManager()->getLeader($fac));
                            $sender->sendMessage(TextFormat::YELLOW . "DTR: " . TextFormat::GREEN . $dtr . "/" . Myriad::getFactionsManager()->getMaxDTR($fac));
                            if ($checkfrozen) {
                                $sender->sendMessage(TextFormat::YELLOW . "DTR Freeze: " . TextFormat::GREEN . Utils::intToString($frozentime));
                            }
                            $sender->sendMessage(TextFormat::YELLOW . "Home: " . TextFormat::GREEN . $coords);
                            $sender->sendMessage(TextFormat::YELLOW . "Balance: " . TextFormat::GOLD . Myriad::getFactionsManager()->getBalance($fac) . "$");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /f info <faction>");
                    }
                break;

                case "chat":
                case "c":
                    if($sender->isInFaction()){
                        if($sender->getChat() == MDPlayer::PUBLIC){
                            $sender->setChat(MDPlayer::FACTION);
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully switched your chat to faction!");
                        } else {
                            $sender->setChat(MDPlayer::PUBLIC);
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully switched your chat to public!");
                        }
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do this!");
                break;

                case "who":
                    if(isset($args[1])){
                        $player = $sender->getServer()->getPlayer($args[1]);
                        if($player){
                            if($player instanceof MDPlayer){
                                if($player->isInFaction()){
                                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . $player->getName() . "'s faction is: " . $player->getFaction());
                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This players doesn't have a faction!");
                            }
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This player isn't online!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /f who <player>");

                break;

                case "withdraw":
                    if($sender->isInFaction()){
                        if(Myriad::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()) {
                            if (isset($args[1])) {
                                if (is_numeric($args[1])) {
                                    if (Myriad::getFactionsManager()->getBalance($sender->getFaction()) >= $args[1]) {
                                        $sender->addMoney($args[1]);
                                        Myriad::getFactionsManager()->reduceBalance($sender->getFaction(), $args[1]);
                                        $sender->sendMessage(Utils::getPrefix() . "You have successfully withdraw from your Faction balance " . TextFormat::YELLOW . $args[1] . "$");
                                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have that much of money!");
                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid number!");
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /f withdraw <amount>");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be the leader to do this!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do this!");
                break;

                case "members":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You aren't in a faction!");
                        return;
                    }
                    $fac = $sender->getFaction();
                    $members = Myriad::getFactionsManager()->getMembers($fac);
                    $text = "";
                    foreach ($members as $member){
                        $text .= $member . ", ";
                    }
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Your faction's members are: " . TextFormat::GRAY . $text);

                break;

                case "kick":
                    if(!$sender->isInFaction()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You aren't in a faction!");
                        return;
                    }
                    if(!Myriad::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be the leader to do this!");
                        return;
                    }
                    if(!isset($args[1])){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid name! please do /f kick <name> to kick someone from your faction.");
                        return;
                    }
                    if(!Myriad::getFactionsManager()->isMember($sender->getFaction(), $args[1])){
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This player isn't in your faction!");
                        return;
                    }
                    Myriad::getFactionsManager()->kick($args[1]);
                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully kicked " . $args[1] . " from your faction!");
                break;

                case "invite":
                    if($sender->isInFaction()) {
                        if (Myriad::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()){
                            if (isset($args[1])) {
                                if ($this->getMyriad()->getServer()->getPlayer($args[1]) != null) {
                                    $player = $this->getMyriad()->getServer()->getPlayer($args[1]);
                                    if ($player instanceof MDPlayer) {
                                        if (count(Myriad::getFactionsManager()->getMembers($sender->getFaction())) < 10) {
                                            if (!$player->isInFaction()) {
                                                if (!$player->isInvited()) {
                                                    $player->setLastinvite($sender->getFaction());
                                                    $player->setInvited(true);
                                                    $player->setTask(new InviteTask($this->getMyriad(), $player));
                                                    $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . $sender->getName() . "sent you an invitation to join his faction, you have 30secs left to do /f accept and accept his invitation!");
                                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This player have been already invited by another faction! Please wait few seconds then try again.");
                                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This player is in faction!");
                                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your Faction is full!");
                                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid Player!");
                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This player isn't online!");
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /f invite <player>");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be the leader to do this!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do this!");
                break;

                case "accept":
                    if($sender->isInvited()){
                        $fac = $sender->getLastinvite();
                        $sender->getTask()->cancel();
                        if(count(Myriad::getFactionsManager()->getMembers($fac)) < 10){
                            $sender->addToFaction($fac);
                            $sender->setInvited(false);
                            foreach (Myriad::getFactionsManager()->getOnlineMembers($fac) as $member){
                                $member->sendMessage(Utils::getPrefix() . TextFormat::GREEN . $sender->getName() . " has joined the faction.");
                            }
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't join this faction because its full!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have no invitation!");
                break;

                case "leader":
                case "setleader":
                    if($sender->isInFaction()){
                        if(Myriad::getFactionsManager()->getLeader($sender->getFaction()) == $sender->getName()){
                            if(isset($args[1])){
                                $player = $this->getMyriad()->getServer()->getPlayer($args[1]);
                                if($player and $player instanceof MDPlayer){
                                    if(Myriad::getFactionsManager()->isMember($sender->getFaction(), $player->getName())) {
                                        $player->addToFaction($sender->getFaction(), FactionsManager::LEADER);
                                        $sender->addToFaction($sender->getFaction(), FactionsManager::OFFICER);
                                        $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully updated your faction's leader and you turned into an officer!");
                                        $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You are now the leader of your faction!");
                                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "The Player must be in the same faction as you!");
                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "The player isn't online!");
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /f leader <player>");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be the leader to do this!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must be in a faction to do this!");
                break;

                case "deny":
                    if($sender->isInvited()){
                        $fac = $sender->getLastinvite();
                        $sender->getTask()->cancel();
                        $sender->setInvited(false);
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully denied the invitation!");
                        foreach (Myriad::getFactionsManager()->getOnlineMembers($fac) as $member){
                            $member->sendMessage(Utils::getPrefix() . TextFormat::RED . $sender->getName() . " has denied the invitation to join this faction.");
                        }
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have no invitation!");
                break;

            }
        } else {
            echo "try succ" . PHP_EOL;
            Myriad::getFactionsManager()->getDb()->exec("SELECT * FROM claims");
        }
    }
}