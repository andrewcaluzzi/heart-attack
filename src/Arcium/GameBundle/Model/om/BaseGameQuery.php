<?php

namespace Arcium\GameBundle\Model\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Arcium\GameBundle\Model\Game;
use Arcium\GameBundle\Model\GamePeer;
use Arcium\GameBundle\Model\GameQuery;
use Arcium\GameBundle\Model\Player;
use Arcium\GameBundle\Model\Turn;

/**
 * @method GameQuery orderById($order = Criteria::ASC) Order by the id column
 * @method GameQuery orderByDeck($order = Criteria::ASC) Order by the deck column
 * @method GameQuery orderByDiscard($order = Criteria::ASC) Order by the discard column
 * @method GameQuery orderByShop($order = Criteria::ASC) Order by the shop column
 * @method GameQuery orderByPlayerOne($order = Criteria::ASC) Order by the player_one column
 * @method GameQuery orderByPlayerOneHand($order = Criteria::ASC) Order by the player_one_hand column
 * @method GameQuery orderByPlayerTwo($order = Criteria::ASC) Order by the player_two column
 * @method GameQuery orderByPlayerTwoHand($order = Criteria::ASC) Order by the player_two_hand column
 * @method GameQuery orderByLastTurn($order = Criteria::ASC) Order by the last_turn column
 *
 * @method GameQuery groupById() Group by the id column
 * @method GameQuery groupByDeck() Group by the deck column
 * @method GameQuery groupByDiscard() Group by the discard column
 * @method GameQuery groupByShop() Group by the shop column
 * @method GameQuery groupByPlayerOne() Group by the player_one column
 * @method GameQuery groupByPlayerOneHand() Group by the player_one_hand column
 * @method GameQuery groupByPlayerTwo() Group by the player_two column
 * @method GameQuery groupByPlayerTwoHand() Group by the player_two_hand column
 * @method GameQuery groupByLastTurn() Group by the last_turn column
 *
 * @method GameQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method GameQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method GameQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method GameQuery leftJoinTurnRelatedByLastTurn($relationAlias = null) Adds a LEFT JOIN clause to the query using the TurnRelatedByLastTurn relation
 * @method GameQuery rightJoinTurnRelatedByLastTurn($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TurnRelatedByLastTurn relation
 * @method GameQuery innerJoinTurnRelatedByLastTurn($relationAlias = null) Adds a INNER JOIN clause to the query using the TurnRelatedByLastTurn relation
 *
 * @method GameQuery leftJoinPlayerRelatedByPlayerOne($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayerRelatedByPlayerOne relation
 * @method GameQuery rightJoinPlayerRelatedByPlayerOne($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayerRelatedByPlayerOne relation
 * @method GameQuery innerJoinPlayerRelatedByPlayerOne($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayerRelatedByPlayerOne relation
 *
 * @method GameQuery leftJoinPlayerRelatedByPlayerTwo($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayerRelatedByPlayerTwo relation
 * @method GameQuery rightJoinPlayerRelatedByPlayerTwo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayerRelatedByPlayerTwo relation
 * @method GameQuery innerJoinPlayerRelatedByPlayerTwo($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayerRelatedByPlayerTwo relation
 *
 * @method GameQuery leftJoinTurnRelatedByGameId($relationAlias = null) Adds a LEFT JOIN clause to the query using the TurnRelatedByGameId relation
 * @method GameQuery rightJoinTurnRelatedByGameId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TurnRelatedByGameId relation
 * @method GameQuery innerJoinTurnRelatedByGameId($relationAlias = null) Adds a INNER JOIN clause to the query using the TurnRelatedByGameId relation
 *
 * @method Game findOne(PropelPDO $con = null) Return the first Game matching the query
 * @method Game findOneOrCreate(PropelPDO $con = null) Return the first Game matching the query, or a new Game object populated from the query conditions when no match is found
 *
 * @method Game findOneByDeck(string $deck) Return the first Game filtered by the deck column
 * @method Game findOneByDiscard(string $discard) Return the first Game filtered by the discard column
 * @method Game findOneByShop(string $shop) Return the first Game filtered by the shop column
 * @method Game findOneByPlayerOne(int $player_one) Return the first Game filtered by the player_one column
 * @method Game findOneByPlayerOneHand(string $player_one_hand) Return the first Game filtered by the player_one_hand column
 * @method Game findOneByPlayerTwo(int $player_two) Return the first Game filtered by the player_two column
 * @method Game findOneByPlayerTwoHand(string $player_two_hand) Return the first Game filtered by the player_two_hand column
 * @method Game findOneByLastTurn(int $last_turn) Return the first Game filtered by the last_turn column
 *
 * @method array findById(int $id) Return Game objects filtered by the id column
 * @method array findByDeck(string $deck) Return Game objects filtered by the deck column
 * @method array findByDiscard(string $discard) Return Game objects filtered by the discard column
 * @method array findByShop(string $shop) Return Game objects filtered by the shop column
 * @method array findByPlayerOne(int $player_one) Return Game objects filtered by the player_one column
 * @method array findByPlayerOneHand(string $player_one_hand) Return Game objects filtered by the player_one_hand column
 * @method array findByPlayerTwo(int $player_two) Return Game objects filtered by the player_two column
 * @method array findByPlayerTwoHand(string $player_two_hand) Return Game objects filtered by the player_two_hand column
 * @method array findByLastTurn(int $last_turn) Return Game objects filtered by the last_turn column
 */
