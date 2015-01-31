<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model;
use Arcium\GameBundle\Model\om\BasePlayer;

class Player extends BasePlayer
{
    /** @var Model\Hand $hand */
    private $hand;

    public function setHand(Model\Hand $hand)
    {
        $this->hand = $hand;
    }
}
