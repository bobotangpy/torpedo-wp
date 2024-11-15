<?php
namespace Torpedo\Wp\Posts;

use Torpedo\Wp\ViewElements\Card;
use Torpedo\Wp\ViewElements\ViewElement;

interface CardListable
{
    /**
     * Return a view element object representing a card view of this post
     * @param string $context   string representing context within which this card is being rendererd
     * @return ViewElement|Card
     */
    public function getCardView($context = null);

    /**
     * Return a view element object representing a cta
     * @param string $context   string representing context within which this cta is being rendererd
     * @return ViewElement
     */
    public function getCtaView($context = null);

    /**
     * @param string $context   string representing context within which this cta is being rendererd
     * @return ViewElement
     */
    public function getSubCtaView($context = null);
}
