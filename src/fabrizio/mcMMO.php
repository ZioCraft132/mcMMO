<?php

namespace fabrizio\mcmmo;

use pocketmine\plugin\PluginBase;
use fabrizio\mcmmo\mcMMOListener;
use pocketmine\player\Player;
use fabrizio\mcmmo\command\mcmmoConsole;
use fabrizio\mcmmo\command\mcmmoPlayer;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\utils\Config;

class mcMMO extends PluginBase {
    
    public const ARCHERY = "archery";
    public const ACROBATIC = "acrobatic";
    public const LUMBERJACK = "lumberjack";
    public const MINER = "miner";
    public const HERBALISM = "herbalism";
    public const BUILDER = "builder";
    public const FIGHTER = "builder";
    public const FISHING = "fishing";

    private $instance;
    public $plugin = [];
    public $skills = ["archery", "acrobatic", "lumberjack", "miner", "herbalism", "builder", "fighter", "fishing"];
    public $prefic = "§a[§6mcMMO§a]";
    private $database;
    private $xuids;
    public $verification;
    private $skinTag;
    public $prefix = "§a[§6mcMMO§a]";

    public function onEnable() : void {
        ## Load Listener
        $this->getServer()->getPluginManager()->registerEvents(new mcMMOListener($this), $this);
        ## Logger
        $this->getLogger()->info("$this->prefix §6mc§aMMO §bby §2@Fabrizio §benabled.");
        $this->saveResource("database/databases.yml");
        $this->saveResource("database/xuids.yml");
        $this->saveResource("backup/readme.txt");
        $this->database = new Config($this->getDataFolder() . "database/databases.yml", Config::YAML);
        $this->xuids = new Config($this->getDataFolder() . "database/xuids.yml", Config::YAML);
    }

    public function onDisable() : void {
        $this->getLogger()->info("$this->prefix §6mc§aMMO §bby §2@Fabrizio §bdisabled.");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        if($cmd == "mcmmo"){
            if(!$sender instanceof Player) {
                new mcmmoConsole($this, $sender, $args);
                return true;
            }
            new mcmmoPlayer($this, $sender, $args);
            return true;
        }
    }

    public function database() {
        return $this->database;
    }

    public function createBackup() {
        $date = date("jMY");
        $backup = new Config($this->getDataFolder() . "backup/$date.yml", Config::YAML);
        $backup->setAll($this->database->getAll());
        return true;
    }

    public function setXuids(string $name, $xuid) : bool {
        $this->xuids->setNested($name.".xuid", $xuid);
        $this->xuids->save();
        return true;
    }

    public function getUsernames() : array {
        $array = [];
        foreach($this->database->getAll() as $data){
            $out = [$data["username"] ?? ""];
            $array = array_merge($array, $out);
        }
        return $array;
    }

    public function getXuids() : array {
        $array = [];
        foreach($this->xuids->getAll() as $data){
            $out = [$data["xuid"] ?? ""];
            $array = array_merge($array, $out);
        }
        return $array;
    }

    /**
     * For next project, we will adding floating text.
     */
    public function leaderboard(string $skill, int $int = 10) : array {
        if(!in_array($skill, $this->skills)) {
            return [];
        }
        $username = $this->getUsernames();
        $array = [];
        foreach($this->database->getAll() as $data){
            $out = [$data[$skill]["level"]];
            $array = array_merge($array, $out);
        }
        $i=1;
        $strings = [];
        foreach($array as $key => $values){
            if($i < $int){
                $max = max($array);
                $keys = array_search($max, $array);
                $key = $username[$keys];
                $string = [
                    "§f» §b{$i} §a{$key} §6Level§f: §b{$max}§f.§f «"
                ];
                $strings = array_merge($strings, $string);
                unset($array[$keys]);
                unset($username[$keys]);
                if(sizeof($array) >0)
                if(!in_array($max,$array))
                $i++;
            }
        }
        return $strings;
    }

    public static function getInstance() : mcMMO {
        return self::$instance;
    }

    public function getXp(Player $player, string $skill) : int {
        if(!$player instanceof Player){
            return 0;
        }
        if(!in_array($skill, $this->skills)) {
            return 0;
        }
        $uuid = $player->getXuid();
        $ret = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        return $ret;
    }
    
    public function getLevel(Player $player, string $skill) : int {
        if(!$player instanceof Player){
            return 0;
        }
        if(!in_array($skill, $this->skills)) {
            return 0;
        }
        $uuid = $player->getXuid();
        $ret = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        return $ret;
    }

