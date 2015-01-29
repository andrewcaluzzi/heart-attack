<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseGame;
use Arcium\GameBundle\Model;

class Game extends BaseGame
{
    /*****************************
     * DECK MANIPULATION FUNCTIONS
     ****************************/

    /**
     * @param $startingCards An array of cards in format Xy
     * @return Deck The deck object after creation
     */
    public function initialiseDeck(array $startingCards, $save = true)
    {
        $deck = new Deck($startingCards);
        $deck->shuffle();
        $this->setDeck($deck->getCards());

        if($save)
            $this->save();

        return $deck;
    }

    /**
     * @param $number The number of cards to draw
     * @return array The number of cards requested
     */
    public function draw($number, $save = true)
    {
        $deck = new Deck($this->getDeck());
        $cards = $deck->draw($number);
        $this->setDeck($deck->getCards());

        if($save)
            $this->save();

        return $cards;
    }

    /************************
     * DECK GET/SET FUNCTIONS
     ***********************/

    /**
     * @return array An array of cards in the form Xy
     */
    public function getDeck()
    {
        return unserialize(parent::getDeck());
    }

    /**
     * @param array $cards An array of cards in the format Xy
     */
    public function setDeck($cards)
    {
        if(!is_array($cards))
            return;

        parent::setDeck(serialize($cards));
    }

    /*******************************
     * PLAYER HAND GET/SET FUNCTIONS
     ******************************/

    public function drawCardsForPlayerOne($number, $save = true)
    {
        $cards = $this->draw($number, $save);
        $hand = $this->getPlayerOneHand();
        $this->setPlayerOneHand(array_merge($cards, $hand), $save);

        return $this->getPlayerTwoHand();
    }

    public function drawCardsForPlayerTwo($number, $save = true)
    {
        $cards = $this->draw($number, $save);
        $hand = $this->getPlayerTwoHand();
        $this->setPlayerTwoHand(array_merge($cards, $hand), $save);

        return $this->getPlayerTwoHand();
    }

    /**
     * @return array An array of cards in the format Xy
     */
    public function getPlayerOneHand()
    {
        return unserialize(parent::getPlayeronehand());
    }

    /**
     * @return array An array of cards in the format Xy
     */
    public function getPlayerTwoHand()
    {
        return unserialize(parent::getPlayertwohand());
    }

    /**
     * @param string $cards An array of cards in the format Xy
     */
    public function setPlayerOneHand($cards, $save = true)
    {
        if(!is_array($cards))
            return;

        parent::setPlayeronehand(serialize($cards));

        if($save)
            $this->save();
    }

    /**
     * @param string $cards An array of cards in the format Xy
     */
    public function setPlayerTwoHand($cards, $save = true)
    {
        if(!is_array($cards))
            return;

        parent::setPlayertwohand(serialize($cards));

        if($save)
            $this->save();
    }

    /**********************
     * PLAYER GET FUNCTIONS
     *********************/

    /**
     * @return Player The game's player one object
     */
    public function getPlayerOneObject()
    {
        return parent::getPlayerRelatedByPlayerone();
    }

    /**
     * @return Player The game's player two object
     */
    public function getPlayerTwoObject()
    {
        return parent::getPlayerRelatedByPlayertwo();
    }
}
