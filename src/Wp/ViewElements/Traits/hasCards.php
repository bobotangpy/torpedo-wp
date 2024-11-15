<?php


namespace Torpedo\Wp\ViewElements\Traits;


use Torpedo\Wp\ViewElements\Card;
use Torpedo\Wp\ViewElements\Interfaces\isCard;
use Torpedo\Wp\ViewElements\ViewElement;

trait hasCards
{
	/** @var Card[] */
	protected $cards = [];

    /**
     * @param isCard $card
     * @return ViewElement|hasCards|Mixed
     */
	public function addCard(isCard $card)
	{
		$this->cards[] = $card;

		return $this;
	}

	public function getCards()
    {
        return $this->cards;
    }
}

