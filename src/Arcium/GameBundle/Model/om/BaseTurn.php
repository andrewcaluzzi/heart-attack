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
use Arcium\GameBundle\Model\Game;
use Arcium\GameBundle\Model\GameQuery;
use Arcium\GameBundle\Model\Player;
use Arcium\GameBundle\Model\PlayerQuery;
use Arcium\GameBundle\Model\Turn;
use Arcium\GameBundle\Model\TurnPeer;
use Arcium\GameBundle\Model\TurnQuery;

abstract class BaseTurn extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Arcium\\GameBundle\\Model\\TurnPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        TurnPeer
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
     * The value for the phase field.
     * @var        string
     */
    protected $phase;

    /**
     * The value for the cards field.
     * @var        string
     */
    protected $cards;

    /**
     * @var        Game
     */
    protected $aGameRelatedByGameId;

    /**
     * @var        Player
     */
    protected $aPlayer;

    /**
     * @var        PropelObjectCollection|Game[] Collection to store aggregation of Game objects.
     */
    protected $collGamesRelatedByLastTurnId;
    protected $collGamesRelatedByLastTurnIdPartial;

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
    protected $gamesRelatedByLastTurnIdScheduledForDeletion = null;

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
     * Get the [phase] column value.
     *
     * @return string
     */
    public function getPhase()
    {

        return $this->phase;
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
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Turn The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = TurnPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [game_id] column.
     *
     * @param  int $v new value
     * @return Turn The current object (for fluent API support)
     */
    public function setGameId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->game_id !== $v) {
            $this->game_id = $v;
            $this->modifiedColumns[] = TurnPeer::GAME_ID;
        }

        if ($this->aGameRelatedByGameId !== null && $this->aGameRelatedByGameId->getId() !== $v) {
            $this->aGameRelatedByGameId = null;
        }


        return $this;
    } // setGameId()

    /**
     * Set the value of [player_id] column.
     *
     * @param  int $v new value
     * @return Turn The current object (for fluent API support)
     */
    public function setPlayerId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->player_id !== $v) {
            $this->player_id = $v;
            $this->modifiedColumns[] = TurnPeer::PLAYER_ID;
        }

        if ($this->aPlayer !== null && $this->aPlayer->getId() !== $v) {
            $this->aPlayer = null;
        }


        return $this;
    } // setPlayerId()

    /**
     * Set the value of [phase] column.
     *
     * @param  string $v new value
     * @return Turn The current object (for fluent API support)
     */
    public function setPhase($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->phase !== $v) {
            $this->phase = $v;
            $this->modifiedColumns[] = TurnPeer::PHASE;
        }


        return $this;
    } // setPhase()

    /**
     * Set the value of [cards] column.
     *
     * @param  string $v new value
     * @return Turn The current object (for fluent API support)
     */
    public function setCards($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cards !== $v) {
            $this->cards = $v;
            $this->modifiedColumns[] = TurnPeer::CARDS;
        }


        return $this;
    } // setCards()

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
            $this->phase = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->cards = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 5; // 5 = TurnPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Turn object", $e);
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

        if ($this->aGameRelatedByGameId !== null && $this->game_id !== $this->aGameRelatedByGameId->getId()) {
            $this->aGameRelatedByGameId = null;
        }
        if ($this->aPlayer !== null && $this->player_id !== $this->aPlayer->getId()) {
            $this->aPlayer = null;
        }
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
            $con = Propel::getConnection(TurnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = TurnPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aGameRelatedByGameId = null;
            $this->aPlayer = null;
            $this->collGamesRelatedByLastTurnId = null;

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
            $con = Propel::getConnection(TurnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = TurnQuery::create()
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
            $con = Propel::getConnection(TurnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                TurnPeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aGameRelatedByGameId !== null) {
                if ($this->aGameRelatedByGameId->isModified() || $this->aGameRelatedByGameId->isNew()) {
                    $affectedRows += $this->aGameRelatedByGameId->save($con);
                }
                $this->setGameRelatedByGameId($this->aGameRelatedByGameId);
            }

            if ($this->aPlayer !== null) {
                if ($this->aPlayer->isModified() || $this->aPlayer->isNew()) {
                    $affectedRows += $this->aPlayer->save($con);
                }
                $this->setPlayer($this->aPlayer);
            }

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

            if ($this->gamesRelatedByLastTurnIdScheduledForDeletion !== null) {
                if (!$this->gamesRelatedByLastTurnIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->gamesRelatedByLastTurnIdScheduledForDeletion as $gameRelatedByLastTurnId) {
                        // need to save related object because we set the relation to null
                        $gameRelatedByLastTurnId->save($con);
                    }
                    $this->gamesRelatedByLastTurnIdScheduledForDeletion = null;
                }
            }

            if ($this->collGamesRelatedByLastTurnId !== null) {
                foreach ($this->collGamesRelatedByLastTurnId as $referrerFK) {
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

        $this->modifiedColumns[] = TurnPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . TurnPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(TurnPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(TurnPeer::GAME_ID)) {
            $modifiedColumns[':p' . $index++]  = '`game_id`';
        }
        if ($this->isColumnModified(TurnPeer::PLAYER_ID)) {
            $modifiedColumns[':p' . $index++]  = '`player_id`';
        }
        if ($this->isColumnModified(TurnPeer::PHASE)) {
            $modifiedColumns[':p' . $index++]  = '`phase`';
        }
        if ($this->isColumnModified(TurnPeer::CARDS)) {
            $modifiedColumns[':p' . $index++]  = '`cards`';
        }

        $sql = sprintf(
            'INSERT INTO `turns` (%s) VALUES (%s)',
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
                    case '`phase`':
                        $stmt->bindValue($identifier, $this->phase, PDO::PARAM_STR);
                        break;
                    case '`cards`':
                        $stmt->bindValue($identifier, $this->cards, PDO::PARAM_STR);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aGameRelatedByGameId !== null) {
                if (!$this->aGameRelatedByGameId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aGameRelatedByGameId->getValidationFailures());
                }
            }

            if ($this->aPlayer !== null) {
                if (!$this->aPlayer->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayer->getValidationFailures());
                }
            }


            if (($retval = TurnPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collGamesRelatedByLastTurnId !== null) {
                    foreach ($this->collGamesRelatedByLastTurnId as $referrerFK) {
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
        $pos = TurnPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getPhase();
                break;
            case 4:
                return $this->getCards();
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
        if (isset($alreadyDumpedObjects['Turn'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Turn'][$this->getPrimaryKey()] = true;
        $keys = TurnPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getGameId(),
            $keys[2] => $this->getPlayerId(),
            $keys[3] => $this->getPhase(),
            $keys[4] => $this->getCards(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aGameRelatedByGameId) {
                $result['GameRelatedByGameId'] = $this->aGameRelatedByGameId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPlayer) {
                $result['Player'] = $this->aPlayer->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collGamesRelatedByLastTurnId) {
                $result['GamesRelatedByLastTurnId'] = $this->collGamesRelatedByLastTurnId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = TurnPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setPhase($value);
                break;
            case 4:
                $this->setCards($value);
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
        $keys = TurnPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setGameId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setPlayerId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPhase($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setCards($arr[$keys[4]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(TurnPeer::DATABASE_NAME);

        if ($this->isColumnModified(TurnPeer::ID)) $criteria->add(TurnPeer::ID, $this->id);
        if ($this->isColumnModified(TurnPeer::GAME_ID)) $criteria->add(TurnPeer::GAME_ID, $this->game_id);
        if ($this->isColumnModified(TurnPeer::PLAYER_ID)) $criteria->add(TurnPeer::PLAYER_ID, $this->player_id);
        if ($this->isColumnModified(TurnPeer::PHASE)) $criteria->add(TurnPeer::PHASE, $this->phase);
        if ($this->isColumnModified(TurnPeer::CARDS)) $criteria->add(TurnPeer::CARDS, $this->cards);

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
        $criteria = new Criteria(TurnPeer::DATABASE_NAME);
        $criteria->add(TurnPeer::ID, $this->id);

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
     * @param object $copyObj An object of Turn (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setGameId($this->getGameId());
        $copyObj->setPlayerId($this->getPlayerId());
        $copyObj->setPhase($this->getPhase());
        $copyObj->setCards($this->getCards());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getGamesRelatedByLastTurnId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGameRelatedByLastTurnId($relObj->copy($deepCopy));
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
     * @return Turn Clone of current object.
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
     * @return TurnPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new TurnPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Game object.
     *
     * @param                  Game $v
     * @return Turn The current object (for fluent API support)
     * @throws PropelException
     */
    public function setGameRelatedByGameId(Game $v = null)
    {
        if ($v === null) {
            $this->setGameId(NULL);
        } else {
            $this->setGameId($v->getId());
        }

        $this->aGameRelatedByGameId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Game object, it will not be re-added.
        if ($v !== null) {
            $v->addTurnRelatedByGameId($this);
        }


        return $this;
    }


    /**
     * Get the associated Game object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Game The associated Game object.
     * @throws PropelException
     */
    public function getGameRelatedByGameId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aGameRelatedByGameId === null && ($this->game_id !== null) && $doQuery) {
            $this->aGameRelatedByGameId = GameQuery::create()->findPk($this->game_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aGameRelatedByGameId->addTurnsRelatedByGameId($this);
             */
        }

        return $this->aGameRelatedByGameId;
    }

    /**
     * Declares an association between this object and a Player object.
     *
     * @param                  Player $v
     * @return Turn The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayer(Player $v = null)
    {
        if ($v === null) {
            $this->setPlayerId(NULL);
        } else {
            $this->setPlayerId($v->getId());
        }

        $this->aPlayer = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Player object, it will not be re-added.
        if ($v !== null) {
            $v->addTurn($this);
        }


        return $this;
    }


    /**
     * Get the associated Player object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Player The associated Player object.
     * @throws PropelException
     */
    public function getPlayer(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayer === null && ($this->player_id !== null) && $doQuery) {
            $this->aPlayer = PlayerQuery::create()->findPk($this->player_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayer->addTurns($this);
             */
        }

        return $this->aPlayer;
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
        if ('GameRelatedByLastTurnId' == $relationName) {
            $this->initGamesRelatedByLastTurnId();
        }
    }

    /**
     * Clears out the collGamesRelatedByLastTurnId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Turn The current object (for fluent API support)
     * @see        addGamesRelatedByLastTurnId()
     */
    public function clearGamesRelatedByLastTurnId()
    {
        $this->collGamesRelatedByLastTurnId = null; // important to set this to null since that means it is uninitialized
        $this->collGamesRelatedByLastTurnIdPartial = null;

        return $this;
    }

    /**
     * reset is the collGamesRelatedByLastTurnId collection loaded partially
     *
     * @return void
     */
    public function resetPartialGamesRelatedByLastTurnId($v = true)
    {
        $this->collGamesRelatedByLastTurnIdPartial = $v;
    }

    /**
     * Initializes the collGamesRelatedByLastTurnId collection.
     *
     * By default this just sets the collGamesRelatedByLastTurnId collection to an empty array (like clearcollGamesRelatedByLastTurnId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGamesRelatedByLastTurnId($overrideExisting = true)
    {
        if (null !== $this->collGamesRelatedByLastTurnId && !$overrideExisting) {
            return;
        }
        $this->collGamesRelatedByLastTurnId = new PropelObjectCollection();
        $this->collGamesRelatedByLastTurnId->setModel('Game');
    }

    /**
     * Gets an array of Game objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Turn is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Game[] List of Game objects
     * @throws PropelException
     */
    public function getGamesRelatedByLastTurnId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByLastTurnIdPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByLastTurnId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByLastTurnId) {
                // return empty collection
                $this->initGamesRelatedByLastTurnId();
            } else {
                $collGamesRelatedByLastTurnId = GameQuery::create(null, $criteria)
                    ->filterByTurnRelatedByLastTurnId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGamesRelatedByLastTurnIdPartial && count($collGamesRelatedByLastTurnId)) {
                      $this->initGamesRelatedByLastTurnId(false);

                      foreach ($collGamesRelatedByLastTurnId as $obj) {
                        if (false == $this->collGamesRelatedByLastTurnId->contains($obj)) {
                          $this->collGamesRelatedByLastTurnId->append($obj);
                        }
                      }

                      $this->collGamesRelatedByLastTurnIdPartial = true;
                    }

                    $collGamesRelatedByLastTurnId->getInternalIterator()->rewind();

                    return $collGamesRelatedByLastTurnId;
                }

                if ($partial && $this->collGamesRelatedByLastTurnId) {
                    foreach ($this->collGamesRelatedByLastTurnId as $obj) {
                        if ($obj->isNew()) {
                            $collGamesRelatedByLastTurnId[] = $obj;
                        }
                    }
                }

                $this->collGamesRelatedByLastTurnId = $collGamesRelatedByLastTurnId;
                $this->collGamesRelatedByLastTurnIdPartial = false;
            }
        }

        return $this->collGamesRelatedByLastTurnId;
    }

    /**
     * Sets a collection of GameRelatedByLastTurnId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $gamesRelatedByLastTurnId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Turn The current object (for fluent API support)
     */
    public function setGamesRelatedByLastTurnId(PropelCollection $gamesRelatedByLastTurnId, PropelPDO $con = null)
    {
        $gamesRelatedByLastTurnIdToDelete = $this->getGamesRelatedByLastTurnId(new Criteria(), $con)->diff($gamesRelatedByLastTurnId);


        $this->gamesRelatedByLastTurnIdScheduledForDeletion = $gamesRelatedByLastTurnIdToDelete;

        foreach ($gamesRelatedByLastTurnIdToDelete as $gameRelatedByLastTurnIdRemoved) {
            $gameRelatedByLastTurnIdRemoved->setTurnRelatedByLastTurnId(null);
        }

        $this->collGamesRelatedByLastTurnId = null;
        foreach ($gamesRelatedByLastTurnId as $gameRelatedByLastTurnId) {
            $this->addGameRelatedByLastTurnId($gameRelatedByLastTurnId);
        }

        $this->collGamesRelatedByLastTurnId = $gamesRelatedByLastTurnId;
        $this->collGamesRelatedByLastTurnIdPartial = false;

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
    public function countGamesRelatedByLastTurnId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByLastTurnIdPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByLastTurnId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByLastTurnId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getGamesRelatedByLastTurnId());
            }
            $query = GameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByTurnRelatedByLastTurnId($this)
                ->count($con);
        }

        return count($this->collGamesRelatedByLastTurnId);
    }

    /**
     * Method called to associate a Game object to this object
     * through the Game foreign key attribute.
     *
     * @param    Game $l Game
     * @return Turn The current object (for fluent API support)
     */
    public function addGameRelatedByLastTurnId(Game $l)
    {
        if ($this->collGamesRelatedByLastTurnId === null) {
            $this->initGamesRelatedByLastTurnId();
            $this->collGamesRelatedByLastTurnIdPartial = true;
        }

        if (!in_array($l, $this->collGamesRelatedByLastTurnId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGameRelatedByLastTurnId($l);

            if ($this->gamesRelatedByLastTurnIdScheduledForDeletion and $this->gamesRelatedByLastTurnIdScheduledForDeletion->contains($l)) {
                $this->gamesRelatedByLastTurnIdScheduledForDeletion->remove($this->gamesRelatedByLastTurnIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	GameRelatedByLastTurnId $gameRelatedByLastTurnId The gameRelatedByLastTurnId object to add.
     */
    protected function doAddGameRelatedByLastTurnId($gameRelatedByLastTurnId)
    {
        $this->collGamesRelatedByLastTurnId[]= $gameRelatedByLastTurnId;
        $gameRelatedByLastTurnId->setTurnRelatedByLastTurnId($this);
    }

    /**
     * @param	GameRelatedByLastTurnId $gameRelatedByLastTurnId The gameRelatedByLastTurnId object to remove.
     * @return Turn The current object (for fluent API support)
     */
    public function removeGameRelatedByLastTurnId($gameRelatedByLastTurnId)
    {
        if ($this->getGamesRelatedByLastTurnId()->contains($gameRelatedByLastTurnId)) {
            $this->collGamesRelatedByLastTurnId->remove($this->collGamesRelatedByLastTurnId->search($gameRelatedByLastTurnId));
            if (null === $this->gamesRelatedByLastTurnIdScheduledForDeletion) {
                $this->gamesRelatedByLastTurnIdScheduledForDeletion = clone $this->collGamesRelatedByLastTurnId;
                $this->gamesRelatedByLastTurnIdScheduledForDeletion->clear();
            }
            $this->gamesRelatedByLastTurnIdScheduledForDeletion[]= $gameRelatedByLastTurnId;
            $gameRelatedByLastTurnId->setTurnRelatedByLastTurnId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Turn is new, it will return
     * an empty collection; or if this Turn has previously
     * been saved, it will retrieve related GamesRelatedByLastTurnId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Turn.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByLastTurnIdJoinCardRelatedByDiscard($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByDiscard', $join_behavior);

        return $this->getGamesRelatedByLastTurnId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Turn is new, it will return
     * an empty collection; or if this Turn has previously
     * been saved, it will retrieve related GamesRelatedByLastTurnId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Turn.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByLastTurnIdJoinCardRelatedByDraw($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByDraw', $join_behavior);

        return $this->getGamesRelatedByLastTurnId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Turn is new, it will return
     * an empty collection; or if this Turn has previously
     * been saved, it will retrieve related GamesRelatedByLastTurnId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Turn.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByLastTurnIdJoinPlayerRelatedByPlayerOneId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerOneId', $join_behavior);

        return $this->getGamesRelatedByLastTurnId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Turn is new, it will return
     * an empty collection; or if this Turn has previously
     * been saved, it will retrieve related GamesRelatedByLastTurnId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Turn.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByLastTurnIdJoinPlayerRelatedByPlayerTwoId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('PlayerRelatedByPlayerTwoId', $join_behavior);

        return $this->getGamesRelatedByLastTurnId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Turn is new, it will return
     * an empty collection; or if this Turn has previously
     * been saved, it will retrieve related GamesRelatedByLastTurnId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Turn.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByLastTurnIdJoinCardRelatedByShop($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByShop', $join_behavior);

        return $this->getGamesRelatedByLastTurnId($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->game_id = null;
        $this->player_id = null;
        $this->phase = null;
        $this->cards = null;
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
            if ($this->collGamesRelatedByLastTurnId) {
                foreach ($this->collGamesRelatedByLastTurnId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aGameRelatedByGameId instanceof Persistent) {
              $this->aGameRelatedByGameId->clearAllReferences($deep);
            }
            if ($this->aPlayer instanceof Persistent) {
              $this->aPlayer->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collGamesRelatedByLastTurnId instanceof PropelCollection) {
            $this->collGamesRelatedByLastTurnId->clearIterator();
        }
        $this->collGamesRelatedByLastTurnId = null;
        $this->aGameRelatedByGameId = null;
        $this->aPlayer = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(TurnPeer::DEFAULT_STRING_FORMAT);
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
