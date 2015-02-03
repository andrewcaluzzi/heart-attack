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
use Arcium\GameBundle\Model\Card;
use Arcium\GameBundle\Model\Game;
use Arcium\GameBundle\Model\GamePeer;
use Arcium\GameBundle\Model\GameQuery;
use Arcium\GameBundle\Model\Player;
use Arcium\GameBundle\Model\Turn;

/**
 * @method GameQuery orderById($order = Criteria::ASC) Order by the id column
 * @method GameQuery orderByDraw($order = Criteria::ASC) Order by the draw column
 * @method GameQuery orderByDiscard($order = Criteria::ASC) Order by the discard column
 * @method GameQuery orderByShop($order = Criteria::ASC) Order by the shop column
 * @method GameQuery orderByPlayerOneId($order = Criteria::ASC) Order by the player_one_id column
 * @method GameQuery orderByPlayerTwoId($order = Criteria::ASC) Order by the player_two_id column
 * @method GameQuery orderByLastTurnId($order = Criteria::ASC) Order by the last_turn_id column
 *
 * @method GameQuery groupById() Group by the id column
 * @method GameQuery groupByDraw() Group by the draw column
 * @method GameQuery groupByDiscard() Group by the discard column
 * @method GameQuery groupByShop() Group by the shop column
 * @method GameQuery groupByPlayerOneId() Group by the player_one_id column
 * @method GameQuery groupByPlayerTwoId() Group by the player_two_id column
 * @method GameQuery groupByLastTurnId() Group by the last_turn_id column
 *
 * @method GameQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method GameQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method GameQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method GameQuery leftJoinCardRelatedByDiscard($relationAlias = null) Adds a LEFT JOIN clause to the query using the CardRelatedByDiscard relation
 * @method GameQuery rightJoinCardRelatedByDiscard($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CardRelatedByDiscard relation
 * @method GameQuery innerJoinCardRelatedByDiscard($relationAlias = null) Adds a INNER JOIN clause to the query using the CardRelatedByDiscard relation
 *
 * @method GameQuery leftJoinCardRelatedByDraw($relationAlias = null) Adds a LEFT JOIN clause to the query using the CardRelatedByDraw relation
 * @method GameQuery rightJoinCardRelatedByDraw($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CardRelatedByDraw relation
 * @method GameQuery innerJoinCardRelatedByDraw($relationAlias = null) Adds a INNER JOIN clause to the query using the CardRelatedByDraw relation
 *
 * @method GameQuery leftJoinTurnRelatedByLastTurnId($relationAlias = null) Adds a LEFT JOIN clause to the query using the TurnRelatedByLastTurnId relation
 * @method GameQuery rightJoinTurnRelatedByLastTurnId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TurnRelatedByLastTurnId relation
 * @method GameQuery innerJoinTurnRelatedByLastTurnId($relationAlias = null) Adds a INNER JOIN clause to the query using the TurnRelatedByLastTurnId relation
 *
 * @method GameQuery leftJoinPlayerRelatedByPlayerOneId($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayerRelatedByPlayerOneId relation
 * @method GameQuery rightJoinPlayerRelatedByPlayerOneId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayerRelatedByPlayerOneId relation
 * @method GameQuery innerJoinPlayerRelatedByPlayerOneId($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayerRelatedByPlayerOneId relation
 *
 * @method GameQuery leftJoinPlayerRelatedByPlayerTwoId($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayerRelatedByPlayerTwoId relation
 * @method GameQuery rightJoinPlayerRelatedByPlayerTwoId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayerRelatedByPlayerTwoId relation
 * @method GameQuery innerJoinPlayerRelatedByPlayerTwoId($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayerRelatedByPlayerTwoId relation
 *
 * @method GameQuery leftJoinCardRelatedByShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the CardRelatedByShop relation
 * @method GameQuery rightJoinCardRelatedByShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CardRelatedByShop relation
 * @method GameQuery innerJoinCardRelatedByShop($relationAlias = null) Adds a INNER JOIN clause to the query using the CardRelatedByShop relation
 *
 * @method GameQuery leftJoinTurnRelatedByGameId($relationAlias = null) Adds a LEFT JOIN clause to the query using the TurnRelatedByGameId relation
 * @method GameQuery rightJoinTurnRelatedByGameId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TurnRelatedByGameId relation
 * @method GameQuery innerJoinTurnRelatedByGameId($relationAlias = null) Adds a INNER JOIN clause to the query using the TurnRelatedByGameId relation
 *
 * @method Game findOne(PropelPDO $con = null) Return the first Game matching the query
 * @method Game findOneOrCreate(PropelPDO $con = null) Return the first Game matching the query, or a new Game object populated from the query conditions when no match is found
 *
 * @method Game findOneByDraw(int $draw) Return the first Game filtered by the draw column
 * @method Game findOneByDiscard(int $discard) Return the first Game filtered by the discard column
 * @method Game findOneByShop(int $shop) Return the first Game filtered by the shop column
 * @method Game findOneByPlayerOneId(int $player_one_id) Return the first Game filtered by the player_one_id column
 * @method Game findOneByPlayerTwoId(int $player_two_id) Return the first Game filtered by the player_two_id column
 * @method Game findOneByLastTurnId(int $last_turn_id) Return the first Game filtered by the last_turn_id column
 *
 * @method array findById(int $id) Return Game objects filtered by the id column
 * @method array findByDraw(int $draw) Return Game objects filtered by the draw column
 * @method array findByDiscard(int $discard) Return Game objects filtered by the discard column
 * @method array findByShop(int $shop) Return Game objects filtered by the shop column
 * @method array findByPlayerOneId(int $player_one_id) Return Game objects filtered by the player_one_id column
 * @method array findByPlayerTwoId(int $player_two_id) Return Game objects filtered by the player_two_id column
 * @method array findByLastTurnId(int $last_turn_id) Return Game objects filtered by the last_turn_id column
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
        $sql = 'SELECT `id`, `draw`, `discard`, `shop`, `player_one_id`, `player_two_id`, `last_turn_id` FROM `games` WHERE `id` = :p0';
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
     * Filter the query on the draw column
     *
     * Example usage:
     * <code>
     * $query->filterByDraw(1234); // WHERE draw = 1234
     * $query->filterByDraw(array(12, 34)); // WHERE draw IN (12, 34)
     * $query->filterByDraw(array('min' => 12)); // WHERE draw >= 12
     * $query->filterByDraw(array('max' => 12)); // WHERE draw <= 12
     * </code>
     *
     * @see       filterByCardRelatedByDraw()
     *
     * @param     mixed $draw The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByDraw($draw = null, $comparison = null)
    {
        if (is_array($draw)) {
            $useMinMax = false;
            if (isset($draw['min'])) {
                $this->addUsingAlias(GamePeer::DRAW, $draw['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($draw['max'])) {
                $this->addUsingAlias(GamePeer::DRAW, $draw['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::DRAW, $draw, $comparison);
    }

    /**
     * Filter the query on the discard column
     *
     * Example usage:
     * <code>
     * $query->filterByDiscard(1234); // WHERE discard = 1234
     * $query->filterByDiscard(array(12, 34)); // WHERE discard IN (12, 34)
     * $query->filterByDiscard(array('min' => 12)); // WHERE discard >= 12
     * $query->filterByDiscard(array('max' => 12)); // WHERE discard <= 12
     * </code>
     *
     * @see       filterByCardRelatedByDiscard()
     *
     * @param     mixed $discard The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByDiscard($discard = null, $comparison = null)
    {
        if (is_array($discard)) {
            $useMinMax = false;
            if (isset($discard['min'])) {
                $this->addUsingAlias(GamePeer::DISCARD, $discard['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($discard['max'])) {
                $this->addUsingAlias(GamePeer::DISCARD, $discard['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::DISCARD, $discard, $comparison);
    }

    /**
     * Filter the query on the shop column
     *
     * Example usage:
     * <code>
     * $query->filterByShop(1234); // WHERE shop = 1234
     * $query->filterByShop(array(12, 34)); // WHERE shop IN (12, 34)
     * $query->filterByShop(array('min' => 12)); // WHERE shop >= 12
     * $query->filterByShop(array('max' => 12)); // WHERE shop <= 12
     * </code>
     *
     * @see       filterByCardRelatedByShop()
     *
     * @param     mixed $shop The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByShop($shop = null, $comparison = null)
    {
        if (is_array($shop)) {
            $useMinMax = false;
            if (isset($shop['min'])) {
                $this->addUsingAlias(GamePeer::SHOP, $shop['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($shop['max'])) {
                $this->addUsingAlias(GamePeer::SHOP, $shop['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::SHOP, $shop, $comparison);
    }

    /**
     * Filter the query on the player_one_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerOneId(1234); // WHERE player_one_id = 1234
     * $query->filterByPlayerOneId(array(12, 34)); // WHERE player_one_id IN (12, 34)
     * $query->filterByPlayerOneId(array('min' => 12)); // WHERE player_one_id >= 12
     * $query->filterByPlayerOneId(array('max' => 12)); // WHERE player_one_id <= 12
     * </code>
     *
     * @see       filterByPlayerRelatedByPlayerOneId()
     *
     * @param     mixed $playerOneId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerOneId($playerOneId = null, $comparison = null)
    {
        if (is_array($playerOneId)) {
            $useMinMax = false;
            if (isset($playerOneId['min'])) {
                $this->addUsingAlias(GamePeer::PLAYER_ONE_ID, $playerOneId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerOneId['max'])) {
                $this->addUsingAlias(GamePeer::PLAYER_ONE_ID, $playerOneId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYER_ONE_ID, $playerOneId, $comparison);
    }

    /**
     * Filter the query on the player_two_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerTwoId(1234); // WHERE player_two_id = 1234
     * $query->filterByPlayerTwoId(array(12, 34)); // WHERE player_two_id IN (12, 34)
     * $query->filterByPlayerTwoId(array('min' => 12)); // WHERE player_two_id >= 12
     * $query->filterByPlayerTwoId(array('max' => 12)); // WHERE player_two_id <= 12
     * </code>
     *
     * @see       filterByPlayerRelatedByPlayerTwoId()
     *
     * @param     mixed $playerTwoId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerTwoId($playerTwoId = null, $comparison = null)
    {
        if (is_array($playerTwoId)) {
            $useMinMax = false;
            if (isset($playerTwoId['min'])) {
                $this->addUsingAlias(GamePeer::PLAYER_TWO_ID, $playerTwoId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerTwoId['max'])) {
                $this->addUsingAlias(GamePeer::PLAYER_TWO_ID, $playerTwoId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYER_TWO_ID, $playerTwoId, $comparison);
    }

    /**
     * Filter the query on the last_turn_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLastTurnId(1234); // WHERE last_turn_id = 1234
     * $query->filterByLastTurnId(array(12, 34)); // WHERE last_turn_id IN (12, 34)
     * $query->filterByLastTurnId(array('min' => 12)); // WHERE last_turn_id >= 12
     * $query->filterByLastTurnId(array('max' => 12)); // WHERE last_turn_id <= 12
     * </code>
     *
     * @see       filterByTurnRelatedByLastTurnId()
     *
     * @param     mixed $lastTurnId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByLastTurnId($lastTurnId = null, $comparison = null)
    {
        if (is_array($lastTurnId)) {
            $useMinMax = false;
            if (isset($lastTurnId['min'])) {
                $this->addUsingAlias(GamePeer::LAST_TURN_ID, $lastTurnId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastTurnId['max'])) {
                $this->addUsingAlias(GamePeer::LAST_TURN_ID, $lastTurnId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::LAST_TURN_ID, $lastTurnId, $comparison);
    }

    /**
     * Filter the query by a related Card object
     *
     * @param   Card|PropelObjectCollection $card The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCardRelatedByDiscard($card, $comparison = null)
    {
        if ($card instanceof Card) {
            return $this
                ->addUsingAlias(GamePeer::DISCARD, $card->getId(), $comparison);
        } elseif ($card instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::DISCARD, $card->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCardRelatedByDiscard() only accepts arguments of type Card or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CardRelatedByDiscard relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinCardRelatedByDiscard($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CardRelatedByDiscard');

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
            $this->addJoinObject($join, 'CardRelatedByDiscard');
        }

        return $this;
    }

    /**
     * Use the CardRelatedByDiscard relation Card object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\CardQuery A secondary query class using the current class as primary query
     */
    public function useCardRelatedByDiscardQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCardRelatedByDiscard($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CardRelatedByDiscard', '\Arcium\GameBundle\Model\CardQuery');
    }

    /**
     * Filter the query by a related Card object
     *
     * @param   Card|PropelObjectCollection $card The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCardRelatedByDraw($card, $comparison = null)
    {
        if ($card instanceof Card) {
            return $this
                ->addUsingAlias(GamePeer::DRAW, $card->getId(), $comparison);
        } elseif ($card instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::DRAW, $card->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCardRelatedByDraw() only accepts arguments of type Card or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CardRelatedByDraw relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinCardRelatedByDraw($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CardRelatedByDraw');

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
            $this->addJoinObject($join, 'CardRelatedByDraw');
        }

        return $this;
    }

    /**
     * Use the CardRelatedByDraw relation Card object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\CardQuery A secondary query class using the current class as primary query
     */
    public function useCardRelatedByDrawQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCardRelatedByDraw($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CardRelatedByDraw', '\Arcium\GameBundle\Model\CardQuery');
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
    public function filterByTurnRelatedByLastTurnId($turn, $comparison = null)
    {
        if ($turn instanceof Turn) {
            return $this
                ->addUsingAlias(GamePeer::LAST_TURN_ID, $turn->getId(), $comparison);
        } elseif ($turn instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::LAST_TURN_ID, $turn->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByTurnRelatedByLastTurnId() only accepts arguments of type Turn or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the TurnRelatedByLastTurnId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinTurnRelatedByLastTurnId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TurnRelatedByLastTurnId');

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
            $this->addJoinObject($join, 'TurnRelatedByLastTurnId');
        }

        return $this;
    }

    /**
     * Use the TurnRelatedByLastTurnId relation Turn object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\TurnQuery A secondary query class using the current class as primary query
     */
    public function useTurnRelatedByLastTurnIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinTurnRelatedByLastTurnId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TurnRelatedByLastTurnId', '\Arcium\GameBundle\Model\TurnQuery');
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
    public function filterByPlayerRelatedByPlayerOneId($player, $comparison = null)
    {
        if ($player instanceof Player) {
            return $this
                ->addUsingAlias(GamePeer::PLAYER_ONE_ID, $player->getId(), $comparison);
        } elseif ($player instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::PLAYER_ONE_ID, $player->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlayerRelatedByPlayerOneId() only accepts arguments of type Player or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayerRelatedByPlayerOneId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinPlayerRelatedByPlayerOneId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayerRelatedByPlayerOneId');

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
            $this->addJoinObject($join, 'PlayerRelatedByPlayerOneId');
        }

        return $this;
    }

    /**
     * Use the PlayerRelatedByPlayerOneId relation Player object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\PlayerQuery A secondary query class using the current class as primary query
     */
    public function usePlayerRelatedByPlayerOneIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayerRelatedByPlayerOneId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayerRelatedByPlayerOneId', '\Arcium\GameBundle\Model\PlayerQuery');
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
    public function filterByPlayerRelatedByPlayerTwoId($player, $comparison = null)
    {
        if ($player instanceof Player) {
            return $this
                ->addUsingAlias(GamePeer::PLAYER_TWO_ID, $player->getId(), $comparison);
        } elseif ($player instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::PLAYER_TWO_ID, $player->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlayerRelatedByPlayerTwoId() only accepts arguments of type Player or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayerRelatedByPlayerTwoId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinPlayerRelatedByPlayerTwoId($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayerRelatedByPlayerTwoId');

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
            $this->addJoinObject($join, 'PlayerRelatedByPlayerTwoId');
        }

        return $this;
    }

    /**
     * Use the PlayerRelatedByPlayerTwoId relation Player object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\PlayerQuery A secondary query class using the current class as primary query
     */
    public function usePlayerRelatedByPlayerTwoIdQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayerRelatedByPlayerTwoId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayerRelatedByPlayerTwoId', '\Arcium\GameBundle\Model\PlayerQuery');
    }

    /**
     * Filter the query by a related Card object
     *
     * @param   Card|PropelObjectCollection $card The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 GameQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByCardRelatedByShop($card, $comparison = null)
    {
        if ($card instanceof Card) {
            return $this
                ->addUsingAlias(GamePeer::SHOP, $card->getId(), $comparison);
        } elseif ($card instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::SHOP, $card->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByCardRelatedByShop() only accepts arguments of type Card or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CardRelatedByShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinCardRelatedByShop($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CardRelatedByShop');

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
            $this->addJoinObject($join, 'CardRelatedByShop');
        }

        return $this;
    }

    /**
     * Use the CardRelatedByShop relation Card object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\CardQuery A secondary query class using the current class as primary query
     */
    public function useCardRelatedByShopQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinCardRelatedByShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CardRelatedByShop', '\Arcium\GameBundle\Model\CardQuery');
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
