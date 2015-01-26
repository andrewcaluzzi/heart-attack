<?php

namespace Arcium\GameBundle\Model;

use Arcium\GameBundle\Model\om\BaseTurnPeer;

class TurnPeer extends BaseTurnPeer
{
  const PHASE_SETUP   = 'SETUP';
  const PHASE_ATTACK  = 'ATTACK';
  const PHASE_SHOP    = 'SHOP';
  const PHASE_CLEANUP = 'CLEANUP';
}
