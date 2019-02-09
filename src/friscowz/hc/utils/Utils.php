<?php

/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:58
 */

namespace friscowz\hc\utils;

use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class Utils
{
    const DAY_TIME = 86400;
    const PREFIX = TextFormat::BOLD . TextFormat::DARK_GRAY . "(" . TextFormat::RESET . TextFormat::RED . "LegacyHCF" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::RESET;

    /**
     * @param int $int
     * @return string
     */
    public static function intToString(int $int) : string
    {
        $m = floor($int / 60);
        $s = floor($int % 60);
        return (($m < 10 ? "0" : "") . $m . ":" . ($s < 10 ? "0" : "") . $s);
    }

    /**
     * @param int $time
     * @return string
     */
    public static function intToFullString(int $time) : string
    {
        $seconds = floor($time % 60);
        $minutes = null;
        $hours = null;
        $days = null;

        if($time >= 60){
            $minutes = floor(($time % 3600) / 60);
            if($time >= 3600){
                $hours = floor(($time % (3600 * 24)) / 3600);
                if($time >= 3600 * 24){
                    $days = floor($time / (3600 * 24));
                }
            }
        }
        return ($minutes !== null ? ($hours !== null ? ($days !== null ? ($days < 10 ? "0" : "") . "$days" . ":" : "") . ($hours < 10 ? "0" : "") . "$hours" . ":" : "") . ($minutes < 10 ? "0" : "") . "$minutes" . ":" : "") . ($seconds < 10 ? "0" : "") . "$seconds";
    }

    /**
     * @return string
     */
    public static function getPrefix() : string
    {
        return self::PREFIX;
    }

    /**
     * @param string $message
     * @return string
     */
    public static function getColors(string $message) : string
    {
        return str_replace("&k", TextFormat::OBFUSCATED, str_replace("&r",TextFormat::RESET, str_replace("&l",TextFormat::BOLD, str_replace("&o",TextFormat::ITALIC, str_replace("&f",TextFormat::WHITE, str_replace("&e",TextFormat::YELLOW, str_replace("&d",TextFormat::LIGHT_PURPLE, str_replace("&c",TextFormat::RED, str_replace("&b",TextFormat::AQUA, str_replace("&a",TextFormat::GREEN, str_replace("&0",TextFormat::BLACK, str_replace("&9",TextFormat::BLUE, str_replace("&8",TextFormat::DARK_GRAY, str_replace("&7",TextFormat::GRAY, str_replace("&6",TextFormat::GOLD, str_replace("&5",TextFormat::DARK_PURPLE, str_replace("&4",TextFormat::DARK_RED, str_replace("&3",TextFormat::DARK_AQUA, str_replace("&2",TextFormat::DARK_GREEN, str_replace("&1",TextFormat::DARK_BLUE, $message))))))))))))))))))));
    }

    /**
     * @param int $from
     * @param int $to
     * @return int
     */
    public static function getRandomNumber(int $from = 10000, int $to = PHP_INT_MAX) : int
    {
        return mt_rand($from, $to);
    }

    /**
     * @param Vector3 $vector3
     * @return string
     */
    public static function vector3AsString(Vector3 $vector3) : string
    {
        return $vector3->getX() . ":" . $vector3->getY() . ":" . $vector3->getZ();
    }
}