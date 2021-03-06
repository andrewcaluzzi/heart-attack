<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseTurn;

class Turn extends BaseTurn
{
    /*************************
     * CARDS SET/GET FUNCTIONS
     ************************/


    /**
     * @return array An array of cards in the format Xy
     */
    public function getCards()
    {
        return unserialize(parent::getCards());
    }

    /**
     * @param string $cards An array of cards in the format Xy
     * @return Turn|void
     */
    public function setCards($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setCards(serialize($cards));
    }

}
