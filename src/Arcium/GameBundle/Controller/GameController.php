<?php

namespace Arcium\GameBundle\Controller;

use Arcium\GameBundle\Model;
use Arcium\GameBundle\Services;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class GameController extends Controller
{
    /**
     * @Route("/", name="arcium_game_index")
     */
    public function indexAction()
    {
        $game = Model\GamePeer::retrieveByPK(1);
        $playerOne = $game->getPlayerOneObject();
        $playerTwo = $game->getPlayerTwoObject();
        $history = array();

        /** @var $gm Services\GameManager */
        $gm = $this->get('gamemanager');
        $displayCards = function($cards) { return implode(', ', $cards); };

        $startingCards = $gm->getFullDeck($gm->getStartingCards());
        $history[] = "Unshuffled deck: " . $displayCards($startingCards);
        $game->initialiseDeck($startingCards);
        $history[] = "Shuffled deck:   " . $displayCards($game->getDeck());
        $game->setPlayerOneHand(array());
        $game->setPlayerTwoHand(array());

        $game->drawCardsForPlayerOne(5);
        $game->drawCardsForPlayerTwo(5);

        ## TODO: how to handle hearts separate to normal cards? new field in games table?

        $history[] = "Player One: " . $playerOne->getName() . " starts with hand " . $displayCards($game->getPlayerOneHand());
        $history[] = "Player Two: " . $playerTwo->getName() . " starts with hand " . $displayCards($game->getPlayerTwoHand());
        $history[] = "Deck is now " . $displayCards($game->getDeck());

        /**
         * Turn anatomy
         *
         * Phase 1: SETUP
         *
         * Phase 2: ATTACK
         *
         * Phase 3: SHOP
         *
         * Phase 4: CLEANUP
         */

        return $this->render('ArciumGameBundle:Default:index.html.twig', array('history' => $history));
    }
}
