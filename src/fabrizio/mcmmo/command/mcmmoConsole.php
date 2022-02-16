<?php

namespace fabrizio\mcmmo\command;

use pocketmine\command\CommandSender;
use fabrizio\mcmmo\mcMMO;
use fabrizio\mcmmo\command\mcmmoPlayer;
use fabrizio\mcmmo\menu\Menu;

class mcmmoConsole {

    public function __construct(mcMMO $plugin, CommandSender $sender, array $args) {
        $prefix = $plugin->prefix ?? "§a[§6mcMMO§a]§r";
        if(!isset($args[0])){
            $msg = "$prefix =================================\n$prefix » Name: mcMMO v.1.0\n$prefix » authors: @Fabrizio\n$prefix » type 'mcmmo <help or ?> <page>'\n$prefix =================================";
            $sender->sendMessage($msg);
            return true;
        }
        switch($args[0]){
            case "help":
            case "?":
                $sender->sendMessage("$prefix =========== Show mcMMO help. page (1/1) ===========");
                $sender->sendMessage("$prefix /mcmmo | §7To get information this plugin.");
                $sender->sendMessage("$prefix /mcmmo addlevel <player> <skill> <level> | §7" . "Added skill level to players.");
                $sender->sendMessage("$prefix /mcmmo addxp <player> <skill> <xp> | §7" . "Added skill xp to players.");
                $sender->sendMessage("$prefix /mcmmo backups | §7Make a file backup in plugin-data.");
                $sender->sendMessage("$prefix /mcmmo check <player> <skill> | §7" . "Check player skill information.");
                $sender->sendMessage("$prefix /mcmmo reducelevel <player> <skill> <level> | §7" . "Reducing player skill level.");
                $sender->sendMessage("$prefix /mcmmo reducexp <player> <skill> <xp> | §7" . "Reducing player skill xp.");
                $sender->sendMessage("$prefix /mcmmo skills | §7" . "Show list skill on §6mcMMO§f.");
                $sender->sendMessage("$prefix ===================================================");
                return true;
            break;
            case "leaderboard":
            case "top":
                $count = 0;
                if(!isset($args[1])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo $args[0] <skill>.");
                    return true;
                }
                if(!in_array(strtolower($args[1]), $plugin->skills)){
                    $sender->sendMessage("$prefix §cSkill $args[2] not found.");
                    return true;
                }
                if(!isset($args[2])){
                    $count = 10;
                }
                if(!is_numeric($args[2])){
                    $count = 10;
                }
                if($args[2] == 0){
                    $count = 10;
                }
                $count = $args[2];
                if($plugin->database()->getAll() == []){
                    $sender->sendMessage("$prefix §cDatabase is clear, sorry! :(");
                    return true;
                }
                $username = $plugin->getUsernames();
                $array = [];
                foreach($plugin->database()->getAll() as $data){
                    $out = [$data[$args[1]]["level"]];
                    $array = array_merge($array, $out);
                }
                $i=1;
                $skill = ucfirst($args[1]);
                $sender->sendMessage("$prefix §f====== §eShowing Leaderboard mcMMO. §f======\n$prefix §7» §6Skill§f: §a{$skill}§f.\n\n");
                foreach($array as $key => $values){
                    if($i == $count){
                        $sender->sendMessage("$prefix §f========================================");
                        return true;
                    }
                    $max = max($array);
                    $keys = array_search($max, $array);
                    $key = $username[$keys];
                    $sender->sendMessage("$prefix §e{$i}§7. §a{$key}§7: §6{$max}§7.");
                    unset($array[$keys]);
                    unset($username[$keys]);
                    if(sizeof($array) >0)
                    if(!in_array($max,$array))
                    $i++;
                }
                $sender->sendMessage("$prefix §f========================================");
                return true;
            break;
            case "backups":
                $plugin->createBackup();
                $date = date("jMY");
                $sender->sendMessage("$prefix §aSuccessfully making a file backup in plugin_data/mcMMO/{$date}.yml");
                return true;
            break;
            case "skills":
                $array = [];
                foreach($plugin->skills as $skill){
                    $array[] = ucfirst($skill);
                }
                $string = implode(", ", $array);
                $count = count($array);
                $sender->sendMessage("$prefix §fSkills ({$count}): §6{$skill}.");
                return true;
            break;
            case "addlevel":
                if(!isset($args[1])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo addlevel <player> <skill> <level>");
                    return true;
                }
                if(!in_array($args[1], $plugin->getUsernames())){
                    $sender->sendMessage("$prefix §cPlayer $args[1] is not found in database.");
                    return true;
                }
                if(!isset($args[2])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo addlevel $args[1] <skill> <level>");
                    return true;
                }
                if(!in_array(strtolower($args[2]), $plugin->skills)){
                    $sender->sendMessage("$prefix §cSkill $args[2] not found.");
                    return true;
                }
                if(!isset($args[3])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo addlevel $args[1] $args[2] <level>");
                    return true;
                }
                if(!is_numeric($args[3])){
                    $sender->sendMessage("$prefix §cEnter number only.");
                    return true;
                }
                $uid = $plugin->getXuids()[array_search($args[1], $plugin->getXuids())];
                $plugin->addLevel(strtolower($args[2]), $args[3], $uid);
                $skill = ucfirst($args[2]);
                $name = $args[1];
                $sender->sendMessage("$prefix §aSuccessfully added $args[3] Level $skill to $name.");
                return true;
            break;
            case "reducelevel":
                if(!isset($args[1])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo reducelevel <player> <skill> <level>");
                    return true;
                }
                if(!in_array($args[1], $plugin->getUsernames())){
                    $sender->sendMessage("$prefix §cPlayer $args[1] is not found in database.");
                    return true;
                }
                if(!isset($args[2])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo reducelevel $args[1] <skill> <level>");
                    return true;
                }
                if(!in_array(strtolower($args[2]), $plugin->skills)){
                    $sender->sendMessage("$prefix §cSkill $args[2] not found.");
                    return true;
                }
                if(!isset($args[3])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo reducelevel $args[1] $args[2] <level>");
                    return true;
                }
                if(!is_numeric($args[3])){
                    $sender->sendMessage("$prefix §cEnter number only.");
                    return true;
                }
                $uid = $plugin->getXuids()[array_search($args[1], $plugin->getXuids())];
                $plugin->reduceLevel(strtolower($args[2]), $args[3], $uid);
                $skill = ucfirst($args[2]);
                $name = $args[1];
                $sender->sendMessage("$prefix §aSuccessfully distract $args[3] Level $skill from $name.");
                return true;
            break; 
            case "addxp":
                if(!isset($args[1])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo addxp <player> <skill> <xp>");
                    return true;
                }
                if(!in_array($args[1], $plugin->getUsernames())){
                    $sender->sendMessage("$prefix §cPlayer $args[1] is not found in database.");
                    return true;
                }
                if(!isset($args[2])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo addxp $args[1] <skill> <xp>");
                    return true;
                }
                if(!in_array(strtolower($args[2]), $plugin->skills)){
                    $sender->sendMessage("$prefix §cSkill $args[2] not found.");
                    return true;
                }
                if(!isset($args[3])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo addxp $args[1] $args[2] <xp>");
                    return true;
                }
                if(!is_numeric($args[3])){
                    $sender->sendMessage("$prefix §cEnter number only.");
                    return true;
                }
                $uid = $plugin->getXuids()[array_search($args[1], $plugin->getXuids())];
                $plugin->addXp(strtolower($args[2]), $args[3], $uid);
                $skill = ucfirst($args[2]);
                $name = $args[1];
                $sender->sendMessage("$prefix §aSuccessfully added $args[3] XP $skill to $name.");
                return true;
            break;
            case "reducexp":
                if(!isset($args[1])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo reducexp <player> <skill> <xp>");
                    return true;
                }
                if(!in_array($args[1], $plugin->getUsernames())){
                    $sender->sendMessage("$prefix §cPlayer $args[1] is not found in database.");
                    return true;
                }
                if(!isset($args[2])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo reducexp $args[1] <skill> <xp>");
                    return true;
                }
                if(!in_array(strtolower($args[2]), $plugin->skills)){
                    $sender->sendMessage("$prefix §cSkill $args[2] not found.");
                    return true;
                }
                if(!isset($args[3])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo reducexp $args[1] $args[2] <xp>");
                    return true;
                }
                if(!is_numeric($args[3])){
                    $sender->sendMessage("$prefix §cEnter number only.");
                    return true;
                }
                $uid = $plugin->getXuids()[array_search($args[1], $plugin->getXuids())];
                $plugin->reduceXp(strtolower($args[2]), $args[3], $uid);
                $skill = ucfirst($args[2]);
                $name = $args[1];
                $sender->sendMessage("$prefix §aSuccessfully distract $args[3] XP $skill from $name.");
                return true;
            break;
            case "check":
                if(!isset($args[1])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo check <player> <skill>");
                    return true;
                }
                if(!$plugin->getServer()->getPlayerByPrefix($args[1]) instanceof Player){
                    $sender->sendMessage("$prefix §cPlayer $args[1] is not online player.");
                    return true;
                }
                if(!isset($args[2])){
                    $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo check $args[1] <skill>");
                    return true;
                }
                if(!in_array(strtolower($args[2]), $plugin->skills)){
                    $sender->sendMessage("$prefix §cSkill $args[2] not found.");
                    return true;
                }
                $uid = $plugin->getXuids()[array_search($args[1], $plugin->getXuids())];
                $skill = strtolower($args[2]);
                $xp = $plugin->getXp($target, $skill);
                $level = $plugin->getLevel($target, $skill);
                $targetxp = $level * 100;
                $skillName = ucfirst($skill);
                $sender->sendMessage("$prefix §r======= mcMMO Player Information =======\n$prefix §r» Name: {$name}\n$prefix §r» Skill: {$skillName}\n$prefix §r» Level: {$level} ({$xp}/{$targetxp})\n$prefix §r» Rank: Next Update!.\n$prefix §r========================================");
                return true;
            break;
            default:
                $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo help to show you list command mcMMO.");
                return true;
            break;
        }
        return true;
    }
}