    public function getRank(Player $player, string $skill) : string {
        if(!$player instanceof Player){
            return 0;
        }
        if(!in_array($skill, $this->skills)) {
            return 0;
        }
        $uuid = $player->getXuid();
        $ret = (int) $this->database->get($uuid)[$skill]["rank"] ?? "No Rank";
        return $ret;
    }

    public function addLevel(string $skill, int $count, $player) {
        if(!$player instanceof Player){
            if(!in_array($player, $this->getXuids())){
                $this->getLogger()->info("$this->prefix §cFailed to catch xuids from database.");
                return true;
            }
            if(!in_array($skill, $this->skills)){
                $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
                return true;
            }
            $uuid = $player;
            $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
            $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
            $this->database->setNested("$uuid.$skill.level", $level + $count);
            $this->database->save();
            return true;
        }
        if(!in_array($skill, $this->skills)){
            $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
            return true;
        }
        $uuid = $player->getXuid();
        $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        $this->database->setNested("$uuid.$skill.level", $level + $count);
        $this->database->save();
        return true;
    }
    
    public function reduceLevel(string $skill, int $count, $player) {
        if(!$player instanceof Player){
            if(!in_array($player, $this->getXuids())){
                $this->getLogger()->info("$this->prefix §cFailed to catch xuids from database.");
                return true;
            }
            if(!in_array($skill, $this->skills)){
                $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
                return true;
            }
            $uuid = $player;
            $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
            $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
            $this->database->setNested("$uuid.$skill.level", $level - $count);
            $this->database->save();
            return true;
        }
        if(!in_array($skill, $this->skills)){
            $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
            return true;
        }
        $uuid = $player->getXuid();
        $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        $this->database->setNested("$uuid.$skill.level", $level - $count);
        $this->database->save();
        return true;
    }

    public function setLevel(string $skill, int $count, Player $player) {
        if(!$player instanceof Player){
            $this->getLogger()->info("$this->prefix §cTrying to access player.");
            return true;
        } 
        if(!in_array($skill, $this->skills)){
            $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
            return true;
        }
        $uuid = $player->getXuid();
        $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        $this->database->setNested("$uuid.$skill.level", $count);
        $this->database->save();
        return true;
    }

    public function addXp(string $skill, int $count, $player) {
        if(!$player instanceof Player){
            if(!in_array($player, $this->getXuids())){
                $this->getLogger()->info("$this->prefix §cFailed to catch xuids from database.");
                return true;
            }
            if(!in_array($skill, $this->skills)){
                $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
                return true;
            }
            $uuid = $player;
            $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
            $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
            $this->database->setNested("$uuid.$skill.xp", $xp + $count);
            $this->database->save();
            return true;
        }
        if(!in_array($skill, $this->skills)){
            $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
            return true;
        }
        $uuid = $player->getXuid();
        $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        $this->database->setNested("$uuid.$skill.xp", $xp + $count);
        $this->database->save();
        return true;
    }
    
    public function reduceXp(string $skill, int $count, $player) {
        if(!$player instanceof Player){
            if(!in_array($player, $this->getXuids())){
                $this->getLogger()->info("$this->prefix §cFailed to catch xuids from database.");
                return true;
            }
            if(!in_array($skill, $this->skills)){
                $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
                return true;
            }
            $uuid = $player;
            $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
            $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
            $this->database->setNested("$uuid.$skill.xp", $xp - $count);
            $this->database->save();
            return true;
        }
        if(!in_array($skill, $this->skills)){
            $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
            return true;
        }
        $uuid = $player->getXuid();
        $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        $this->database->setNested("$uuid.$skill.xp", $xp - $count);
        $this->database->save();
        return true;
    }

    public function setXp(string $skill, int $count, Player $player) {
        if(!$player instanceof Player){
            $this->getLogger()->info("$this->prefix §cTrying to access player.");
            return true;
        }
        if(!in_array($skill, $this->skills)){
            $this->getLogger()->info("$this->prefix §cFailed to trying to saving database.");
            return true;
        }
        $uuid = $player->getXuid();
        $xp = (int) $this->database->get($uuid)[$skill]["xp"] ?? 0;
        $level = (int) $this->database->get($uuid)[$skill]["level"] ?? 0;
        $this->database->setNested("$uuid.$skill.xp", $count);
        $this->database->save();
        return true;
    }
}
