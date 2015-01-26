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
        $player = Model\PlayerPeer::retrieveByPK(1);
        $game = Model\GamePeer::retrieveByPK(1);
        $turn = Model\TurnPeer::retrieveByPK(1);

        /** @var $gm Services\GameManager */
        $gm = $this->get('gamemanager');

        return $this->render('ArciumGameBundle:Default:index.html.twig',
            array(
                'player' => $player,
                'game'   => $game,
                'turn'   => $turn
            )
        );
    }
}
