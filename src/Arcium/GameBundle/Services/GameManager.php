<?php

namespace Arcium\GameBundle\Services;

use Arcium\GameBundle\Model;

class GameManager
{
    /**
     * Turn anatomy
     *
     * Phase 1: SETUP
     * - choose a [heart] if there isn't one out
     * - choose a [spade] to defend heart if:
     *      * player has a [spade] in their hand
     *      * [heart] hasn't got a [spade]
     *      * [heart] hasn't lost previous [spade] (defence broken)
     *
     * Phase 2: ATTACK
     * - if a player has a [club] in their hand:
     *      * choose one or more [clubs] and use their total value as an attack
     *      - if value > defending [heart] + [spade], discard both
     *      - if value > defending [spade], discard [spade] and turn [heart] sideways (defence broken)
     *
     * Phase 3: SHOP
     * - choose one or more [diamonds] and use total value to choose a card in the shop with the same or less value
     *      * cannot draw beyond MAX_CARDS_IN_HAND
     *
     * Phase 4: CLEANUP
     * - discard up to MAX_CARDS_IN_HAND and draw back up to MAX_CARDS_IN_HAND
     */

    const MAX_CARDS_IN_HAND = 5;


    /*****************************
     * CLASS MEMBERS AND FUNCTIONS
     ****************************/


    /** @var Model\Deck $deck */
    private $deck;

    /** @var Model\Deck $discard */
    private $discard;

    /** @var Model\Deck $shop */
    private $shop;

    /** @var Model\Game $game */
    private $game;

    /** @var Model\Deck $playerOneHand */
    private $playerOneHand;

    /** @var Model\Deck $playerTwoHand */
    private $playerTwoHand;

    /** @var Model\Turn $lastTurn */
    private $lastTurn;

    /**
     * @param Model\Game $game The game object to use
     * @param bool $create Whether to create a new game or load an existing one
     */
    public function __construct(Model\Game $game, $create = false)
    {
        $this->game = $game;

        if($create)
            $this->createNewGame();

        else
            $this->loadExistingGame();
    }

    private function createNewGame()
    {
        // setup the deck and shuffle
        $fullDeck = self::getFullDeck(self::getStartingCards());
        $this->deck = new Model\Deck($fullDeck);
        $this->deck->shuffle();

        // setup shop
        $this->shop = new Model\Deck($this->deck->draw(5));

        // setup empty discard
        $this->discard = new Model\Deck(array());

        // setup player hands
        $this->playerOneHand = new Model\Deck($this->deck->draw(5));
        $this->playerTwoHand = new Model\Deck($this->deck->draw(5));
    }

    private function loadExistingGame()
    {
        // load and setup deck
        $this->deck = new Model\Deck($this->game->getDeck());

        // load and setup shop
        $this->shop = new Model\Deck($this->game->getShop());

        // load and setup discard
        $this->discard = new Model\Deck($this->game->getDiscard());

        // load and setup player hands
        $this->playerOneHand = new Model\Deck($this->game->getPlayerOneHand());
        $this->playerTwoHand = new Model\Deck($this->game->getPlayerTwoHand());

        // load and setup last turn (if exists)
        $this->lastTurn = $this->game->getTurnRelatedByLastTurn();
    }

    public function handleTurn(Model\Turn $turn)
    {

    }

    public function save()
    {
        ## TODO: handle saving of the game object
        // save player hands, deck, discard, etc
        $this->game->setDeck($this->deck->getCards());
        $this->game->setShop($this->shop->getCards());
        $this->game->setDiscard($this->discard->getCards());
        $this->game->setPlayerOneHand($this->playerOneHand->getCards());
        $this->game->setPlayerTwoHand($this->playerTwoHand->getCards());
        $this->game->setLastTurn($this->lastTurn);

        try
        {
            $this->game->save();
        }

        catch(Exception $e)
        {
            ## TODO: handle this
        }
    }


    /******************************
     * STATIC MEMBERS AND FUNCTIONS
     *****************************/


    // card names
    private static $cardNames = array(
        'A' => 'Ace', 'K' => 'King', 'Q' => 'Queen', 'J' => 'Jack', '10' => '10', '9' => '9',
        '8' => '8', '7' => '7', '6' => '6', '5' => '5', '4' => '4', '3' => '3', '2' => '2'
    );

    // card suits
    private static $cardSuits = array(
        'h' => 'Hearts', 'd' => 'Diamonds', 'c' => 'Clubs', 's' => 'Spades'
    );

    // player one's starting heart set
    public static $startingHeartSetOne = array(
        '2h', '5h', '6h', '9h', '10h', 'Kh'
    );

    // player two's starting heart set
    public static $startingHeartSetTwo = array(
        '3h', '4h', '7h', '8h', 'Jh', 'Qh'
    );

    /**
     * @param array $exclusions Any cards to remove from the deck before returning
     * @return array An array representing a full deck in the format Xy where X is the card name and y is the card suit
     *               eg. array('Ah', 'Kh', ...
     */
    public static function getFullDeck($exclusions = array())
    {
        $deck = array();

        foreach(self::$cardNames as $nameLetter => $name)
        {
            foreach(self::$cardSuits as $suitLetter => $suit)
            {
                $deck[] = $nameLetter . $suitLetter;
            }
        }

        return array_diff($deck, $exclusions);
    }

    /**
     * @return array An array of starting cards to be excluded from the deck
     */
    public static function getStartingCards()
    {
        return array_merge(self::$startingHeartSetOne, self::$startingHeartSetTwo);
    }

    /**
     * @param $card A card in the format Xy where X is the card name and y is the card suit
     *              eg. Ah = Ace of Hearts
     * @return int|null Returns the integer value of a card, or null if invalid
     */
    public static function getCardValue($card)
    {
        if(self::validateCard($card) === false)
            return null;

        $values = str_split($card);
        $cardValue = null;

        switch($values[0])
        {
            case 'A':
            case 'K':
            case 'Q':
            case 'J':
            case '10':
                $value = 10;
                break;
            default:
                $value = intval($values[0]);
        }

        return $value > 2 && $value <= 10 ? $value : null;
    }

    /**
     * @param $card A card in the format Xy where X is the card name and y is the card suit
     *              eg. Ah = Ace of Hearts
     * @return bool Whether the card is valid
     */
    public static function validateCard($card)
    {
        # TODO: refactor to return the str_split rather than true false?

        $values = str_split($card);

        if(count($values) != 2)
            return false;
        if(!array_key_exists($values[0], self::$cardNames))
            return false;
        if(!array_key_exists($values[1], self::$cardSuits))
            return false;

        return true;
    }

    /**
     * @param $card A card in the format Xy where X is the card name and y is the card suit
     *              eg. Ah = Ace of Hearts
     * @return string|null The card's natural name if valid, or null if not
     */
    public static function getNaturalCardName($card)
    {
        if(self::validateCard($card) === false)
            return null;

        $values = str_split($card);
        $name = self::$cardNames[$values[0]] . " of " . self::$cardSuits[$values[1]];

        return $name;
    }

    /**
     * @param array $cards An array of strings, each in the format Xy where X is the card name and y is the card suit
     *                     eg. array('Ah', '7c') = Ace of Hearts, 7 of Clubs
     * @return string A natural representation of the valid cards in the array
     */
    public static function getCardString(array $cards)
    {
        $cardArray = array();

        foreach($cards as $card)
        {
            #$cardArray[] = '[' . self::getNaturalCardName($card) . ' = ' . self::getCardValue($card) . ']';
            $cardArray[] = self::getNaturalCardName($card);
        }

        return implode('|', $cardArray);
    }
}
