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
use Arcium\GameBundle\Model\CardPeer;
use Arcium\GameBundle\Model\CardQuery;
use Arcium\GameBundle\Model\Game;

/**
 * @method CardQuery orderById($order = Criteria::ASC) Order by the id column
 * @method CardQuery orderByGameId($order = Criteria::ASC) Order by the game_id column
 * @method CardQuery orderByPlayerId($order = Criteria::ASC) Order by the player_id column
 * @method CardQuery orderByCards($order = Criteria::ASC) Order by the cards column
 * @method CardQuery orderByType($order = Criteria::ASC) Order by the type column
 *
 * @method CardQuery groupById() Group by the id column
 * @method CardQuery groupByGameId() Group by the game_id column
 * @method CardQuery groupByPlayerId() Group by the player_id column
 * @method CardQuery groupByCards() Group by the cards column
 * @method CardQuery groupByType() Group by the type column
 *
 * @method CardQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method CardQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method CardQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method CardQuery leftJoinGameRelatedByDiscard($relationAlias = null) Adds a LEFT JOIN clause to the query using the GameRelatedByDiscard relation
 * @method CardQuery rightJoinGameRelatedByDiscard($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GameRelatedByDiscard relation
 * @method CardQuery innerJoinGameRelatedByDiscard($relationAlias = null) Adds a INNER JOIN clause to the query using the GameRelatedByDiscard relation
 *
 * @method CardQuery leftJoinGameRelatedByDraw($relationAlias = null) Adds a LEFT JOIN clause to the query using the GameRelatedByDraw relation
 * @method CardQuery rightJoinGameRelatedByDraw($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GameRelatedByDraw relation
 * @method CardQuery innerJoinGameRelatedByDraw($relationAlias = null) Adds a INNER JOIN clause to the query using the GameRelatedByDraw relation
 *
 * @method CardQuery leftJoinGameRelatedByShop($relationAlias = null) Adds a LEFT JOIN clause to the query using the GameRelatedByShop relation
 * @method CardQuery rightJoinGameRelatedByShop($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GameRelatedByShop relation
 * @method CardQuery innerJoinGameRelatedByShop($relationAlias = null) Adds a INNER JOIN clause to the query using the GameRelatedByShop relation
 *
 * @method Card findOne(PropelPDO $con = null) Return the first Card matching the query
 * @method Card findOneOrCreate(PropelPDO $con = null) Return the first Card matching the query, or a new Card object populated from the query conditions when no match is found
 *
 * @method Card findOneByGameId(int $game_id) Return the first Card filtered by the game_id column
 * @method Card findOneByPlayerId(int $player_id) Return the first Card filtered by the player_id column
 * @method Card findOneByCards(string $cards) Return the first Card filtered by the cards column
 * @method Card findOneByType(string $type) Return the first Card filtered by the type column
 *
 * @method array findById(int $id) Return Card objects filtered by the id column
 * @method array findByGameId(int $game_id) Return Card objects filtered by the game_id column
 * @method array findByPlayerId(int $player_id) Return Card objects filtered by the player_id column
 * @method array findByCards(string $cards) Return Card objects filtered by the cards column
 * @method array findByType(string $type) Return Card objects filtered by the type column
 */