abstract class BaseGameQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseGameQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = null, $modelName = null, $modelAlias = null)
    {
        if (null === $dbName) {
            $dbName = 'default';
        }
        if (null === $modelName) {
            $modelName = 'Arcium\\GameBundle\\Model\\Game';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new GameQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   GameQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return GameQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof GameQuery) {
            return $criteria;
        }
        $query = new GameQuery(null, null, $modelAlias);

        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Game|Game[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = GamePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Game A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Game A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `deck`, `discard`, `shop`, `player_one`, `player_one_hand`, `player_two`, `player_two_hand`, `last_turn` FROM `games` WHERE `id` = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Game();
            $obj->hydrate($row);
            GamePeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Game|Game[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Game[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(GamePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(GamePeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(GamePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(GamePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the deck column
     *
     * Example usage:
     * <code>
     * $query->filterByDeck('fooValue');   // WHERE deck = 'fooValue'
     * $query->filterByDeck('%fooValue%'); // WHERE deck LIKE '%fooValue%'
     * </code>
     *
     * @param     string $deck The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByDeck($deck = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($deck)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $deck)) {
                $deck = str_replace('*', '%', $deck);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::DECK, $deck, $comparison);
    }

    /**
     * Filter the query on the discard column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscard('fooValue');   // WHERE discard = 'fooValue'
     * $query->filterByDiscard('%fooValue%'); // WHERE discard LIKE '%fooValue%'
     * </code>
     *
     * @param     string $discard The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByDiscard($discard = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($discard)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $discard)) {
                $discard = str_replace('*', '%', $discard);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::DISCARD, $discard, $comparison);
    }

    /**
     * Filter the query on the shop column
     *
     * Example usage:
     * <code>
     * $query->filterByShop('fooValue');   // WHERE shop = 'fooValue'
     * $query->filterByShop('%fooValue%'); // WHERE shop LIKE '%fooValue%'
     * </code>
     *
     * @param     string $shop The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByShop($shop = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($shop)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $shop)) {
                $shop = str_replace('*', '%', $shop);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::SHOP, $shop, $comparison);
    }

    /**
     * Filter the query on the player_one column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerOne(1234); // WHERE player_one = 1234
     * $query->filterByPlayerOne(array(12, 34)); // WHERE player_one IN (12, 34)
     * $query->filterByPlayerOne(array('min' => 12)); // WHERE player_one >= 12
     * $query->filterByPlayerOne(array('max' => 12)); // WHERE player_one <= 12
     * </code>
     *
     * @see       filterByPlayerRelatedByPlayerOne()
     *
     * @param     mixed $playerOne The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerOne($playerOne = null, $comparison = null)
    {
        if (is_array($playerOne)) {
            $useMinMax = false;
            if (isset($playerOne['min'])) {
                $this->addUsingAlias(GamePeer::PLAYER_ONE, $playerOne['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerOne['max'])) {
                $this->addUsingAlias(GamePeer::PLAYER_ONE, $playerOne['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYER_ONE, $playerOne, $comparison);
    }

    /**
     * Filter the query on the player_one_hand column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerOneHand('fooValue');   // WHERE player_one_hand = 'fooValue'
     * $query->filterByPlayerOneHand('%fooValue%'); // WHERE player_one_hand LIKE '%fooValue%'
     * </code>
     *
     * @param     string $playerOneHand The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerOneHand($playerOneHand = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($playerOneHand)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $playerOneHand)) {
                $playerOneHand = str_replace('*', '%', $playerOneHand);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYER_ONE_HAND, $playerOneHand, $comparison);
    }

    /**
     * Filter the query on the player_two column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerTwo(1234); // WHERE player_two = 1234
     * $query->filterByPlayerTwo(array(12, 34)); // WHERE player_two IN (12, 34)
     * $query->filterByPlayerTwo(array('min' => 12)); // WHERE player_two >= 12
     * $query->filterByPlayerTwo(array('max' => 12)); // WHERE player_two <= 12
     * </code>
     *
     * @see       filterByPlayerRelatedByPlayerTwo()
     *
     * @param     mixed $playerTwo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerTwo($playerTwo = null, $comparison = null)
    {
        if (is_array($playerTwo)) {
            $useMinMax = false;
            if (isset($playerTwo['min'])) {
                $this->addUsingAlias(GamePeer::PLAYER_TWO, $playerTwo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerTwo['max'])) {
                $this->addUsingAlias(GamePeer::PLAYER_TWO, $playerTwo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYER_TWO, $playerTwo, $comparison);
    }

    /**
     * Filter the query on the player_two_hand column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerTwoHand('fooValue');   // WHERE player_two_hand = 'fooValue'
     * $query->filterByPlayerTwoHand('%fooValue%'); // WHERE player_two_hand LIKE '%fooValue%'
     * </code>
     *
     * @param     string $playerTwoHand The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerTwoHand($playerTwoHand = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($playerTwoHand)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $playerTwoHand)) {
                $playerTwoHand = str_replace('*', '%', $playerTwoHand);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYER_TWO_HAND, $playerTwoHand, $comparison);
    }

    /**
     * Filter the query on the last_turn column
     *
     * Example usage:
     * <code>
     * $query->filterByLastTurn(1234); // WHERE last_turn = 1234
     * $query->filterByLastTurn(array(12, 34)); // WHERE last_turn IN (12, 34)
     * $query->filterByLastTurn(array('min' => 12)); // WHERE last_turn >= 12
     * $query->filterByLastTurn(array('max' => 12)); // WHERE last_turn <= 12
     * </code>
     *
     * @see       filterByTurnRelatedByLastTurn()
     *
     * @param     mixed $lastTurn The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByLastTurn($lastTurn = null, $comparison = null)
    {
        if (is_array($lastTurn)) {
            $useMinMax = false;
            if (isset($lastTurn['min'])) {
                $this->addUsingAlias(GamePeer::LAST_TURN, $lastTurn['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastTurn['max'])) {
                $this->addUsingAlias(GamePeer::LAST_TURN, $lastTurn['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::LAST_TURN, $lastTurn, $comparison);
    }

    /**
     * Filter the query by a related Turn object
     *
     * @param   Turn|PropelObjectCollection $turn The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTurnRelatedByLastTurn($turn, $comparison = null)
    {
        if ($turn instanceof Turn) {
            return $this
                ->addUsingAlias(GamePeer::LAST_TURN, $turn->getId(), $comparison);
        } elseif ($turn instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::LAST_TURN, $turn->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTurnRelatedByLastTurn() only accepts arguments of type Turn or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TurnRelatedByLastTurn relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinTurnRelatedByLastTurn($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TurnRelatedByLastTurn');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'TurnRelatedByLastTurn');
        }

        return $this;
    }

    /**
     * Use the TurnRelatedByLastTurn relation Turn object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\TurnQuery A secondary query class using the current class as primary query
     */
    public function useTurnRelatedByLastTurnQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTurnRelatedByLastTurn($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TurnRelatedByLastTurn', '\Arcium\GameBundle\Model\TurnQuery');
    }

    /**
     * Filter the query by a related Player object
     *
     * @param   Player|PropelObjectCollection $player The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayerRelatedByPlayerOne($player, $comparison = null)
    {
        if ($player instanceof Player) {
            return $this
                ->addUsingAlias(GamePeer::PLAYER_ONE, $player->getId(), $comparison);
        } elseif ($player instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::PLAYER_ONE, $player->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlayerRelatedByPlayerOne() only accepts arguments of type Player or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayerRelatedByPlayerOne relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinPlayerRelatedByPlayerOne($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayerRelatedByPlayerOne');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PlayerRelatedByPlayerOne');
        }

        return $this;
    }

    /**
     * Use the PlayerRelatedByPlayerOne relation Player object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\PlayerQuery A secondary query class using the current class as primary query
     */
    public function usePlayerRelatedByPlayerOneQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayerRelatedByPlayerOne($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayerRelatedByPlayerOne', '\Arcium\GameBundle\Model\PlayerQuery');
    }

    /**
     * Filter the query by a related Player object
     *
     * @param   Player|PropelObjectCollection $player The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPlayerRelatedByPlayerTwo($player, $comparison = null)
    {
        if ($player instanceof Player) {
            return $this
                ->addUsingAlias(GamePeer::PLAYER_TWO, $player->getId(), $comparison);
        } elseif ($player instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::PLAYER_TWO, $player->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlayerRelatedByPlayerTwo() only accepts arguments of type Player or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayerRelatedByPlayerTwo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinPlayerRelatedByPlayerTwo($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayerRelatedByPlayerTwo');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PlayerRelatedByPlayerTwo');
        }

        return $this;
    }

    /**
     * Use the PlayerRelatedByPlayerTwo relation Player object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\PlayerQuery A secondary query class using the current class as primary query
     */
    public function usePlayerRelatedByPlayerTwoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayerRelatedByPlayerTwo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayerRelatedByPlayerTwo', '\Arcium\GameBundle\Model\PlayerQuery');
    }

    /**
     * Filter the query by a related Turn object
     *
     * @param   Turn|PropelObjectCollection $turn  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTurnRelatedByGameId($turn, $comparison = null)
    {
        if ($turn instanceof Turn) {
            return $this
                ->addUsingAlias(GamePeer::ID, $turn->getGameId(), $comparison);
        } elseif ($turn instanceof PropelObjectCollection) {
            return $this
                ->useTurnRelatedByGameIdQuery()
                ->filterByPrimaryKeys($turn->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTurnRelatedByGameId() only accepts arguments of type Turn or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TurnRelatedByGameId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinTurnRelatedByGameId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TurnRelatedByGameId');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'TurnRelatedByGameId');
        }

        return $this;
    }

    /**
     * Use the TurnRelatedByGameId relation Turn object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\TurnQuery A secondary query class using the current class as primary query
     */
    public function useTurnRelatedByGameIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTurnRelatedByGameId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TurnRelatedByGameId', '\Arcium\GameBundle\Model\TurnQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Game $game Object to remove from the list of results
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function prune($game = null)
    {
        if ($game) {
            $this->addUsingAlias(GamePeer::ID, $game->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
