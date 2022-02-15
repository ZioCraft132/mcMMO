<?php

namespace fabrizio\mcmmo\menu;

use pocketmine\player\Player;
use fabrizio\mcmmo\mcMMO;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\Item;
use fabrizio\mcmmo\closure\Closure;
use fabrizio\mcmmo\menu\{Menu, Profile, Setting, Leaderboard};

class Menu {

    public function __construct(mcMMO $plugin, Player $target) {
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if($data == null){
                return true;
            }
            switch($data){
                case 1:
                    new Profile($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                    return true;
                break;
                case 2:
                    new Setting($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                    return true;
                break;
                case 3:
                    new Leaderboard($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                    return true;
                break;
            }
        });
        $form->setTitle("§6mcMMO §aMenu");
        $form->addButton("§lBack \n§r§eClick to back. ");
        $form->addButton("§lProfile \n§r§eClick to view. ");
        $form->addButton("§lSettings \n§r§eClick to manage. ");
        $form->addButton("§lLeaderboard \n§r§eClick to view. ");
        $form->sendToPlayer($target);
        return $form;
    }
}
