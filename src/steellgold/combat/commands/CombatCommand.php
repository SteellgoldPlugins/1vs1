<?php

namespace steellgold\combat\commands;

use CortexPE\Commando\BaseCommand;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\world\Position;
use steellgold\combat\Combat;

class CombatCommand extends BaseCommand {

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender instanceof Player){
			$sender->sendMessage("§cThis command can only be used in-game.");
			return;
		}

		$sender->sendForm(self::getArenasForm());
	}

	public static function getArenasForm() : MenuForm {

		$buttons = [];
		$duels = [];

		$i = 0;
		foreach (Combat::getInstance()->getManager()->getDuels() as $duelId => $duel) {
			if ($duel->getPosition(1) instanceof Position AND $duel->getPosition(2) instanceof Position) {
				$buttons[] = new MenuOption($duel->getDisplayName() . "\n" . $duel->getSlots());
				$duels[$i] = $duelId;
				$i++;
			}
		}

		return new MenuForm(
			"Liste des arènes",
			"§l» §rVoici la liste des arènes disponibles:",
			$buttons,
			function (Player $player, int $selectedOption) use ($duels) : void {
				$duel = Combat::getInstance()->getManager()->getDuel($duels[$selectedOption]);
				if ($duel->getPlayer1() == null) $duel->setPlayer(1, $player, true);
				else $duel->setPlayer(2, $player, true);

				if ($duel->getSlots(true) == 2) {
					if ($duel->getPlayer1()->isOnline()) {
						$player->sendMessage("§l» §rVous avez rejoint l'arène §f{$duel->getDisplayName()}");
						$duel->getPlayer1()->sendMessage("§l» §r{$player->getName()} a rejoint l'arène, début du compte à rebours...");
						$duel->start();
					}else{
						$duel->setPlayer(1, $player, true);
						$duel->setPlayer(2, null);
						$player->sendMessage("§l» §rVous avez rejoint l'arène §f{$duel->getDisplayName()}");
						$player->sendMessage("§cL'arène n'est plus complete, un joueur la quitté il y a quelque instants, attendez qu'un autre joueur rejoigne");
					}
				} elseif ($duel->getSlots(true) == 1) {
					$player->sendMessage("§cL'arène n'est pas complete, attendez qu'un joueur rejoigne");
				}
			}
		);
	}

	protected function prepare(): void {
		// TODO: Implement prepare() method.
	}
}