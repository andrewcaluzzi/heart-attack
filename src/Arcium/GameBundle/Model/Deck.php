<?php

namespace Arcium\GameBundle\Model;

class Deck
{
    const DECK_EMPTY = 'EMPTY';

    /** @var array $cards */
    private $cards;

    /**
     * @return string A CSV-formatted string of the cards
     */
    public function __toString()
    {
        return implode(', ', $this->cards);
    }

    /**
     * @param array $cards A card in the format Xy where X is the card name and y is the card suit
     *                     eg. Ah = Ace of Hearts
     */
    public function __construct(array $cards)
    {
        ## TODO: some validation?
        shuffle($cards);
        $this->cards = $cards;
    }

    /**
     * @param int $number The number of cards to draw
     * @return array An array of cards in the format Xy where X is the card name and y is the card suit
     *                eg. Ah = Ace of Hearts
     */
    public function draw($number)
    {
        ## TODO: check count, maybe return a signal? who handles shuffling?

        $cards = array();

        for($i = 0; $i < $number; $i++)
            $cards[] = array_shift($this->cards);

        #if($card === null)
        #    return self::DECK_EMPTY;

        return $cards;
    }

    /**
     * @return int The number of cards left in the deck
     */
    public function count()
    {
        return count($this->cards);
    }

    /**
     * @return array The array of cards
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @return string The serialized array of cards
     */
    public function getSerialisedCards()
    {
        return serialize($this->cards);
    }
}
