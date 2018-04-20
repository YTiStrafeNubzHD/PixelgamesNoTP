<?php

namespace Authors\PixelgamesNoTP;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class PixelgamesNoTP extends PluginBase implements Listener {

    private $enabled;

    public function onLoad() {
        $this->getLogger()->info("Laden...");
    }
    
    public function onEnable() {
        $this->enabled = [];
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("Aktiviert");
    }
    
    public function onDisable() {
        $this->getLogger()->info("Deaktiviert");
    }

    public function onCommand(CommandSender $issuer, Command $cmd, string $label, array $args) : bool{
        switch($cmd->getName()) {
            
            case "notp":
                
                if(!isset($args[0])) {
                    $issuer->sendMessage("§c[PGNoTP] Benutzung: /notp toggle");
                    $issuer->sendMessage("§6[PGNoTP] Benutzung: /notp info");
                    $issuer->sendMessage("§6[PGNoTP] Benutzung: /notp help");
                }
                
                switch(strtolower(array_shift($args))) {
                    
                
                    case "toggle":
                        
                if ($issuer instanceof Player && ($issuer->hasPermission("pgnotp.toggle") || $issuer->hasPermission("pgnotp.toggle.self"))) {
                    if (isset($this->enabled[strtolower($issuer->getName())])) {
                        unset($this->enabled[strtolower($issuer->getName())]);
                    }
                    
                else {
                    $this->enabled[strtolower($issuer->getName())] = strtolower($issuer->getName());
                }
                
                if (isset($this->enabled[strtolower($issuer->getName())])) {
                    $issuer->sendMessage("§b[PGNoTP] NoTP-Modus aktiviert. Keiner kann dich (oder sich zu dir) teleportieren");
                }
                else {
                    $issuer->sendMessage("§b[PGNoTP] NoTP-Modus deaktiviert");
                }
                return true;
                }
                
                elseif (!$issuer instanceof Player) {
                    $issuer->sendMessage("§4[PGNoTP] Dieser Befehl muss ingame ausgeführt werden!");
                    return true;
                }
                
                elseif (!$issuer->hasPermission("pgnotp.toggle") || !$issuer->hasPermission("pgnotp.toggle.self")) {
                    $issuer->sendMessage("§c[PGNoTP] Du hast nicht die Berechtigung, diesen Befehl auszuführen!");
                }
                return true;
                
                
                    case "info":
                        $issuer->sendMessage("§e-----------------------------");
                        $issuer->sendMessage("§ePlugin von Awzaw, iStrafeNubzHDyt");
                        $issuer->sendMessage("§bName: PixelgamesNoTP");
                        $issuer->sendMessage("§bOriginal: NoTP");
                        $issuer->sendMessage("§bVersion: 2.2#");
                        $issuer->sendMessage("§bFür PocketMine-API: 3.0.0-ALPHA11");
                        $issuer->sendMessage("§6Permissions: pgnotp, pgnotp.toggle, pgnotp.toggle.self");
                        $issuer->sendMessage("§eSpeziell für PIXELGAMES entwickelt");
                        $issuer->sendMessage("§e-----------------------------");
                        return true;
                        
                        
                    case "help":
                        $issuer->sendMessage("§9---§aNoTP-Plugin§9---");
                        $issuer->sendMessage("§a/notp toggle §b-> Schaltet den NoTP-Modus ein oder aus");
                        $issuer->sendMessage("§6/notp info §b-> Zeigt Details über das Plugin");
                        $issuer->sendMessage("§6/notp help §b-> Zeigt dieses Hilfemenü an");
                        return true;
                }
    }
    return true;
    }
    public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if ($event->isCancelled()) return;

        $message = $event->getMessage();

        if (strtolower(substr($message, 0, 3)) == "/tp" || strtolower(substr($message, 0, 5)) == "/call" || strtolower(substr($message, 0, 7)) == "/tphere" || strtolower(substr($message, 0, 4)) == "/tpa") {

            $args = explode(" ", $message);

            if (!isset($args[1])) {
                return;    
            }

            $sender = $event->getPlayer();

            foreach ($this->enabled as $notpuser) {

                if ((strpos(strtolower($notpuser), strtolower($args[1])) !== false) && (strtolower($notpuser) !== strtolower($sender->getName()))) {
                    $sender->sendMessage(TextFormat::RED . "[PGNoTP] Dieser Spieler lässt derzeit keine Teleportationen zu");
                    $event->setCancelled(true);
                    return;
                }

                if (isset($args[2]) && strpos(strtolower($notpuser), strtolower($args[2])) !== false && (strtolower($notpuser) !== strtolower($sender->getName()))) {
                    $sender->sendMessage(TextFormat::RED . "[PGNoTP] Dieser Spieler lässt derzeit keine Teleportationen zu");
                    $event->setCancelled(true);
                    return;
                }
            }
        }
    }
}