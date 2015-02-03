<?php

namespace Arcium\GameBundle\Model\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use Arcium\GameBundle\Model\Card;
use Arcium\GameBundle\Model\CardPeer;
use Arcium\GameBundle\Model\CardQuery;
use Arcium\GameBundle\Model\Game;
use Arcium\GameBundle\Model\GameQuery;

abstract class BaseCard extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Arcium\\GameBundle\\Model\\CardPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CardPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the game_id field.
     * @var        int
     */
    protected $game_id;

    /**
     * The value for the player_id field.
     * @var        int
     */
    protected $player_id;

    /**
     * The value for the cards field.
     * @var        string
     */
    protected $cards;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * @var        PropelObjectCollection|Game[] Collection to store aggregation of Game objects.
     */
    protected $collGamesRelatedByDiscard;
    protected $collGamesRelatedByDiscardPartial;

    /**
     * @var        PropelObjectCollection|Game[] Collection to store aggregation of Game objects.
     */
    protected $collGamesRelatedByDraw;
    protected $collGamesRelatedByDrawPartial;

    /**
     * @var        PropelObjectCollection|Game[] Collection to store aggregation of Game objects.
     */
    protected $collGamesRelatedByShop;
    protected $collGamesRelatedByShopPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $gamesRelatedByDiscardScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $gamesRelatedByDrawScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $gamesRelatedByShopScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [game_id] column value.
     *
     * @return int
     */
    public function getGameId()
    {

        return $this->game_id;
    }

    /**
     * Get the [player_id] column value.
     *
     * @return int
     */
    public function getPlayerId()
    {

        return $this->player_id;
    }

    /**
     * Get the [cards] column value.
     *
     * @return string
     */
    public function getCards()
    {

        return $this->cards;
    }

    /**
     * Get the [type] column value.
     *
     * @return string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Card The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CardPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [game_id] column.
     *
     * @param  int $v new value
     * @return Card The current object (for fluent API support)
     */
    public function setGameId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->game_id !== $v) {
            $this->game_id = $v;
            $this->modifiedColumns[] = CardPeer::GAME_ID;
        }


        return $this;
    } // setGameId()

    /**
     * Set the value of [player_id] column.
     *
     * @param  int $v new value
     * @return Card The current object (for fluent API support)
     */
    public function setPlayerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->player_id !== $v) {
            $this->player_id = $v;
            $this->modifiedColumns[] = CardPeer::PLAYER_ID;
        }


        return $this;
    } // setPlayerId()

    /**
     * Set the value of [cards] column.
     *
     * @param  string $v new value
     * @return Card The current object (for fluent API support)
     */
    public function setCards($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cards !== $v) {
            $this->cards = $v;
            $this->modifiedColumns[] = CardPeer::CARDS;
        }


        return $this;
    } // setCards()

    /**
     * Set the value of [type] column.
     *
     * @param  string $v new value
     * @return Card The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = CardPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->game_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->player_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->cards = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->type = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = CardPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Card object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CardPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CardPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collGamesRelatedByDiscard = null;

            $this->collGamesRelatedByDraw = null;

            $this->collGamesRelatedByShop = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CardPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CardQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CardPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CardPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->gamesRelatedByDiscardScheduledForDeletion !== null) {
                if (!$this->gamesRelatedByDiscardScheduledForDeletion->isEmpty()) {
                    foreach ($this->gamesRelatedByDiscardScheduledForDeletion as $gameRelatedByDiscard) {
                        // need to save related object because we set the relation to null
                        $gameRelatedByDiscard->save($con);
                    }
                    $this->gamesRelatedByDiscardScheduledForDeletion = null;
                }
            }

            if ($this->collGamesRelatedByDiscard !== null) {
                foreach ($this->collGamesRelatedByDiscard as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->gamesRelatedByDrawScheduledForDeletion !== null) {
                if (!$this->gamesRelatedByDrawScheduledForDeletion->isEmpty()) {
                    foreach ($this->gamesRelatedByDrawScheduledForDeletion as $gameRelatedByDraw) {
                        // need to save related object because we set the relation to null
                        $gameRelatedByDraw->save($con);
                    }
                    $this->gamesRelatedByDrawScheduledForDeletion = null;
                }
            }

            if ($this->collGamesRelatedByDraw !== null) {
                foreach ($this->collGamesRelatedByDraw as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->gamesRelatedByShopScheduledForDeletion !== null) {
                if (!$this->gamesRelatedByShopScheduledForDeletion->isEmpty()) {
                    foreach ($this->gamesRelatedByShopScheduledForDeletion as $gameRelatedByShop) {
                        // need to save related object because we set the relation to null
                        $gameRelatedByShop->save($con);
                    }
                    $this->gamesRelatedByShopScheduledForDeletion = null;
                }
            }

            if ($this->collGamesRelatedByShop !== null) {
                foreach ($this->collGamesRelatedByShop as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = CardPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CardPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CardPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(CardPeer::GAME_ID)) {
            $modifiedColumns[':p' . $index++]  = '`game_id`';
        }
        if ($this->isColumnModified(CardPeer::PLAYER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`player_id`';
        }
        if ($this->isColumnModified(CardPeer::CARDS)) {
            $modifiedColumns[':p' . $index++]  = '`cards`';
        }
        if ($this->isColumnModified(CardPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = '`type`';
        }

        $sql = sprintf(
            'INSERT INTO `cards` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`id`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`game_id`':
                        $stmt->bindValue($identifier, $this->game_id, PDO::PARAM_INT);
                        break;
                    case '`player_id`':
                        $stmt->bindValue($identifier, $this->player_id, PDO::PARAM_INT);
                        break;
                    case '`cards`':
                        $stmt->bindValue($identifier, $this->cards, PDO::PARAM_STR);
                        break;
                    case '`type`':
                        $stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = CardPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collGamesRelatedByDiscard !== null) {
                    foreach ($this->collGamesRelatedByDiscard as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collGamesRelatedByDraw !== null) {
                    foreach ($this->collGamesRelatedByDraw as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collGamesRelatedByShop !== null) {
                    foreach ($this->collGamesRelatedByShop as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CardPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getGameId();
                break;
            case 2:
                return $this->getPlayerId();
                break;
            case 3:
                return $this->getCards();
                break;
            case 4:
                return $this->getType();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Card'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Card'][$this->getPrimaryKey()] = true;
        $keys = CardPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getGameId(),
            $keys[2] => $this->getPlayerId(),
            $keys[3] => $this->getCards(),
            $keys[4] => $this->getType(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collGamesRelatedByDiscard) {
                $result['GamesRelatedByDiscard'] = $this->collGamesRelatedByDiscard->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collGamesRelatedByDraw) {
                $result['GamesRelatedByDraw'] = $this->collGamesRelatedByDraw->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collGamesRelatedByShop) {
                $result['GamesRelatedByShop'] = $this->collGamesRelatedByShop->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CardPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setGameId($value);
                break;
            case 2:
                $this->setPlayerId($value);
                break;
            case 3:
                $this->setCards($value);
                break;
            case 4:
                $this->setType($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = CardPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setGameId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPlayerId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCards($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setType($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CardPeer::DATABASE_NAME);

        if ($this->isColumnModified(CardPeer::ID)) $criteria->add(CardPeer::ID, $this->id);
        if ($this->isColumnModified(CardPeer::GAME_ID)) $criteria->add(CardPeer::GAME_ID, $this->game_id);
        if ($this->isColumnModified(CardPeer::PLAYER_ID)) $criteria->add(CardPeer::PLAYER_ID, $this->player_id);
        if ($this->isColumnModified(CardPeer::CARDS)) $criteria->add(CardPeer::CARDS, $this->cards);
        if ($this->isColumnModified(CardPeer::TYPE)) $criteria->add(CardPeer::TYPE, $this->type);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CardPeer::DATABASE_NAME);
        $criteria->add(CardPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Card (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setGameId($this->getGameId());
        $copyObj->setPlayerId($this->getPlayerId());
        $copyObj->setCards($this->getCards());
        $copyObj->setType($this->getType());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getGamesRelatedByDiscard() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGameRelatedByDiscard($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getGamesRelatedByDraw() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGameRelatedByDraw($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getGamesRelatedByShop() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGameRelatedByShop($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Card Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return CardPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CardPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('GameRelatedByDiscard' == $relationName) {
            $this->initGamesRelatedByDiscard();
        }
        if ('GameRelatedByDraw' == $relationName) {
            $this->initGamesRelatedByDraw();
        }
        if ('GameRelatedByShop' == $relationName) {
            $this->initGamesRelatedByShop();
        }
    }

    /**
     * Clears out the collGamesRelatedByDiscard collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Card The current object (for fluent API support)
     * @see        addGamesRelatedByDiscard()
     */
    public function clearGamesRelatedByDiscard()
    {
        $this->collGamesRelatedByDiscard = null; // important to set this to null since that means it is uninitialized
        $this->collGamesRelatedByDiscardPartial = null;

        return $this;
    }

    /**
     * reset is the collGamesRelatedByDiscard collection loaded partially
     *
     * @return void
     */
    public function resetPartialGamesRelatedByDiscard($v = true)
    {
        $this->collGamesRelatedByDiscardPartial = $v;
    }

    /**
     * Initializes the collGamesRelatedByDiscard collection.
     *
     * By default this just sets the collGamesRelatedByDiscard collection to an empty array (like clearcollGamesRelatedByDiscard());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGamesRelatedByDiscard($overrideExisting = true)
    {
        if (null !== $this->collGamesRelatedByDiscard && !$overrideExisting) {
            return;
        }
        $this->collGamesRelatedByDiscard = new PropelObjectCollection();
        $this->collGamesRelatedByDiscard->setModel('Game');
    }

    /**
     * Gets an array of Game objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Card is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Game[] List of Game objects
     * @throws PropelException
     */
    public function getGamesRelatedByDiscard($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByDiscardPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByDiscard || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByDiscard) {
                // return empty collection
                $this->initGamesRelatedByDiscard();
            } else {
                $collGamesRelatedByDiscard = GameQuery::create(null, $criteria)
                    ->filterByCardRelatedByDiscard($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGamesRelatedByDiscardPartial && count($collGamesRelatedByDiscard)) {
                      $this->initGamesRelatedByDiscard(false);

                      foreach ($collGamesRelatedByDiscard as $obj) {
                        if (false == $this->collGamesRelatedByDiscard->contains($obj)) {
                          $this->collGamesRelatedByDiscard->append($obj);
                        }
                      }

                      $this->collGamesRelatedByDiscardPartial = true;
                    }

                    $collGamesRelatedByDiscard->getInternalIterator()->rewind();

                    return $collGamesRelatedByDiscard;
                }

                if ($partial && $this->collGamesRelatedByDiscard) {
                    foreach ($this->collGamesRelatedByDiscard as $obj) {
                        if ($obj->isNew()) {
                            $collGamesRelatedByDiscard[] = $obj;
                        }
                    }
                }

                $this->collGamesRelatedByDiscard = $collGamesRelatedByDiscard;
                $this->collGamesRelatedByDiscardPartial = false;
            }
        }

        return $this->collGamesRelatedByDiscard;
    }

    /**
     * Sets a collection of GameRelatedByDiscard objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $gamesRelatedByDiscard A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Card The current object (for fluent API support)
     */
    public function setGamesRelatedByDiscard(PropelCollection $gamesRelatedByDiscard, PropelPDO $con = null)
    {
        $gamesRelatedByDiscardToDelete = $this->getGamesRelatedByDiscard(new Criteria(), $con)->diff($gamesRelatedByDiscard);


        $this->gamesRelatedByDiscardScheduledForDeletion = $gamesRelatedByDiscardToDelete;

        foreach ($gamesRelatedByDiscardToDelete as $gameRelatedByDiscardRemoved) {
            $gameRelatedByDiscardRemoved->setCardRelatedByDiscard(null);
        }

        $this->collGamesRelatedByDiscard = null;
        foreach ($gamesRelatedByDiscard as $gameRelatedByDiscard) {
            $this->addGameRelatedByDiscard($gameRelatedByDiscard);
        }

        $this->collGamesRelatedByDiscard = $gamesRelatedByDiscard;
        $this->collGamesRelatedByDiscardPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Game objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Game objects.
     * @throws PropelException
     */
    public function countGamesRelatedByDiscard(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByDiscardPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByDiscard || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByDiscard) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getGamesRelatedByDiscard());
            }
            $query = GameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCardRelatedByDiscard($this)
                ->count($con);
        }

        return count($this->collGamesRelatedByDiscard);
    }

    /**
     * Method called to associate a Game object to this object
     * through the Game foreign key attribute.
     *
     * @param    Game $l Game
     * @return Card The current object (for fluent API support)
     */
    public function addGameRelatedByDiscard(Game $l)
    {
        if ($this->collGamesRelatedByDiscard === null) {
            $this->initGamesRelatedByDiscard();
            $this->collGamesRelatedByDiscardPartial = true;
        }

        if (!in_array($l, $this->collGamesRelatedByDiscard->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGameRelatedByDiscard($l);

            if ($this->gamesRelatedByDiscardScheduledForDeletion and $this->gamesRelatedByDiscardScheduledForDeletion->contains($l)) {
                $this->gamesRelatedByDiscardScheduledForDeletion->remove($this->gamesRelatedByDiscardScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	GameRelatedByDiscard $gameRelatedByDiscard The gameRelatedByDiscard object to add.
     */
    protected function doAddGameRelatedByDiscard($gameRelatedByDiscard)
    {
        $this->collGamesRelatedByDiscard[]= $gameRelatedByDiscard;
        $gameRelatedByDiscard->setCardRelatedByDiscard($this);
    }

    /**
     * @param	GameRelatedByDiscard $gameRelatedByDiscard The gameRelatedByDiscard object to remove.
     * @return Card The current object (for fluent API support)
     */
    public function removeGameRelatedByDiscard($gameRelatedByDiscard)
    {
        if ($this->getGamesRelatedByDiscard()->contains($gameRelatedByDiscard)) {
            $this->collGamesRelatedByDiscard->remove($this->collGamesRelatedByDiscard->search($gameRelatedByDiscard));
            if (null === $this->gamesRelatedByDiscardScheduledForDeletion) {
                $this->gamesRelatedByDiscardScheduledForDeletion = clone $this->collGamesRelatedByDiscard;
                $this->gamesRelatedByDiscardScheduledForDeletion->clear();
            }
            $this->gamesRelatedByDiscardScheduledForDeletion[]= $gameRelatedByDiscard;
            $gameRelatedByDiscard->setCardRelatedByDiscard(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByDiscard from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByDiscardJoinTurnRelatedByLastTurnId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('TurnRelatedByLastTurnId', $join_behavior);

        return $this->getGamesRelatedByDiscard($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByDiscard from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByDiscardJoinPlayerRelatedByPlayerOneId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerOneId', $join_behavior);

        return $this->getGamesRelatedByDiscard($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByDiscard from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByDiscardJoinPlayerRelatedByPlayerTwoId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerTwoId', $join_behavior);

        return $this->getGamesRelatedByDiscard($query, $con);
    }

    /**
     * Clears out the collGamesRelatedByDraw collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Card The current object (for fluent API support)
     * @see        addGamesRelatedByDraw()
     */
    public function clearGamesRelatedByDraw()
    {
        $this->collGamesRelatedByDraw = null; // important to set this to null since that means it is uninitialized
        $this->collGamesRelatedByDrawPartial = null;

        return $this;
    }

    /**
     * reset is the collGamesRelatedByDraw collection loaded partially
     *
     * @return void
     */
    public function resetPartialGamesRelatedByDraw($v = true)
    {
        $this->collGamesRelatedByDrawPartial = $v;
    }

    /**
     * Initializes the collGamesRelatedByDraw collection.
     *
     * By default this just sets the collGamesRelatedByDraw collection to an empty array (like clearcollGamesRelatedByDraw());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGamesRelatedByDraw($overrideExisting = true)
    {
        if (null !== $this->collGamesRelatedByDraw && !$overrideExisting) {
            return;
        }
        $this->collGamesRelatedByDraw = new PropelObjectCollection();
        $this->collGamesRelatedByDraw->setModel('Game');
    }

    /**
     * Gets an array of Game objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Card is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Game[] List of Game objects
     * @throws PropelException
     */
    public function getGamesRelatedByDraw($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByDrawPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByDraw || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByDraw) {
                // return empty collection
                $this->initGamesRelatedByDraw();
            } else {
                $collGamesRelatedByDraw = GameQuery::create(null, $criteria)
                    ->filterByCardRelatedByDraw($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGamesRelatedByDrawPartial && count($collGamesRelatedByDraw)) {
                      $this->initGamesRelatedByDraw(false);

                      foreach ($collGamesRelatedByDraw as $obj) {
                        if (false == $this->collGamesRelatedByDraw->contains($obj)) {
                          $this->collGamesRelatedByDraw->append($obj);
                        }
                      }

                      $this->collGamesRelatedByDrawPartial = true;
                    }

                    $collGamesRelatedByDraw->getInternalIterator()->rewind();

                    return $collGamesRelatedByDraw;
                }

                if ($partial && $this->collGamesRelatedByDraw) {
                    foreach ($this->collGamesRelatedByDraw as $obj) {
                        if ($obj->isNew()) {
                            $collGamesRelatedByDraw[] = $obj;
                        }
                    }
                }

                $this->collGamesRelatedByDraw = $collGamesRelatedByDraw;
                $this->collGamesRelatedByDrawPartial = false;
            }
        }

        return $this->collGamesRelatedByDraw;
    }

    /**
     * Sets a collection of GameRelatedByDraw objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $gamesRelatedByDraw A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Card The current object (for fluent API support)
     */
    public function setGamesRelatedByDraw(PropelCollection $gamesRelatedByDraw, PropelPDO $con = null)
    {
        $gamesRelatedByDrawToDelete = $this->getGamesRelatedByDraw(new Criteria(), $con)->diff($gamesRelatedByDraw);


        $this->gamesRelatedByDrawScheduledForDeletion = $gamesRelatedByDrawToDelete;

        foreach ($gamesRelatedByDrawToDelete as $gameRelatedByDrawRemoved) {
            $gameRelatedByDrawRemoved->setCardRelatedByDraw(null);
        }

        $this->collGamesRelatedByDraw = null;
        foreach ($gamesRelatedByDraw as $gameRelatedByDraw) {
            $this->addGameRelatedByDraw($gameRelatedByDraw);
        }

        $this->collGamesRelatedByDraw = $gamesRelatedByDraw;
        $this->collGamesRelatedByDrawPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Game objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Game objects.
     * @throws PropelException
     */
    public function countGamesRelatedByDraw(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByDrawPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByDraw || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByDraw) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getGamesRelatedByDraw());
            }
            $query = GameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCardRelatedByDraw($this)
                ->count($con);
        }

        return count($this->collGamesRelatedByDraw);
    }

    /**
     * Method called to associate a Game object to this object
     * through the Game foreign key attribute.
     *
     * @param    Game $l Game
     * @return Card The current object (for fluent API support)
     */
    public function addGameRelatedByDraw(Game $l)
    {
        if ($this->collGamesRelatedByDraw === null) {
            $this->initGamesRelatedByDraw();
            $this->collGamesRelatedByDrawPartial = true;
        }

        if (!in_array($l, $this->collGamesRelatedByDraw->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGameRelatedByDraw($l);

            if ($this->gamesRelatedByDrawScheduledForDeletion and $this->gamesRelatedByDrawScheduledForDeletion->contains($l)) {
                $this->gamesRelatedByDrawScheduledForDeletion->remove($this->gamesRelatedByDrawScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	GameRelatedByDraw $gameRelatedByDraw The gameRelatedByDraw object to add.
     */
    protected function doAddGameRelatedByDraw($gameRelatedByDraw)
    {
        $this->collGamesRelatedByDraw[]= $gameRelatedByDraw;
        $gameRelatedByDraw->setCardRelatedByDraw($this);
    }

    /**
     * @param	GameRelatedByDraw $gameRelatedByDraw The gameRelatedByDraw object to remove.
     * @return Card The current object (for fluent API support)
     */
    public function removeGameRelatedByDraw($gameRelatedByDraw)
    {
        if ($this->getGamesRelatedByDraw()->contains($gameRelatedByDraw)) {
            $this->collGamesRelatedByDraw->remove($this->collGamesRelatedByDraw->search($gameRelatedByDraw));
            if (null === $this->gamesRelatedByDrawScheduledForDeletion) {
                $this->gamesRelatedByDrawScheduledForDeletion = clone $this->collGamesRelatedByDraw;
                $this->gamesRelatedByDrawScheduledForDeletion->clear();
            }
            $this->gamesRelatedByDrawScheduledForDeletion[]= $gameRelatedByDraw;
            $gameRelatedByDraw->setCardRelatedByDraw(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByDraw from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByDrawJoinTurnRelatedByLastTurnId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('TurnRelatedByLastTurnId', $join_behavior);

        return $this->getGamesRelatedByDraw($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByDraw from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByDrawJoinPlayerRelatedByPlayerOneId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerOneId', $join_behavior);

        return $this->getGamesRelatedByDraw($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByDraw from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByDrawJoinPlayerRelatedByPlayerTwoId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerTwoId', $join_behavior);

        return $this->getGamesRelatedByDraw($query, $con);
    }

    /**
     * Clears out the collGamesRelatedByShop collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Card The current object (for fluent API support)
     * @see        addGamesRelatedByShop()
     */
    public function clearGamesRelatedByShop()
    {
        $this->collGamesRelatedByShop = null; // important to set this to null since that means it is uninitialized
        $this->collGamesRelatedByShopPartial = null;

        return $this;
    }

    /**
     * reset is the collGamesRelatedByShop collection loaded partially
     *
     * @return void
     */
    public function resetPartialGamesRelatedByShop($v = true)
    {
        $this->collGamesRelatedByShopPartial = $v;
    }

    /**
     * Initializes the collGamesRelatedByShop collection.
     *
     * By default this just sets the collGamesRelatedByShop collection to an empty array (like clearcollGamesRelatedByShop());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGamesRelatedByShop($overrideExisting = true)
    {
        if (null !== $this->collGamesRelatedByShop && !$overrideExisting) {
            return;
        }
        $this->collGamesRelatedByShop = new PropelObjectCollection();
        $this->collGamesRelatedByShop->setModel('Game');
    }

    /**
     * Gets an array of Game objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Card is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Game[] List of Game objects
     * @throws PropelException
     */
    public function getGamesRelatedByShop($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByShopPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByShop || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByShop) {
                // return empty collection
                $this->initGamesRelatedByShop();
            } else {
                $collGamesRelatedByShop = GameQuery::create(null, $criteria)
                    ->filterByCardRelatedByShop($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGamesRelatedByShopPartial && count($collGamesRelatedByShop)) {
                      $this->initGamesRelatedByShop(false);

                      foreach ($collGamesRelatedByShop as $obj) {
                        if (false == $this->collGamesRelatedByShop->contains($obj)) {
                          $this->collGamesRelatedByShop->append($obj);
                        }
                      }

                      $this->collGamesRelatedByShopPartial = true;
                    }

                    $collGamesRelatedByShop->getInternalIterator()->rewind();

                    return $collGamesRelatedByShop;
                }

                if ($partial && $this->collGamesRelatedByShop) {
                    foreach ($this->collGamesRelatedByShop as $obj) {
                        if ($obj->isNew()) {
                            $collGamesRelatedByShop[] = $obj;
                        }
                    }
                }

                $this->collGamesRelatedByShop = $collGamesRelatedByShop;
                $this->collGamesRelatedByShopPartial = false;
            }
        }

        return $this->collGamesRelatedByShop;
    }

    /**
     * Sets a collection of GameRelatedByShop objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $gamesRelatedByShop A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Card The current object (for fluent API support)
     */
    public function setGamesRelatedByShop(PropelCollection $gamesRelatedByShop, PropelPDO $con = null)
    {
        $gamesRelatedByShopToDelete = $this->getGamesRelatedByShop(new Criteria(), $con)->diff($gamesRelatedByShop);


        $this->gamesRelatedByShopScheduledForDeletion = $gamesRelatedByShopToDelete;

        foreach ($gamesRelatedByShopToDelete as $gameRelatedByShopRemoved) {
            $gameRelatedByShopRemoved->setCardRelatedByShop(null);
        }

        $this->collGamesRelatedByShop = null;
        foreach ($gamesRelatedByShop as $gameRelatedByShop) {
            $this->addGameRelatedByShop($gameRelatedByShop);
        }

        $this->collGamesRelatedByShop = $gamesRelatedByShop;
        $this->collGamesRelatedByShopPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Game objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Game objects.
     * @throws PropelException
     */
    public function countGamesRelatedByShop(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByShopPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByShop || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByShop) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getGamesRelatedByShop());
            }
            $query = GameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCardRelatedByShop($this)
                ->count($con);
        }

        return count($this->collGamesRelatedByShop);
    }

    /**
     * Method called to associate a Game object to this object
     * through the Game foreign key attribute.
     *
     * @param    Game $l Game
     * @return Card The current object (for fluent API support)
     */
    public function addGameRelatedByShop(Game $l)
    {
        if ($this->collGamesRelatedByShop === null) {
            $this->initGamesRelatedByShop();
            $this->collGamesRelatedByShopPartial = true;
        }

        if (!in_array($l, $this->collGamesRelatedByShop->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGameRelatedByShop($l);

            if ($this->gamesRelatedByShopScheduledForDeletion and $this->gamesRelatedByShopScheduledForDeletion->contains($l)) {
                $this->gamesRelatedByShopScheduledForDeletion->remove($this->gamesRelatedByShopScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	GameRelatedByShop $gameRelatedByShop The gameRelatedByShop object to add.
     */
    protected function doAddGameRelatedByShop($gameRelatedByShop)
    {
        $this->collGamesRelatedByShop[]= $gameRelatedByShop;
        $gameRelatedByShop->setCardRelatedByShop($this);
    }

    /**
     * @param	GameRelatedByShop $gameRelatedByShop The gameRelatedByShop object to remove.
     * @return Card The current object (for fluent API support)
     */
    public function removeGameRelatedByShop($gameRelatedByShop)
    {
        if ($this->getGamesRelatedByShop()->contains($gameRelatedByShop)) {
            $this->collGamesRelatedByShop->remove($this->collGamesRelatedByShop->search($gameRelatedByShop));
            if (null === $this->gamesRelatedByShopScheduledForDeletion) {
                $this->gamesRelatedByShopScheduledForDeletion = clone $this->collGamesRelatedByShop;
                $this->gamesRelatedByShopScheduledForDeletion->clear();
            }
            $this->gamesRelatedByShopScheduledForDeletion[]= $gameRelatedByShop;
            $gameRelatedByShop->setCardRelatedByShop(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByShop from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByShopJoinTurnRelatedByLastTurnId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('TurnRelatedByLastTurnId', $join_behavior);

        return $this->getGamesRelatedByShop($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByShop from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByShopJoinPlayerRelatedByPlayerOneId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerOneId', $join_behavior);

        return $this->getGamesRelatedByShop($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Card is new, it will return
     * an empty collection; or if this Card has previously
     * been saved, it will retrieve related GamesRelatedByShop from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Card.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByShopJoinPlayerRelatedByPlayerTwoId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerTwoId', $join_behavior);

        return $this->getGamesRelatedByShop($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->game_id = null;
        $this->player_id = null;
        $this->cards = null;
        $this->type = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collGamesRelatedByDiscard) {
                foreach ($this->collGamesRelatedByDiscard as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGamesRelatedByDraw) {
                foreach ($this->collGamesRelatedByDraw as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGamesRelatedByShop) {
                foreach ($this->collGamesRelatedByShop as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collGamesRelatedByDiscard instanceof PropelCollection) {
            $this->collGamesRelatedByDiscard->clearIterator();
        }
        $this->collGamesRelatedByDiscard = null;
        if ($this->collGamesRelatedByDraw instanceof PropelCollection) {
            $this->collGamesRelatedByDraw->clearIterator();
        }
        $this->collGamesRelatedByDraw = null;
        if ($this->collGamesRelatedByShop instanceof PropelCollection) {
            $this->collGamesRelatedByShop->clearIterator();
        }
        $this->collGamesRelatedByShop = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CardPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
