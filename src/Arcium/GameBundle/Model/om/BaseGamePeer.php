<?php

namespace Arcium\GameBundle\Model\om;

use \BasePeer;
use \Criteria;
use \PDO;
use \PDOStatement;
use \Propel;
use \PropelException;
use \PropelPDO;
use Arcium\GameBundle\Model\CardPeer;
use Arcium\GameBundle\Model\Game;
use Arcium\GameBundle\Model\GamePeer;
use Arcium\GameBundle\Model\PlayerPeer;
use Arcium\GameBundle\Model\TurnPeer;
use Arcium\GameBundle\Model\map\GameTableMap;

abstract class BaseGamePeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'default';

    /** the table name for this class */
    const TABLE_NAME = 'games';

    /** the related Propel class for this table */
    const OM_CLASS = 'Arcium\\GameBundle\\Model\\Game';

    /** the related TableMap class for this table */
    const TM_CLASS = 'Arcium\\GameBundle\\Model\\map\\GameTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 7;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 7;

    /** the column name for the id field */
    const ID = 'games.id';

    /** the column name for the draw field */
    const DRAW = 'games.draw';

    /** the column name for the discard field */
    const DISCARD = 'games.discard';

    /** the column name for the shop field */
    const SHOP = 'games.shop';

    /** the column name for the player_one_id field */
    const PLAYER_ONE_ID = 'games.player_one_id';

    /** the column name for the player_two_id field */
    const PLAYER_TWO_ID = 'games.player_two_id';

    /** the column name for the last_turn_id field */
    const LAST_TURN_ID = 'games.last_turn_id';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identity map to hold any loaded instances of Game objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Game[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. GamePeer::$fieldNames[GamePeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Draw', 'Discard', 'Shop', 'PlayerOneId', 'PlayerTwoId', 'LastTurnId', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'draw', 'discard', 'shop', 'playerOneId', 'playerTwoId', 'lastTurnId', ),
        BasePeer::TYPE_COLNAME => array (GamePeer::ID, GamePeer::DRAW, GamePeer::DISCARD, GamePeer::SHOP, GamePeer::PLAYER_ONE_ID, GamePeer::PLAYER_TWO_ID, GamePeer::LAST_TURN_ID, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'DRAW', 'DISCARD', 'SHOP', 'PLAYER_ONE_ID', 'PLAYER_TWO_ID', 'LAST_TURN_ID', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'draw', 'discard', 'shop', 'player_one_id', 'player_two_id', 'last_turn_id', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. GamePeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Draw' => 1, 'Discard' => 2, 'Shop' => 3, 'PlayerOneId' => 4, 'PlayerTwoId' => 5, 'LastTurnId' => 6, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'draw' => 1, 'discard' => 2, 'shop' => 3, 'playerOneId' => 4, 'playerTwoId' => 5, 'lastTurnId' => 6, ),
        BasePeer::TYPE_COLNAME => array (GamePeer::ID => 0, GamePeer::DRAW => 1, GamePeer::DISCARD => 2, GamePeer::SHOP => 3, GamePeer::PLAYER_ONE_ID => 4, GamePeer::PLAYER_TWO_ID => 5, GamePeer::LAST_TURN_ID => 6, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'DRAW' => 1, 'DISCARD' => 2, 'SHOP' => 3, 'PLAYER_ONE_ID' => 4, 'PLAYER_TWO_ID' => 5, 'LAST_TURN_ID' => 6, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'draw' => 1, 'discard' => 2, 'shop' => 3, 'player_one_id' => 4, 'player_two_id' => 5, 'last_turn_id' => 6, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = GamePeer::getFieldNames($toType);
        $key = isset(GamePeer::$fieldKeys[$fromType][$name]) ? GamePeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(GamePeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, GamePeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return GamePeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. GamePeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(GamePeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(GamePeer::ID);
            $criteria->addSelectColumn(GamePeer::DRAW);
            $criteria->addSelectColumn(GamePeer::DISCARD);
            $criteria->addSelectColumn(GamePeer::SHOP);
            $criteria->addSelectColumn(GamePeer::PLAYER_ONE_ID);
            $criteria->addSelectColumn(GamePeer::PLAYER_TWO_ID);
            $criteria->addSelectColumn(GamePeer::LAST_TURN_ID);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.draw');
            $criteria->addSelectColumn($alias . '.discard');
            $criteria->addSelectColumn($alias . '.shop');
            $criteria->addSelectColumn($alias . '.player_one_id');
            $criteria->addSelectColumn($alias . '.player_two_id');
            $criteria->addSelectColumn($alias . '.last_turn_id');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(GamePeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return Game
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = GamePeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return GamePeer::populateObjects(GamePeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement directly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            GamePeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param Game $obj A Game object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            GamePeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A Game object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Game) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Game object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(GamePeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return Game Found object or null if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(GamePeer::$instances[$key])) {
                return GamePeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }

    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool($and_clear_all_references = false)
    {
      if ($and_clear_all_references) {
        foreach (GamePeer::$instances as $instance) {
          $instance->clearAllReferences(true);
        }
      }
        GamePeer::$instances = array();
    }

    /**
     * Method to invalidate the instance pool of all tables related to games
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or null if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return null.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = GamePeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = GamePeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                GamePeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (Game object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = GamePeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = GamePeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + GamePeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = GamePeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            GamePeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related CardRelatedByDiscard table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCardRelatedByDiscard(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CardRelatedByDraw table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCardRelatedByDraw(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related TurnRelatedByLastTurnId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinTurnRelatedByLastTurnId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related PlayerRelatedByPlayerOneId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinPlayerRelatedByPlayerOneId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related PlayerRelatedByPlayerTwoId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinPlayerRelatedByPlayerTwoId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CardRelatedByShop table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinCardRelatedByShop(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Game objects pre-filled with their Card objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCardRelatedByDiscard(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol = GamePeer::NUM_HYDRATE_COLUMNS;
        CardPeer::addSelectColumns($criteria);

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CardPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Game) to $obj2 (Card)
                $obj2->addGameRelatedByDiscard($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with their Card objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCardRelatedByDraw(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol = GamePeer::NUM_HYDRATE_COLUMNS;
        CardPeer::addSelectColumns($criteria);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CardPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Game) to $obj2 (Card)
                $obj2->addGameRelatedByDraw($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with their Turn objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinTurnRelatedByLastTurnId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol = GamePeer::NUM_HYDRATE_COLUMNS;
        TurnPeer::addSelectColumns($criteria);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = TurnPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = TurnPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    TurnPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Game) to $obj2 (Turn)
                $obj2->addGameRelatedByLastTurnId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with their Player objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinPlayerRelatedByPlayerOneId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol = GamePeer::NUM_HYDRATE_COLUMNS;
        PlayerPeer::addSelectColumns($criteria);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = PlayerPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = PlayerPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    PlayerPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Game) to $obj2 (Player)
                $obj2->addGameRelatedByPlayerOneId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with their Player objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinPlayerRelatedByPlayerTwoId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol = GamePeer::NUM_HYDRATE_COLUMNS;
        PlayerPeer::addSelectColumns($criteria);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = PlayerPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = PlayerPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    PlayerPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Game) to $obj2 (Player)
                $obj2->addGameRelatedByPlayerTwoId($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with their Card objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinCardRelatedByShop(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol = GamePeer::NUM_HYDRATE_COLUMNS;
        CardPeer::addSelectColumns($criteria);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = CardPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Game) to $obj2 (Card)
                $obj2->addGameRelatedByShop($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }

    /**
     * Selects a collection of Game objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CardPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CardPeer::NUM_HYDRATE_COLUMNS;

        TurnPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + TurnPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol7 = $startcol6 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol8 = $startcol7 + CardPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Card rows

            $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = CardPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Card)
                $obj2->addGameRelatedByDiscard($obj1);
            } // if joined row not null

            // Add objects for joined Card rows

            $key3 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol3);
            if ($key3 !== null) {
                $obj3 = CardPeer::getInstanceFromPool($key3);
                if (!$obj3) {

                    $cls = CardPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CardPeer::addInstanceToPool($obj3, $key3);
                } // if obj3 loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Card)
                $obj3->addGameRelatedByDraw($obj1);
            } // if joined row not null

            // Add objects for joined Turn rows

            $key4 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol4);
            if ($key4 !== null) {
                $obj4 = TurnPeer::getInstanceFromPool($key4);
                if (!$obj4) {

                    $cls = TurnPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    TurnPeer::addInstanceToPool($obj4, $key4);
                } // if obj4 loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Turn)
                $obj4->addGameRelatedByLastTurnId($obj1);
            } // if joined row not null

            // Add objects for joined Player rows

            $key5 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol5);
            if ($key5 !== null) {
                $obj5 = PlayerPeer::getInstanceFromPool($key5);
                if (!$obj5) {

                    $cls = PlayerPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    PlayerPeer::addInstanceToPool($obj5, $key5);
                } // if obj5 loaded

                // Add the $obj1 (Game) to the collection in $obj5 (Player)
                $obj5->addGameRelatedByPlayerOneId($obj1);
            } // if joined row not null

            // Add objects for joined Player rows

            $key6 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol6);
            if ($key6 !== null) {
                $obj6 = PlayerPeer::getInstanceFromPool($key6);
                if (!$obj6) {

                    $cls = PlayerPeer::getOMClass();

                    $obj6 = new $cls();
                    $obj6->hydrate($row, $startcol6);
                    PlayerPeer::addInstanceToPool($obj6, $key6);
                } // if obj6 loaded

                // Add the $obj1 (Game) to the collection in $obj6 (Player)
                $obj6->addGameRelatedByPlayerTwoId($obj1);
            } // if joined row not null

            // Add objects for joined Card rows

            $key7 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol7);
            if ($key7 !== null) {
                $obj7 = CardPeer::getInstanceFromPool($key7);
                if (!$obj7) {

                    $cls = CardPeer::getOMClass();

                    $obj7 = new $cls();
                    $obj7->hydrate($row, $startcol7);
                    CardPeer::addInstanceToPool($obj7, $key7);
                } // if obj7 loaded

                // Add the $obj1 (Game) to the collection in $obj7 (Card)
                $obj7->addGameRelatedByShop($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CardRelatedByDiscard table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCardRelatedByDiscard(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CardRelatedByDraw table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCardRelatedByDraw(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related TurnRelatedByLastTurnId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptTurnRelatedByLastTurnId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related PlayerRelatedByPlayerOneId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptPlayerRelatedByPlayerOneId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related PlayerRelatedByPlayerTwoId table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptPlayerRelatedByPlayerTwoId(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Returns the number of rows matching criteria, joining the related CardRelatedByShop table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAllExceptCardRelatedByShop(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(GamePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            GamePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY should not affect count

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }


    /**
     * Selects a collection of Game objects pre-filled with all related objects except CardRelatedByDiscard.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCardRelatedByDiscard(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        TurnPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TurnPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Turn rows

                $key2 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TurnPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TurnPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TurnPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Turn)
                $obj2->addGameRelatedByLastTurnId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key3 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = PlayerPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = PlayerPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    PlayerPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Player)
                $obj3->addGameRelatedByPlayerOneId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key4 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = PlayerPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = PlayerPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    PlayerPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Player)
                $obj4->addGameRelatedByPlayerTwoId($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with all related objects except CardRelatedByDraw.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCardRelatedByDraw(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        TurnPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TurnPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Turn rows

                $key2 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TurnPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TurnPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TurnPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Turn)
                $obj2->addGameRelatedByLastTurnId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key3 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = PlayerPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = PlayerPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    PlayerPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Player)
                $obj3->addGameRelatedByPlayerOneId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key4 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = PlayerPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = PlayerPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    PlayerPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Player)
                $obj4->addGameRelatedByPlayerTwoId($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with all related objects except TurnRelatedByLastTurnId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptTurnRelatedByLastTurnId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CardPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CardPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol7 = $startcol6 + CardPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Card rows

                $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CardPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Card)
                $obj2->addGameRelatedByDiscard($obj1);

            } // if joined row is not null

                // Add objects for joined Card rows

                $key3 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CardPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CardPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CardPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Card)
                $obj3->addGameRelatedByDraw($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key4 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = PlayerPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = PlayerPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    PlayerPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Player)
                $obj4->addGameRelatedByPlayerOneId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key5 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = PlayerPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = PlayerPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    PlayerPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Game) to the collection in $obj5 (Player)
                $obj5->addGameRelatedByPlayerTwoId($obj1);

            } // if joined row is not null

                // Add objects for joined Card rows

                $key6 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol6);
                if ($key6 !== null) {
                    $obj6 = CardPeer::getInstanceFromPool($key6);
                    if (!$obj6) {

                        $cls = CardPeer::getOMClass();

                    $obj6 = new $cls();
                    $obj6->hydrate($row, $startcol6);
                    CardPeer::addInstanceToPool($obj6, $key6);
                } // if $obj6 already loaded

                // Add the $obj1 (Game) to the collection in $obj6 (Card)
                $obj6->addGameRelatedByShop($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with all related objects except PlayerRelatedByPlayerOneId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptPlayerRelatedByPlayerOneId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CardPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CardPeer::NUM_HYDRATE_COLUMNS;

        TurnPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + TurnPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + CardPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Card rows

                $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CardPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Card)
                $obj2->addGameRelatedByDiscard($obj1);

            } // if joined row is not null

                // Add objects for joined Card rows

                $key3 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CardPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CardPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CardPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Card)
                $obj3->addGameRelatedByDraw($obj1);

            } // if joined row is not null

                // Add objects for joined Turn rows

                $key4 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = TurnPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = TurnPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    TurnPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Turn)
                $obj4->addGameRelatedByLastTurnId($obj1);

            } // if joined row is not null

                // Add objects for joined Card rows

                $key5 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = CardPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = CardPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    CardPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Game) to the collection in $obj5 (Card)
                $obj5->addGameRelatedByShop($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with all related objects except PlayerRelatedByPlayerTwoId.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptPlayerRelatedByPlayerTwoId(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + CardPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + CardPeer::NUM_HYDRATE_COLUMNS;

        TurnPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + TurnPeer::NUM_HYDRATE_COLUMNS;

        CardPeer::addSelectColumns($criteria);
        $startcol6 = $startcol5 + CardPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::DISCARD, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::DRAW, CardPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::SHOP, CardPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Card rows

                $key2 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = CardPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = CardPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    CardPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Card)
                $obj2->addGameRelatedByDiscard($obj1);

            } // if joined row is not null

                // Add objects for joined Card rows

                $key3 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = CardPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = CardPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    CardPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Card)
                $obj3->addGameRelatedByDraw($obj1);

            } // if joined row is not null

                // Add objects for joined Turn rows

                $key4 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = TurnPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = TurnPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    TurnPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Turn)
                $obj4->addGameRelatedByLastTurnId($obj1);

            } // if joined row is not null

                // Add objects for joined Card rows

                $key5 = CardPeer::getPrimaryKeyHashFromRow($row, $startcol5);
                if ($key5 !== null) {
                    $obj5 = CardPeer::getInstanceFromPool($key5);
                    if (!$obj5) {

                        $cls = CardPeer::getOMClass();

                    $obj5 = new $cls();
                    $obj5->hydrate($row, $startcol5);
                    CardPeer::addInstanceToPool($obj5, $key5);
                } // if $obj5 already loaded

                // Add the $obj1 (Game) to the collection in $obj5 (Card)
                $obj5->addGameRelatedByShop($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Selects a collection of Game objects pre-filled with all related objects except CardRelatedByShop.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Game objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAllExceptCardRelatedByShop(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        // $criteria->getDbName() will return the same object if not set to another value
        // so == check is okay and faster
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(GamePeer::DATABASE_NAME);
        }

        GamePeer::addSelectColumns($criteria);
        $startcol2 = GamePeer::NUM_HYDRATE_COLUMNS;

        TurnPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + TurnPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol4 = $startcol3 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        PlayerPeer::addSelectColumns($criteria);
        $startcol5 = $startcol4 + PlayerPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(GamePeer::LAST_TURN_ID, TurnPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_ONE_ID, PlayerPeer::ID, $join_behavior);

        $criteria->addJoin(GamePeer::PLAYER_TWO_ID, PlayerPeer::ID, $join_behavior);


        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = GamePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = GamePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = GamePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                GamePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

                // Add objects for joined Turn rows

                $key2 = TurnPeer::getPrimaryKeyHashFromRow($row, $startcol2);
                if ($key2 !== null) {
                    $obj2 = TurnPeer::getInstanceFromPool($key2);
                    if (!$obj2) {

                        $cls = TurnPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    TurnPeer::addInstanceToPool($obj2, $key2);
                } // if $obj2 already loaded

                // Add the $obj1 (Game) to the collection in $obj2 (Turn)
                $obj2->addGameRelatedByLastTurnId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key3 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol3);
                if ($key3 !== null) {
                    $obj3 = PlayerPeer::getInstanceFromPool($key3);
                    if (!$obj3) {

                        $cls = PlayerPeer::getOMClass();

                    $obj3 = new $cls();
                    $obj3->hydrate($row, $startcol3);
                    PlayerPeer::addInstanceToPool($obj3, $key3);
                } // if $obj3 already loaded

                // Add the $obj1 (Game) to the collection in $obj3 (Player)
                $obj3->addGameRelatedByPlayerOneId($obj1);

            } // if joined row is not null

                // Add objects for joined Player rows

                $key4 = PlayerPeer::getPrimaryKeyHashFromRow($row, $startcol4);
                if ($key4 !== null) {
                    $obj4 = PlayerPeer::getInstanceFromPool($key4);
                    if (!$obj4) {

                        $cls = PlayerPeer::getOMClass();

                    $obj4 = new $cls();
                    $obj4->hydrate($row, $startcol4);
                    PlayerPeer::addInstanceToPool($obj4, $key4);
                } // if $obj4 already loaded

                // Add the $obj1 (Game) to the collection in $obj4 (Player)
                $obj4->addGameRelatedByPlayerTwoId($obj1);

            } // if joined row is not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(GamePeer::DATABASE_NAME)->getTable(GamePeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseGamePeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseGamePeer::TABLE_NAME)) {
        $dbMap->addTableObject(new \Arcium\GameBundle\Model\map\GameTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass($row = 0, $colnum = 0)
    {
        return GamePeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Game or Criteria object.
     *
     * @param      mixed $values Criteria or Game object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Game object
        }

        if ($criteria->containsKey(GamePeer::ID) && $criteria->keyContainsValue(GamePeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.GamePeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a Game or Criteria object.
     *
     * @param      mixed $values Criteria or Game object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(GamePeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(GamePeer::ID);
            $value = $criteria->remove(GamePeer::ID);
            if ($value) {
                $selectCriteria->add(GamePeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(GamePeer::TABLE_NAME);
            }

        } else { // $values is Game object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the games table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(GamePeer::TABLE_NAME, $con, GamePeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            GamePeer::clearInstancePool();
            GamePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Game or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Game object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            GamePeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Game) { // it's a model object
            // invalidate the cache for this single object
            GamePeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(GamePeer::DATABASE_NAME);
            $criteria->add(GamePeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                GamePeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(GamePeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            GamePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Game object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param Game $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(GamePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(GamePeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(GamePeer::DATABASE_NAME, GamePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Game
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = GamePeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(GamePeer::DATABASE_NAME);
        $criteria->add(GamePeer::ID, $pk);

        $v = GamePeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Game[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(GamePeer::DATABASE_NAME);
            $criteria->add(GamePeer::ID, $pks, Criteria::IN);
            $objs = GamePeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseGamePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseGamePeer::buildTableMap();