abstract class BaseCardQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseCardQuery object.
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
            $modelName = 'Arcium\\GameBundle\\Model\\Card';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new CardQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   CardQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return CardQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof CardQuery) {
            return $criteria;
        }
        $query = new CardQuery(null, null, $modelAlias);

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
     * @return   Card|Card[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = CardPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(CardPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Card A model object, or null if the key is not found
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
     * @return                 Card A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `game_id`, `player_id`, `cards`, `type` FROM `cards` WHERE `id` = :p0';
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
            $obj = new Card();
            $obj->hydrate($row);
            CardPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Card|Card[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Card[]|mixed the list of results, formatted by the current formatter
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
     * @return CardQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(CardPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(CardPeer::ID, $keys, Criteria::IN);
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
     * @return CardQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(CardPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(CardPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CardPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the game_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGameId(1234); // WHERE game_id = 1234
     * $query->filterByGameId(array(12, 34)); // WHERE game_id IN (12, 34)
     * $query->filterByGameId(array('min' => 12)); // WHERE game_id >= 12
     * $query->filterByGameId(array('max' => 12)); // WHERE game_id <= 12
     * </code>
     *
     * @param     mixed $gameId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function filterByGameId($gameId = null, $comparison = null)
    {
        if (is_array($gameId)) {
            $useMinMax = false;
            if (isset($gameId['min'])) {
                $this->addUsingAlias(CardPeer::GAME_ID, $gameId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($gameId['max'])) {
                $this->addUsingAlias(CardPeer::GAME_ID, $gameId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CardPeer::GAME_ID, $gameId, $comparison);
    }

    /**
     * Filter the query on the player_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerId(1234); // WHERE player_id = 1234
     * $query->filterByPlayerId(array(12, 34)); // WHERE player_id IN (12, 34)
     * $query->filterByPlayerId(array('min' => 12)); // WHERE player_id >= 12
     * $query->filterByPlayerId(array('max' => 12)); // WHERE player_id <= 12
     * </code>
     *
     * @param     mixed $playerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function filterByPlayerId($playerId = null, $comparison = null)
    {
        if (is_array($playerId)) {
            $useMinMax = false;
            if (isset($playerId['min'])) {
                $this->addUsingAlias(CardPeer::PLAYER_ID, $playerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerId['max'])) {
                $this->addUsingAlias(CardPeer::PLAYER_ID, $playerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(CardPeer::PLAYER_ID, $playerId, $comparison);
    }

    /**
     * Filter the query on the cards column
     *
     * Example usage:
     * <code>
     * $query->filterByCards('fooValue');   // WHERE cards = 'fooValue'
     * $query->filterByCards('%fooValue%'); // WHERE cards LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cards The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function filterByCards($cards = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cards)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cards)) {
                $cards = str_replace('*', '%', $cards);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CardPeer::CARDS, $cards, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(CardPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query by a related Game object
     *
     * @param   Game|PropelObjectCollection $game  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CardQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByGameRelatedByDiscard($game, $comparison = null)
    {
        if ($game instanceof Game) {
            return $this
                ->addUsingAlias(CardPeer::ID, $game->getDiscard(), $comparison);
        } elseif ($game instanceof PropelObjectCollection) {
            return $this
                ->useGameRelatedByDiscardQuery()
                ->filterByPrimaryKeys($game->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByGameRelatedByDiscard() only accepts arguments of type Game or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GameRelatedByDiscard relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function joinGameRelatedByDiscard($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GameRelatedByDiscard');

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
            $this->addJoinObject($join, 'GameRelatedByDiscard');
        }

        return $this;
    }

    /**
     * Use the GameRelatedByDiscard relation Game object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\GameQuery A secondary query class using the current class as primary query
     */
    public function useGameRelatedByDiscardQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinGameRelatedByDiscard($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GameRelatedByDiscard', '\Arcium\GameBundle\Model\GameQuery');
    }

    /**
     * Filter the query by a related Game object
     *
     * @param   Game|PropelObjectCollection $game  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CardQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByGameRelatedByDraw($game, $comparison = null)
    {
        if ($game instanceof Game) {
            return $this
                ->addUsingAlias(CardPeer::ID, $game->getDraw(), $comparison);
        } elseif ($game instanceof PropelObjectCollection) {
            return $this
                ->useGameRelatedByDrawQuery()
                ->filterByPrimaryKeys($game->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByGameRelatedByDraw() only accepts arguments of type Game or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GameRelatedByDraw relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function joinGameRelatedByDraw($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GameRelatedByDraw');

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
            $this->addJoinObject($join, 'GameRelatedByDraw');
        }

        return $this;
    }

    /**
     * Use the GameRelatedByDraw relation Game object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\GameQuery A secondary query class using the current class as primary query
     */
    public function useGameRelatedByDrawQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinGameRelatedByDraw($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GameRelatedByDraw', '\Arcium\GameBundle\Model\GameQuery');
    }

    /**
     * Filter the query by a related Game object
     *
     * @param   Game|PropelObjectCollection $game  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 CardQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByGameRelatedByShop($game, $comparison = null)
    {
        if ($game instanceof Game) {
            return $this
                ->addUsingAlias(CardPeer::ID, $game->getShop(), $comparison);
        } elseif ($game instanceof PropelObjectCollection) {
            return $this
                ->useGameRelatedByShopQuery()
                ->filterByPrimaryKeys($game->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByGameRelatedByShop() only accepts arguments of type Game or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GameRelatedByShop relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function joinGameRelatedByShop($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GameRelatedByShop');

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
            $this->addJoinObject($join, 'GameRelatedByShop');
        }

        return $this;
    }

    /**
     * Use the GameRelatedByShop relation Game object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\GameQuery A secondary query class using the current class as primary query
     */
    public function useGameRelatedByShopQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinGameRelatedByShop($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GameRelatedByShop', '\Arcium\GameBundle\Model\GameQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Card $card Object to remove from the list of results
     *
     * @return CardQuery The current query, for fluid interface
     */
    public function prune($card = null)
    {
        if ($card) {
            $this->addUsingAlias(CardPeer::ID, $card->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
