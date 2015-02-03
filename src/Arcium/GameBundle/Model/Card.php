<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseCard;

class Card extends BaseCard
{
    /**
     * @return array An array of cards in the format Xy
     */
    public function getCards()
    {
        return unserialize(parent::getCards());
    }

    /**
     * @param array $cards An array of cards in the format Xy
     * @return Card|void
     */
    public function setCards($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setCards(serialize($cards));
    }
}
