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
 * @method GameQuery orderByPlayerone($order = Criteria::ASC) Order by the playerOne column
 * @method GameQuery orderByPlayeronehand($order = Criteria::ASC) Order by the playerOneHand column
 * @method GameQuery orderByPlayertwo($order = Criteria::ASC) Order by the playerTwo column
 * @method GameQuery orderByPlayertwohand($order = Criteria::ASC) Order by the playerTwoHand column
 *
 * @method GameQuery groupById() Group by the id column
 * @method GameQuery groupByDeck() Group by the deck column
 * @method GameQuery groupByDiscard() Group by the discard column
 * @method GameQuery groupByShop() Group by the shop column
 * @method GameQuery groupByPlayerone() Group by the playerOne column
 * @method GameQuery groupByPlayeronehand() Group by the playerOneHand column
 * @method GameQuery groupByPlayertwo() Group by the playerTwo column
 * @method GameQuery groupByPlayertwohand() Group by the playerTwoHand column
 *
 * @method GameQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method GameQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method GameQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method GameQuery leftJoinPlayerRelatedByPlayerone($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayerRelatedByPlayerone relation
 * @method GameQuery rightJoinPlayerRelatedByPlayerone($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayerRelatedByPlayerone relation
 * @method GameQuery innerJoinPlayerRelatedByPlayerone($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayerRelatedByPlayerone relation
 *
 * @method GameQuery leftJoinPlayerRelatedByPlayertwo($relationAlias = null) Adds a LEFT JOIN clause to the query using the PlayerRelatedByPlayertwo relation
 * @method GameQuery rightJoinPlayerRelatedByPlayertwo($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PlayerRelatedByPlayertwo relation
 * @method GameQuery innerJoinPlayerRelatedByPlayertwo($relationAlias = null) Adds a INNER JOIN clause to the query using the PlayerRelatedByPlayertwo relation
 *
 * @method GameQuery leftJoinTurn($relationAlias = null) Adds a LEFT JOIN clause to the query using the Turn relation
 * @method GameQuery rightJoinTurn($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Turn relation
 * @method GameQuery innerJoinTurn($relationAlias = null) Adds a INNER JOIN clause to the query using the Turn relation
 *
 * @method Game findOne(PropelPDO $con = null) Return the first Game matching the query
 * @method Game findOneOrCreate(PropelPDO $con = null) Return the first Game matching the query, or a new Game object populated from the query conditions when no match is found
 *
 * @method Game findOneByDeck(string $deck) Return the first Game filtered by the deck column
 * @method Game findOneByDiscard(string $discard) Return the first Game filtered by the discard column
 * @method Game findOneByShop(string $shop) Return the first Game filtered by the shop column
 * @method Game findOneByPlayerone(int $playerOne) Return the first Game filtered by the playerOne column
 * @method Game findOneByPlayeronehand(string $playerOneHand) Return the first Game filtered by the playerOneHand column
 * @method Game findOneByPlayertwo(int $playerTwo) Return the first Game filtered by the playerTwo column
 * @method Game findOneByPlayertwohand(string $playerTwoHand) Return the first Game filtered by the playerTwoHand column
 *
 * @method array findById(int $id) Return Game objects filtered by the id column
 * @method array findByDeck(string $deck) Return Game objects filtered by the deck column
 * @method array findByDiscard(string $discard) Return Game objects filtered by the discard column
 * @method array findByShop(string $shop) Return Game objects filtered by the shop column
 * @method array findByPlayerone(int $playerOne) Return Game objects filtered by the playerOne column
 * @method array findByPlayeronehand(string $playerOneHand) Return Game objects filtered by the playerOneHand column
 * @method array findByPlayertwo(int $playerTwo) Return Game objects filtered by the playerTwo column
 * @method array findByPlayertwohand(string $playerTwoHand) Return Game objects filtered by the playerTwoHand column
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
        $sql = 'SELECT `id`, `deck`, `discard`, `shop`, `playerOne`, `playerOneHand`, `playerTwo`, `playerTwoHand` FROM `games` WHERE `id` = :p0';
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
     * Filter the query on the playerOne column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayerone(1234); // WHERE playerOne = 1234
     * $query->filterByPlayerone(array(12, 34)); // WHERE playerOne IN (12, 34)
     * $query->filterByPlayerone(array('min' => 12)); // WHERE playerOne >= 12
     * $query->filterByPlayerone(array('max' => 12)); // WHERE playerOne <= 12
     * </code>
     *
     * @see       filterByPlayerRelatedByPlayerone()
     *
     * @param     mixed $playerone The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayerone($playerone = null, $comparison = null)
    {
        if (is_array($playerone)) {
            $useMinMax = false;
            if (isset($playerone['min'])) {
                $this->addUsingAlias(GamePeer::PLAYERONE, $playerone['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playerone['max'])) {
                $this->addUsingAlias(GamePeer::PLAYERONE, $playerone['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYERONE, $playerone, $comparison);
    }

    /**
     * Filter the query on the playerOneHand column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayeronehand('fooValue');   // WHERE playerOneHand = 'fooValue'
     * $query->filterByPlayeronehand('%fooValue%'); // WHERE playerOneHand LIKE '%fooValue%'
     * </code>
     *
     * @param     string $playeronehand The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayeronehand($playeronehand = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($playeronehand)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $playeronehand)) {
                $playeronehand = str_replace('*', '%', $playeronehand);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYERONEHAND, $playeronehand, $comparison);
    }

    /**
     * Filter the query on the playerTwo column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayertwo(1234); // WHERE playerTwo = 1234
     * $query->filterByPlayertwo(array(12, 34)); // WHERE playerTwo IN (12, 34)
     * $query->filterByPlayertwo(array('min' => 12)); // WHERE playerTwo >= 12
     * $query->filterByPlayertwo(array('max' => 12)); // WHERE playerTwo <= 12
     * </code>
     *
     * @see       filterByPlayerRelatedByPlayertwo()
     *
     * @param     mixed $playertwo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayertwo($playertwo = null, $comparison = null)
    {
        if (is_array($playertwo)) {
            $useMinMax = false;
            if (isset($playertwo['min'])) {
                $this->addUsingAlias(GamePeer::PLAYERTWO, $playertwo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($playertwo['max'])) {
                $this->addUsingAlias(GamePeer::PLAYERTWO, $playertwo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYERTWO, $playertwo, $comparison);
    }

    /**
     * Filter the query on the playerTwoHand column
     *
     * Example usage:
     * <code>
     * $query->filterByPlayertwohand('fooValue');   // WHERE playerTwoHand = 'fooValue'
     * $query->filterByPlayertwohand('%fooValue%'); // WHERE playerTwoHand LIKE '%fooValue%'
     * </code>
     *
     * @param     string $playertwohand The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function filterByPlayertwohand($playertwohand = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($playertwohand)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $playertwohand)) {
                $playertwohand = str_replace('*', '%', $playertwohand);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(GamePeer::PLAYERTWOHAND, $playertwohand, $comparison);
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
    public function filterByPlayerRelatedByPlayerone($player, $comparison = null)
    {
        if ($player instanceof Player) {
            return $this
                ->addUsingAlias(GamePeer::PLAYERONE, $player->getId(), $comparison);
        } elseif ($player instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::PLAYERONE, $player->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlayerRelatedByPlayerone() only accepts arguments of type Player or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayerRelatedByPlayerone relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinPlayerRelatedByPlayerone($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayerRelatedByPlayerone');

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
            $this->addJoinObject($join, 'PlayerRelatedByPlayerone');
        }

        return $this;
    }

    /**
     * Use the PlayerRelatedByPlayerone relation Player object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\PlayerQuery A secondary query class using the current class as primary query
     */
    public function usePlayerRelatedByPlayeroneQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayerRelatedByPlayerone($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayerRelatedByPlayerone', '\Arcium\GameBundle\Model\PlayerQuery');
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
    public function filterByPlayerRelatedByPlayertwo($player, $comparison = null)
    {
        if ($player instanceof Player) {
            return $this
                ->addUsingAlias(GamePeer::PLAYERTWO, $player->getId(), $comparison);
        } elseif ($player instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(GamePeer::PLAYERTWO, $player->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPlayerRelatedByPlayertwo() only accepts arguments of type Player or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PlayerRelatedByPlayertwo relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return GameQuery The current query, for fluid interface
     */
    public function joinPlayerRelatedByPlayertwo($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PlayerRelatedByPlayertwo');

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
            $this->addJoinObject($join, 'PlayerRelatedByPlayertwo');
        }

        return $this;
    }

    /**
     * Use the PlayerRelatedByPlayertwo relation Player object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \Arcium\GameBundle\Model\PlayerQuery A secondary query class using the current class as primary query
     */
    public function usePlayerRelatedByPlayertwoQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPlayerRelatedByPlayertwo($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PlayerRelatedByPlayertwo', '\Arcium\GameBundle\Model\PlayerQuery');
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
    public function filterByTurn($turn, $comparison = null)
    {
        if ($turn instanceof Turn) {
            return $this
                ->addUsingAlias(GamePeer::ID, $turn->getGameId(), $comparison);
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
     * @return GameQuery The current query, for fluid interface
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
