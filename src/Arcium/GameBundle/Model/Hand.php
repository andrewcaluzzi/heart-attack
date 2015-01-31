<?php

namespace Arcium\GameBundle\Model;

class Hand
{
    /** @var array $cards */
    private $cards;

    /** @var array $hearts */
    private $hearts;

    /**
     * @return string A CSV-formatted string of the cards
     */
    public function __toString()
    {
        return implode(', ', $this->cards);
    }

    /**
     * @param array $cards An array of cards in the format Xy where X is the card name and y is the card suit
     *                     eg. Ah = Ace of Hearts
     */
    public function __construct(array $cards, array $hearts)
    {
        ## TODO: some validation?
        $this->cards = $cards;
        $this->hearts = $hearts;
    }

    /**
     * @return int The number of non-heart cards in the hand
     */
    public function count()
    {
        return count($this->cards);
    }

    /**
     * @return int The number of heart cards in the hand
     */
    public function countHearts()
    {
        return count($this->hearts);
    }

    /**
     * @return array The array of cards in the format Xy
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @return array The array of hearts in the format Xy
     */
    public function getHearts()
    {
        return $this->hearts;
    }

    /**
     * @return string The serialized array of cards
     */
    public function getSerialisedCards()
    {
        return serialize($this->cards);
    }
}
