<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseGame;
use Arcium\GameBundle\Model;

class Game extends BaseGame
{
    public function initialiseDeck($startingCards)
    {
        $deck = new Deck($startingCards);
        $this->setDeck($deck->getCards());

        return $deck;
    }

    public function draw($number)
    {
        $deck = new Deck($this->getDeck());
        $cards = $deck->draw($number);
        $this->setDeck($deck);

        return $cards;
    }

    public function setPlayerOneHand($cards)
    {
        if(!is_array($cards))
            return;

        parent::setPlayeronehand(serialize($cards));
    }

    public function setPlayerTwoHand($cards)
    {
        if(!is_array($cards))
            return;

        parent::setPlayertwohand(serialize($cards));
    }

    public function getDeck()
    {
        return unserialize(parent::getDeck());
    }

    public function setDeck($cards)
    {
        if(!is_array($cards))
            return;

        parent::setDeck(serialize($cards));
    }

    public function getPlayerOneHand()
    {
        return unserialize(parent::getPlayeronehand());
    }

    public function getPlayerTwoHand()
    {
        return unserialize(parent::getPlayertwohand());
    }

    /**
     * @return Player The first player in the game
     */
    public function getPlayerOneObject()
    {
        return parent::getPlayerRelatedByPlayerone();
    }

    /**
     * @return Player The second player in the game
     */
    public function getPlayerTwoObject()
    {
        return parent::getPlayerRelatedByPlayertwo();
    }
}
