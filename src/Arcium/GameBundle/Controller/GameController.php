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
        $gm = new Services\GameManager($game);
        $gm->save();

        $turn = new Model\Turn();
        $turn->setPlayerId(1);
        $turn->setPhase(Model\TurnPeer::PHASE_SETUP);
        $turn->setCards(array("Kh", "As"));
        $turn->setGameId($game->getId());

        $gm->handleTurn($turn);

        ## TODO: Store hearts and normal cards together in hand, split them out logically
        ## TODO: Store game state (active heart/spade, lost hearts)
        ## TODO: Write functions for handling non-heart cards (eg. counts)

        return $this->render('ArciumGameBundle:Default:index.html.twig', array('history' => $history));
    }
}
