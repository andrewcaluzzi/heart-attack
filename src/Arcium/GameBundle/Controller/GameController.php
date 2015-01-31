<?php

namespace Arcium\GameBundle\Controller;

use Arcium\GameBundle\Model;
use Arcium\GameBundle\Services;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GameController extends Controller
{
    /**
     * @Route("/", name="arcium_game_index")
     */
    public function indexAction()
    {
        $history = array();
        $game = Model\GamePeer::retrieveByPK(1);

        /** @var $gm Services\GameManager */
        $gm = new Services\GameManager($game, true);
        $gm->save();

        var_dump($gm);




        /*$displayCards = function($cards) { return implode(', ', $cards); };

        $startingCards = $gm->getFullDeck($gm->getStartingCards());
        $history[] = "Unshuffled deck: " . $displayCards($startingCards);
        $game->initialiseDeck($startingCards);
        $history[] = "Shuffled deck:   " . $displayCards($game->getDeck());
        $game->setPlayerOneHand(array());
        $game->setPlayerTwoHand(array());

        $game->drawCardsForPlayerOne(5);
        $game->drawCardsForPlayerTwo(5);

        ## TODO: Store hearts and normal cards together in hand, split them out logically
        ## TODO: Write functions for handling non-heart cards (eg. counts)

        $history[] = "Player One: " . $playerOne->getName() . " starts with hand " . $displayCards($game->getPlayerOneHand());
        $history[] = "Player Two: " . $playerTwo->getName() . " starts with hand " . $displayCards($game->getPlayerTwoHand());
        $history[] = "Deck is now " . $displayCards($game->getDeck());*/




        return $this->render('ArciumGameBundle:Default:index.html.twig', array('history' => $history));
    }
}
