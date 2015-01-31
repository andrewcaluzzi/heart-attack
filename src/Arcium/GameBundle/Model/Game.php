<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseGame;
use Arcium\GameBundle\Model;

class Game extends BaseGame
{

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
     * @return Game|void
     */
    public function setDeck($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setDeck(serialize($cards));
    }


    /************************
     * SHOP GET/SET FUNCTIONS
     ***********************/


    /**
     * @return array An array of cards in the form Xy
     */
    public function getShop()
    {
        return unserialize(parent::getShop());
    }

    /**
     * @param string $cards An array of cards in the format Xy
     * @return Game|void
     */
    public function setShop($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setShop(serialize($cards));
    }


    /***************************
     * DISCARD GET/SET FUNCTIONS
     **************************/


    /**
     * @return array An array of cards in the format Xy
     */
    public function getDiscard()
    {
        return unserialize(parent::getDiscard());
    }

    /**
     * @param string $cards An array of cards in the format Xy
     * @return Game|void
     */
    public function setDiscard($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setDiscard(serialize($cards));
    }


    /*************************
     * LAST TURN GET FUNCTIONS
     ************************/


    /**
     * @param Model\Turn $turn The Turn object
     * @return Game|void
     */
    public function setLastTurn($turn)
    {
        if(!($turn instanceof Model\Turn))
            return;

        return $this->setLastTurn($turn->getId());
    }


    /*******************************
     * PLAYER HAND GET/SET FUNCTIONS
     ******************************/

    /**
     * @return array An array of cards in the format Xy
     */
    public function getPlayerOneHand()
    {
        return unserialize(parent::getPlayerOneHand());
    }

    /**
     * @return array An array of cards in the format Xy
     */
    public function getPlayerTwoHand()
    {
        return unserialize(parent::getPlayerTwoHand());
    }

    /**
     * @param string $cards An array of cards in the format Xy
     * @return Game|void
     */
    public function setPlayerOneHand($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setPlayerOneHand(serialize($cards));
    }

    /**
     * @param string $cards An array of cards in the format Xy
     * @return Game|void
     */
    public function setPlayerTwoHand($cards)
    {
        if(!is_array($cards))
            return;

        return parent::setPlayerTwoHand(serialize($cards));
    }


    /**********************
     * PLAYER GET FUNCTIONS
     *********************/


    /**
     * @return Player The game's player one object
     */
    public function getPlayerOneObject()
    {
        return parent::getPlayerRelatedByPlayerOne();
    }

    /**
     * @return Player The game's player two object
     */
    public function getPlayerTwoObject()
    {
        return parent::getPlayerRelatedByPlayerTwo();
    }
}
