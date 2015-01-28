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

        $startingCards = $gm->getFullDeck($gm->getStartingCards());
        $game->initialiseDeck($startingCards);
        $history[] = "Started game with deck " . implode(', ',$game->getDeck());
        $game->setPlayerOneHand($game->draw(5));
        $game->setPlayerTwoHand($game->draw(5));

        ## TODO: how to handle hearts separate to normal cards? new field in games table?

        $history[] = "Player One: " . $playerOne->getName() . " starts with hand " . implode(', ', $game->getPlayerOneHand());
        $history[] = "Player Two: " . $playerTwo->getName() . " starts with hand " . implode(', ', $game->getPlayerTwoHand());
        $history[] = "Deck is now " . implode(', ', $game->getDeck());

        ## Reminder: use [php app/console server:run localhost] for quick ad-hoc server

        return $this->render('ArciumGameBundle:Default:index.html.twig', array('history' => $history));
    }
}
