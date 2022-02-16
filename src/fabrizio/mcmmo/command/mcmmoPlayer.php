<?php

namespace fabrizio\mcmmo\command;

use fabrizio\mcmmo\mcMMO;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use fabrizio\mcmmo\menu\{Menu, Profile};

class mcmmoPlayer {

    public function __construct(mcMMO $plugin, CommandSender $sender, array $args) {
        $prefix = $plugin->prefix ?? "§a[§6mcMMO§a]§r";
        if(!$sender->hasPermission("mcmmo.command.mcmmo")){
            $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
            return true;
        }
        if(!isset($args[0])){
            new Menu($plugin, $sender);
            return true;
        }
        switch($args[0]){
            case "help":
            case "?":
                if(!$sender->hasPermission("mcmmo.command.help")){
                    $sender->sendMessage("$prefix =========== Show mcMMO help. page (1/1) ===========");
                    $sender->sendMessage("$prefix /mcmmo | §7Open menu mcmmo.");
                    $sender->sendMessage("$prefix /mcmmo menu | §7Open menu mcmmo.");
                    $sender->sendMessage("$prefix /mcmmo profile | §7Show profile mcMMO.");
                    $sender->sendMessage("$prefix /mcmmo skills | §7Show list skill on §6mcMMO§f.");
                    $sender->sendMessage("$prefix =================================================");
                    return true;
                }
                $sender->sendMessage("$prefix =========== Show mcMMO help. page (1/1) ===========");
                $sender->sendMessage("$prefix /mcmmo addlevel <player> <skill> <level> | §7" . "Added skill level to players.");
                $sender->sendMessage("$prefix /mcmmo addxp <player> <skill> <xp> | §7" . "Added skill xp to players.");
                $sender->sendMessage("$prefix /mcmmo menu | §7Open menu §6mcMMO§7.");
                $sender->sendMessage("$prefix /mcmmo profile <player> | §7Show profile player §6mcMMO§7.");
                $sender->sendMessage("$prefix /mcmmo reducelevel <player> <skill> <level> | §7" . "Reducing player skill level.");
                $sender->sendMessage("$prefix /mcmmo reducexp <player> <skill> <xp> | §7" . "Reducing player skill xp.");
                $sender->sendMessage("$prefix /mcmmo skills | §7" . "Show list skill on §6mcMMO§f.");
                $sender->sendMessage("$prefix /mcmmo version | §7" . "Check version mcMMO.");
                $sender->sendMessage("$prefix =================================================");
                return true;
            break;
            case "menu":
                new Menu($plugin, $sender);
                return true;
            break;
            case "profile":
                if(!isset($args[1])){
                    new Profile($plugin, $sender);
                    return true;
                }
                if(!$sender->hasPermission("mcmmo.command.profile.player")){
                    $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
                    return true;
                }
                $target = $plugin->getServer()->getPlayerByPrefix($args[1]);
                if($target instanceof Player){
                    $sender->sendMessage("$prefix §cPlayer $args[1] is not online player.");
                    return true;
                }
                new Profile($plugin, $sender, $target);
                return true;
            break;
            case "version":
                $version = $plugin->getDescription()->getVersion();
                $sender->sendMessage("$prefix §6mcMMO §fis running in version §b{$version}.");
                return true;
            break;
            case "skills":
                if(!$sender->hasPermission("mcmmo.command.addlevel")){
                    $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
                    return true;
                }
                $string = ucwords(implode(", ", $plugin->skills));
                $count = count($plugin->skills);
                $sender->sendMessage("$prefix §fSkills ({$count}): §6{$string}.");
                return true;
            break;
            case "addlevel":
                if(!$sender->hasPermission("mcmmo.command.addlevel")){
                    $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
                    return true;
                }
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
                if(!$sender->hasPermission("mcmmo.command.reducelevel")){
                    $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
                    return true;
                }
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
                if(!$sender->hasPermission("mcmmo.command.addxp")){
                    $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
                    return true;
                }
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
                if(!$sender->hasPermission("mcmmo.command.reducexp")){
                    $sender->sendMessage("$prefix §cYou don't have permission to use this command.");
                    return true;
                }
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
            default:
                $sender->sendMessage("$prefix §cInvalid usage. Use /mcmmo help to show you list command mcMMO.");
                return true;
            break;
        }
        return true;
    }
}