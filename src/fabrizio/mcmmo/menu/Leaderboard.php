<?php

namespace fabrizio\mcmmo\menu;

use pocketmine\player\Player;
use fabrizio\mcmmo\mcMMO;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\Item;
use fabrizio\mcmmo\closure\Closure;
use fabrizio\mcmmo\menu\{Menu, Profile, Setting};

class Leaderboard {
    
    public function __construct(mcMMO $plugin, Player $player) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if($data == null){
                new Menu($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                return true;
            }
            if($data == 0){
                new Menu($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                return true;
            }
            $index = $data - 1;
            $skill = $player->getServer()->getPluginManager()->getPlugin("mcMMO")->skills[$index];
            $form = new SimpleForm(function(Player $player, $data) {
                if($data == null){
                    new Leaderboard($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                    return true;
                }
            });
            $plugin = $player->getServer()->getPluginManager()->getPlugin("mcMMO");
            if($plugin->database()->getAll() == []){
                $contents = ["§cNo such file on directory."];
            }
            $username = $plugin->getUsernames();
            $array = [];
            foreach($plugin->database()->getAll() as $data){
                $out = [$data[$skill]["level"]];
                $array = array_merge($array, $out);
            }
            $i=1;
            $contents = [];
            foreach($array as $key => $values){
                if($i < 20){
                    $max = max($array);
                    $keys = array_search($max, $array);
                    $key = $username[$keys];
                    $msg = ["§e{$i}§7. §b{$key}§7: §6{$max}§7."];
                    $contents = array_merge($contents, $msg);
                    unset($array[$keys]);
                    unset($username[$keys]);
                    if(sizeof($array) >0)
                    if(!in_array($max,$array))
                    $i++;
                }
            }
            $skillName = ucfirst($skill);
            $form->setTitle("§6".$skillName. " §aLeaderboard");
            $form->setContent("==============================\n".implode("\n", $contents)."\n§r==============================");
            $form->addButton("§lBack \n§r§eClick to back. ");
            $form->sendToPlayer($player);
            return true;
        });
        $form->setTitle("§6mcMMO §aLeaderboard");
        $form->setContent("Select one.");
        $form->addButton("§lBack \n§r§eClick to back. ");
        foreach($plugin->skills as $skills) {
            $skill = ucfirst($skills);
            $form->addButton("§l".$skill." \n§r§eTap to view. ");
        }
        $form->sendToPlayer($player);
        return $form;
    }
}