<?php

namespace Arcium\GameBundle\Services;

use Arcium\GameBundle\Model;

class GameManager
{
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
    private static $startingHeartSetOne = array(
        '2h', '5h', '6h', '9h', '10h', 'K10'
    );

    // player two's starting heart set
    private static $startingHeartSetTwo = array(
        '3h', '4h', '7h', '8h', 'Jh', 'Qh'
    );

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
