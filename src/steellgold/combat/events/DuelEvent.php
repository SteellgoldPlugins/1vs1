<?php

namespace steellgold\combat\events;

use steellgold\combat\utils\instances\Duel;

class DuelEvent extends CustomEvent {
	public function __construct(
		private Duel $duel
	) {

	}

	public function getDuel(): Duel {
		return $this->duel;
	}
}