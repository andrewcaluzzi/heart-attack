<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseGame;
use Arcium\GameBundle\Model;

class Game extends BaseGame
{
    private function createCardHand($playerId, $type)
    {
        $hand = new Card();
        $hand->setGameId($this->getId());
        $hand->setPlayerId($playerId);
        $hand->setType($type);
        $hand->save();

        return $hand;
    }

    /*****************************
     * LAST TURN GET/SET FUNCTIONS
     ****************************/


    public function getLastTurn()
    {
        return $this->getTurnRelatedByLastTurnId();
    }

    public function setLastTurn(Turn $turn)
    {
        return parent::setTurnRelatedByLastTurnId($turn);
    }


    /*****************************
     * DRAW PILE GET/SET FUNCTIONS
     ****************************/


    public function getDraw()
    {
        $draw = parent::getCardRelatedByDraw();

        if(!$draw)
        {
            $draw = $this->createCardHand(null, CardPeer::TYPE_PILE);
            parent::setDraw($draw->getId());
        }

        return $draw;
    }

    public function setDraw($cards)
    {
        if(!is_array($cards))
            return;

        $draw = $this->getDraw();
        $draw->setCards($cards);
        $draw->save();
    }


    /********************************
     * DISCARD PILE GET/SET FUNCTIONS
     *******************************/


    public function getDiscard()
    {
        $discard = parent::getCardRelatedByDiscard();

        if(!$discard)
        {
            $discard = $this->createCardHand(null, CardPeer::TYPE_PILE);
            parent::setDiscard($discard->getId());
        }

        return $discard;
    }

    public function setDiscard($cards)
    {
        if(!is_array($cards))
            return;

        $discard = $this->getDiscard();
        $discard->setCards($cards);
        $discard->save();
    }


    /*****************************
     * SHOP PILE GET/SET FUNCTIONS
     ****************************/


    public function getShop()
    {
        $shop = parent::getCardRelatedByShop();

        if(!$shop)
        {
            $shop = $this->createCardHand(null, CardPeer::TYPE_PILE);
            parent::setShop($shop->getId());
        }

        return $shop;
    }

    public function setShop($cards)
    {
        if(!is_array($cards))
            return;

        $shop = $this->getShop();
        $shop->setCards($cards);
        $shop->save();
    }


    /**************************************
     * PLAYER OBJECT/HAND GET/SET FUNCTIONS
     *************************************/


    private function findPlayerHand($playerId)
    {
        return CardQuery::create()
            ->filterByGameId($this->getId())
            ->filterByPlayerId($playerId)
            ->filterByType(CardPeer::TYPE_HAND)
            ->findOne();
    }

    private function getPlayerHand($playerId)
    {
        $hand = $this->findPlayerHand($playerId);

        if(!$hand)
            $hand = $this->createCardHand($playerId, CardPeer::TYPE_HAND);

        return $hand;
    }

    public function getPlayerOneHand()
    {
        return $this->getPlayerHand($this->getPlayerOneId());
    }

    public function getPlayerTwoHand()
    {
        return $this->getPlayerHand($this->getPlayerTwoId());
    }

    public function setPlayerOneHand(array $cards)
    {
        $hand = $this->getPlayerOneHand();
        $hand->setCards($cards);
        $hand->save();
    }

    public function setPlayerTwoHand(array $cards)
    {
        $hand = $this->getPlayerTwoHand();
        $hand->setCards($cards);
        $hand->save();
    }

    /**
     * @return Player The game's player one object
     */
    public function getPlayerOneObject()
    {
        return parent::getPlayerRelatedByPlayerOneId();
    }

    /**
     * @return Player The game's player two object
     */
    public function getPlayerTwoObject()
    {
        return parent::getPlayerRelatedByPlayerTwoId();
    }
}
