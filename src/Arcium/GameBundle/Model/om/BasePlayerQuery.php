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
use Arcium\GameBundle\Model\Player;
use Arcium\GameBundle\Model\PlayerPeer;
use Arcium\GameBundle\Model\PlayerQuery;
use Arcium\GameBundle\Model\Turn;

/**
 * @method PlayerQuery orderById($order = Criteria::ASC) Order by the id column
 * @method PlayerQuery orderByName($order = Criteria::ASC) Order by the name column
 * @method PlayerQuery orderByUsername($order = Criteria::ASC) Order by the username column
 *
 * @method PlayerQuery groupById() Group by the id column
 * @method PlayerQuery groupByName() Group by the name column
 * @method PlayerQuery groupByUsername() Group by the username column
 *
 * @method PlayerQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method PlayerQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method PlayerQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method PlayerQuery leftJoinGameRelatedByPlayerone($relationAlias = null) Adds a LEFT JOIN clause to the query using the GameRelatedByPlayerone relation
 * @method PlayerQuery rightJoinGameRelatedByPlayerone($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GameRelatedByPlayerone relation
 * @method PlayerQuery innerJoinGameRelatedByPlayerone($relationAlias = null) Adds a INNER JOIN clause to the query using the GameRelatedByPlayerone relation
 *
 * @method PlayerQuery leftJoinGameRelatedByPlayertwo($relationAlias = null) Adds a LEFT JOIN clause to the query using the GameRelatedByPlayertwo relation
 * @method PlayerQuery rightJoinGameRelatedByPlayertwo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GameRelatedByPlayertwo relation
 * @method PlayerQuery innerJoinGameRelatedByPlayertwo($relationAlias = null) Adds a INNER JOIN clause to the query using the GameRelatedByPlayertwo relation
 *
 * @method PlayerQuery leftJoinTurn($relationAlias = null) Adds a LEFT JOIN clause to the query using the Turn relation
 * @method PlayerQuery rightJoinTurn($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Turn relation
 * @method PlayerQuery innerJoinTurn($relationAlias = null) Adds a INNER JOIN clause to the query using the Turn relation
 *
 * @method Player findOne(PropelPDO $con = null) Return the first Player matching the query
 * @method Player findOneOrCreate(PropelPDO $con = null) Return the first Player matching the query, or a new Player object populated from the query conditions when no match is found
 *
 * @method Player findOneByName(string $name) Return the first Player filtered by the name column
 * @method Player findOneByUsername(string $username) Return the first Player filtered by the username column
 *
 * @method array findById(int $id) Return Player objects filtered by the id column
 * @method array findByName(string $name) Return Player objects filtered by the name column
 * @method array findByUsername(string $username) Return Player objects filtered by the username column
 */
abstract class BasePlayerQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BasePlayerQuery object.
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
            $modelName = 'Arcium\\GameBundle\\Model\\Player';
        }
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PlayerQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   PlayerQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PlayerQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PlayerQuery) {
            return $criteria;
        }
        $query = new PlayerQuery(null, null, $modelAlias);

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
     * @return   Player|Player[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PlayerPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PlayerPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Player A model object, or null if the key is not found
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
     * @return                 Player A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT `id`, `name`, `username` FROM `players` WHERE `id` = :p0';
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
            $obj = new Player();
            $obj->hydrate($row);
            PlayerPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Player|Player[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Player[]|mixed the list of results, formatted by the current formatter
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
     * @return PlayerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PlayerPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PlayerPeer::ID, $keys, Criteria::IN);
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
     * @return PlayerQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PlayerPeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PlayerPeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PlayerPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the name column
     *
     * Example usage:
     * <code>
     * $query->filterByName('fooValue');   // WHERE name = 'fooValue'
     * $query->filterByName('%fooValue%'); // WHERE name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $name The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function filterByName($name = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($name)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $name)) {
                $name = str_replace('*', '%', $name);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayerPeer::NAME, $name, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%'); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $username The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $username)) {
                $username = str_replace('*', '%', $username);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PlayerPeer::USERNAME, $username, $comparison);
    }

    /**
     * Filter the query by a related Game object
     *
     * @param   Game|PropelObjectCollection $game  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayerQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByGameRelatedByPlayerone($game, $comparison = null)
    {
        if ($game instanceof Game) {
            return $this
                ->addUsingAlias(PlayerPeer::ID, $game->getPlayerone(), $comparison);
        } elseif ($game instanceof PropelObjectCollection) {
            return $this
                ->useGameRelatedByPlayeroneQuery()
                ->filterByPrimaryKeys($game->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByGameRelatedByPlayerone() only accepts arguments of type Game or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GameRelatedByPlayerone relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function joinGameRelatedByPlayerone($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GameRelatedByPlayerone');

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
            $this->addJoinObject($join, 'GameRelatedByPlayerone');
        }

        return $this;
    }

    /**
     * Use the GameRelatedByPlayerone relation Game object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\GameQuery A secondary query class using the current class as primary query
     */
    public function useGameRelatedByPlayeroneQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGameRelatedByPlayerone($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GameRelatedByPlayerone', '\Arcium\GameBundle\Model\GameQuery');
    }

    /**
     * Filter the query by a related Game object
     *
     * @param   Game|PropelObjectCollection $game  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayerQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByGameRelatedByPlayertwo($game, $comparison = null)
    {
        if ($game instanceof Game) {
            return $this
                ->addUsingAlias(PlayerPeer::ID, $game->getPlayertwo(), $comparison);
        } elseif ($game instanceof PropelObjectCollection) {
            return $this
                ->useGameRelatedByPlayertwoQuery()
                ->filterByPrimaryKeys($game->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByGameRelatedByPlayertwo() only accepts arguments of type Game or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the GameRelatedByPlayertwo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function joinGameRelatedByPlayertwo($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GameRelatedByPlayertwo');

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
            $this->addJoinObject($join, 'GameRelatedByPlayertwo');
        }

        return $this;
    }

    /**
     * Use the GameRelatedByPlayertwo relation Game object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\GameQuery A secondary query class using the current class as primary query
     */
    public function useGameRelatedByPlayertwoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGameRelatedByPlayertwo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GameRelatedByPlayertwo', '\Arcium\GameBundle\Model\GameQuery');
    }

    /**
     * Filter the query by a related Turn object
     *
     * @param   Turn|PropelObjectCollection $turn  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 PlayerQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByTurn($turn, $comparison = null)
    {
        if ($turn instanceof Turn) {
            return $this
                ->addUsingAlias(PlayerPeer::ID, $turn->getPlayerId(), $comparison);
        } elseif ($turn instanceof PropelObjectCollection) {
            return $this
                ->useTurnQuery()
                ->filterByPrimaryKeys($turn->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByTurn() only accepts arguments of type Turn or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Turn relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function joinTurn($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Turn');

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
            $this->addJoinObject($join, 'Turn');
        }

        return $this;
    }

    /**
     * Use the Turn relation Turn object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\TurnQuery A secondary query class using the current class as primary query
     */
    public function useTurnQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTurn($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Turn', '\Arcium\GameBundle\Model\TurnQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Player $player Object to remove from the list of results
     *
     * @return PlayerQuery The current query, for fluid interface
     */
    public function prune($player = null)
    {
        if ($player) {
            $this->addUsingAlias(PlayerPeer::ID, $player->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
