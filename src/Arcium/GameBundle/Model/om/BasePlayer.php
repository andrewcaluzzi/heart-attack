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
use Arcium\GameBundle\Model\PlayerPeer;
use Arcium\GameBundle\Model\PlayerQuery;
use Arcium\GameBundle\Model\Turn;
use Arcium\GameBundle\Model\TurnQuery;

abstract class BasePlayer extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Arcium\\GameBundle\\Model\\PlayerPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PlayerPeer
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
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the username field.
     * @var        string
     */
    protected $username;

    /**
     * @var        PropelObjectCollection|Game[] Collection to store aggregation of Game objects.
     */
    protected $collGamesRelatedByPlayerOneId;
    protected $collGamesRelatedByPlayerOneIdPartial;

    /**
     * @var        PropelObjectCollection|Game[] Collection to store aggregation of Game objects.
     */
    protected $collGamesRelatedByPlayerTwoId;
    protected $collGamesRelatedByPlayerTwoIdPartial;

    /**
     * @var        PropelObjectCollection|Turn[] Collection to store aggregation of Turn objects.
     */
    protected $collTurns;
    protected $collTurnsPartial;

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
    protected $gamesRelatedByPlayerOneIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $gamesRelatedByPlayerTwoIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $turnsScheduledForDeletion = null;

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
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [username] column value.
     *
     * @return string
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Player The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PlayerPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return Player The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = PlayerPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [username] column.
     *
     * @param  string $v new value
     * @return Player The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[] = PlayerPeer::USERNAME;
        }


        return $this;
    } // setUsername()

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
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->username = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 3; // 3 = PlayerPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Player object", $e);
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
            $con = Propel::getConnection(PlayerPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PlayerPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collGamesRelatedByPlayerOneId = null;

            $this->collGamesRelatedByPlayerTwoId = null;

            $this->collTurns = null;

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
            $con = Propel::getConnection(PlayerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PlayerQuery::create()
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
            $con = Propel::getConnection(PlayerPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PlayerPeer::addInstanceToPool($this);
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

            if ($this->gamesRelatedByPlayerOneIdScheduledForDeletion !== null) {
                if (!$this->gamesRelatedByPlayerOneIdScheduledForDeletion->isEmpty()) {
                    GameQuery::create()
                        ->filterByPrimaryKeys($this->gamesRelatedByPlayerOneIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->gamesRelatedByPlayerOneIdScheduledForDeletion = null;
                }
            }

            if ($this->collGamesRelatedByPlayerOneId !== null) {
                foreach ($this->collGamesRelatedByPlayerOneId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->gamesRelatedByPlayerTwoIdScheduledForDeletion !== null) {
                if (!$this->gamesRelatedByPlayerTwoIdScheduledForDeletion->isEmpty()) {
                    GameQuery::create()
                        ->filterByPrimaryKeys($this->gamesRelatedByPlayerTwoIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->gamesRelatedByPlayerTwoIdScheduledForDeletion = null;
                }
            }

            if ($this->collGamesRelatedByPlayerTwoId !== null) {
                foreach ($this->collGamesRelatedByPlayerTwoId as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->turnsScheduledForDeletion !== null) {
                if (!$this->turnsScheduledForDeletion->isEmpty()) {
                    TurnQuery::create()
                        ->filterByPrimaryKeys($this->turnsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->turnsScheduledForDeletion = null;
                }
            }

            if ($this->collTurns !== null) {
                foreach ($this->collTurns as $referrerFK) {
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

        $this->modifiedColumns[] = PlayerPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PlayerPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PlayerPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(PlayerPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`name`';
        }
        if ($this->isColumnModified(PlayerPeer::USERNAME)) {
            $modifiedColumns[':p' . $index++]  = '`username`';
        }

        $sql = sprintf(
            'INSERT INTO `players` (%s) VALUES (%s)',
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
                    case '`name`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '`username`':
                        $stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
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


            if (($retval = PlayerPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collGamesRelatedByPlayerOneId !== null) {
                    foreach ($this->collGamesRelatedByPlayerOneId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collGamesRelatedByPlayerTwoId !== null) {
                    foreach ($this->collGamesRelatedByPlayerTwoId as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collTurns !== null) {
                    foreach ($this->collTurns as $referrerFK) {
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
        $pos = PlayerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getName();
                break;
            case 2:
                return $this->getUsername();
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
        if (isset($alreadyDumpedObjects['Player'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Player'][$this->getPrimaryKey()] = true;
        $keys = PlayerPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getUsername(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collGamesRelatedByPlayerOneId) {
                $result['GamesRelatedByPlayerOneId'] = $this->collGamesRelatedByPlayerOneId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collGamesRelatedByPlayerTwoId) {
                $result['GamesRelatedByPlayerTwoId'] = $this->collGamesRelatedByPlayerTwoId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collTurns) {
                $result['Turns'] = $this->collTurns->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = PlayerPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setName($value);
                break;
            case 2:
                $this->setUsername($value);
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
        $keys = PlayerPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setUsername($arr[$keys[2]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PlayerPeer::DATABASE_NAME);

        if ($this->isColumnModified(PlayerPeer::ID)) $criteria->add(PlayerPeer::ID, $this->id);
        if ($this->isColumnModified(PlayerPeer::NAME)) $criteria->add(PlayerPeer::NAME, $this->name);
        if ($this->isColumnModified(PlayerPeer::USERNAME)) $criteria->add(PlayerPeer::USERNAME, $this->username);

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
        $criteria = new Criteria(PlayerPeer::DATABASE_NAME);
        $criteria->add(PlayerPeer::ID, $this->id);

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
     * @param object $copyObj An object of Player (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setUsername($this->getUsername());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getGamesRelatedByPlayerOneId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGameRelatedByPlayerOneId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getGamesRelatedByPlayerTwoId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGameRelatedByPlayerTwoId($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getTurns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTurn($relObj->copy($deepCopy));
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
     * @return Player Clone of current object.
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
     * @return PlayerPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PlayerPeer();
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
        if ('GameRelatedByPlayerOneId' == $relationName) {
            $this->initGamesRelatedByPlayerOneId();
        }
        if ('GameRelatedByPlayerTwoId' == $relationName) {
            $this->initGamesRelatedByPlayerTwoId();
        }
        if ('Turn' == $relationName) {
            $this->initTurns();
        }
    }

    /**
     * Clears out the collGamesRelatedByPlayerOneId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Player The current object (for fluent API support)
     * @see        addGamesRelatedByPlayerOneId()
     */
    public function clearGamesRelatedByPlayerOneId()
    {
        $this->collGamesRelatedByPlayerOneId = null; // important to set this to null since that means it is uninitialized
        $this->collGamesRelatedByPlayerOneIdPartial = null;

        return $this;
    }

    /**
     * reset is the collGamesRelatedByPlayerOneId collection loaded partially
     *
     * @return void
     */
    public function resetPartialGamesRelatedByPlayerOneId($v = true)
    {
        $this->collGamesRelatedByPlayerOneIdPartial = $v;
    }

    /**
     * Initializes the collGamesRelatedByPlayerOneId collection.
     *
     * By default this just sets the collGamesRelatedByPlayerOneId collection to an empty array (like clearcollGamesRelatedByPlayerOneId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGamesRelatedByPlayerOneId($overrideExisting = true)
    {
        if (null !== $this->collGamesRelatedByPlayerOneId && !$overrideExisting) {
            return;
        }
        $this->collGamesRelatedByPlayerOneId = new PropelObjectCollection();
        $this->collGamesRelatedByPlayerOneId->setModel('Game');
    }

    /**
     * Gets an array of Game objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Player is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Game[] List of Game objects
     * @throws PropelException
     */
    public function getGamesRelatedByPlayerOneId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByPlayerOneIdPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByPlayerOneId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByPlayerOneId) {
                // return empty collection
                $this->initGamesRelatedByPlayerOneId();
            } else {
                $collGamesRelatedByPlayerOneId = GameQuery::create(null, $criteria)
                    ->filterByPlayerRelatedByPlayerOneId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGamesRelatedByPlayerOneIdPartial && count($collGamesRelatedByPlayerOneId)) {
                      $this->initGamesRelatedByPlayerOneId(false);

                      foreach ($collGamesRelatedByPlayerOneId as $obj) {
                        if (false == $this->collGamesRelatedByPlayerOneId->contains($obj)) {
                          $this->collGamesRelatedByPlayerOneId->append($obj);
                        }
                      }

                      $this->collGamesRelatedByPlayerOneIdPartial = true;
                    }

                    $collGamesRelatedByPlayerOneId->getInternalIterator()->rewind();

                    return $collGamesRelatedByPlayerOneId;
                }

                if ($partial && $this->collGamesRelatedByPlayerOneId) {
                    foreach ($this->collGamesRelatedByPlayerOneId as $obj) {
                        if ($obj->isNew()) {
                            $collGamesRelatedByPlayerOneId[] = $obj;
                        }
                    }
                }

                $this->collGamesRelatedByPlayerOneId = $collGamesRelatedByPlayerOneId;
                $this->collGamesRelatedByPlayerOneIdPartial = false;
            }
        }

        return $this->collGamesRelatedByPlayerOneId;
    }

    /**
     * Sets a collection of GameRelatedByPlayerOneId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $gamesRelatedByPlayerOneId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Player The current object (for fluent API support)
     */
    public function setGamesRelatedByPlayerOneId(PropelCollection $gamesRelatedByPlayerOneId, PropelPDO $con = null)
    {
        $gamesRelatedByPlayerOneIdToDelete = $this->getGamesRelatedByPlayerOneId(new Criteria(), $con)->diff($gamesRelatedByPlayerOneId);


        $this->gamesRelatedByPlayerOneIdScheduledForDeletion = $gamesRelatedByPlayerOneIdToDelete;

        foreach ($gamesRelatedByPlayerOneIdToDelete as $gameRelatedByPlayerOneIdRemoved) {
            $gameRelatedByPlayerOneIdRemoved->setPlayerRelatedByPlayerOneId(null);
        }

        $this->collGamesRelatedByPlayerOneId = null;
        foreach ($gamesRelatedByPlayerOneId as $gameRelatedByPlayerOneId) {
            $this->addGameRelatedByPlayerOneId($gameRelatedByPlayerOneId);
        }

        $this->collGamesRelatedByPlayerOneId = $gamesRelatedByPlayerOneId;
        $this->collGamesRelatedByPlayerOneIdPartial = false;

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
    public function countGamesRelatedByPlayerOneId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByPlayerOneIdPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByPlayerOneId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByPlayerOneId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getGamesRelatedByPlayerOneId());
            }
            $query = GameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayerRelatedByPlayerOneId($this)
                ->count($con);
        }

        return count($this->collGamesRelatedByPlayerOneId);
    }

    /**
     * Method called to associate a Game object to this object
     * through the Game foreign key attribute.
     *
     * @param    Game $l Game
     * @return Player The current object (for fluent API support)
     */
    public function addGameRelatedByPlayerOneId(Game $l)
    {
        if ($this->collGamesRelatedByPlayerOneId === null) {
            $this->initGamesRelatedByPlayerOneId();
            $this->collGamesRelatedByPlayerOneIdPartial = true;
        }

        if (!in_array($l, $this->collGamesRelatedByPlayerOneId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGameRelatedByPlayerOneId($l);

            if ($this->gamesRelatedByPlayerOneIdScheduledForDeletion and $this->gamesRelatedByPlayerOneIdScheduledForDeletion->contains($l)) {
                $this->gamesRelatedByPlayerOneIdScheduledForDeletion->remove($this->gamesRelatedByPlayerOneIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	GameRelatedByPlayerOneId $gameRelatedByPlayerOneId The gameRelatedByPlayerOneId object to add.
     */
    protected function doAddGameRelatedByPlayerOneId($gameRelatedByPlayerOneId)
    {
        $this->collGamesRelatedByPlayerOneId[]= $gameRelatedByPlayerOneId;
        $gameRelatedByPlayerOneId->setPlayerRelatedByPlayerOneId($this);
    }

    /**
     * @param	GameRelatedByPlayerOneId $gameRelatedByPlayerOneId The gameRelatedByPlayerOneId object to remove.
     * @return Player The current object (for fluent API support)
     */
    public function removeGameRelatedByPlayerOneId($gameRelatedByPlayerOneId)
    {
        if ($this->getGamesRelatedByPlayerOneId()->contains($gameRelatedByPlayerOneId)) {
            $this->collGamesRelatedByPlayerOneId->remove($this->collGamesRelatedByPlayerOneId->search($gameRelatedByPlayerOneId));
            if (null === $this->gamesRelatedByPlayerOneIdScheduledForDeletion) {
                $this->gamesRelatedByPlayerOneIdScheduledForDeletion = clone $this->collGamesRelatedByPlayerOneId;
                $this->gamesRelatedByPlayerOneIdScheduledForDeletion->clear();
            }
            $this->gamesRelatedByPlayerOneIdScheduledForDeletion[]= clone $gameRelatedByPlayerOneId;
            $gameRelatedByPlayerOneId->setPlayerRelatedByPlayerOneId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerOneId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerOneIdJoinCardRelatedByDiscard($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByDiscard', $join_behavior);

        return $this->getGamesRelatedByPlayerOneId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerOneId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerOneIdJoinCardRelatedByDraw($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByDraw', $join_behavior);

        return $this->getGamesRelatedByPlayerOneId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerOneId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerOneIdJoinTurnRelatedByLastTurnId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('TurnRelatedByLastTurnId', $join_behavior);

        return $this->getGamesRelatedByPlayerOneId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerOneId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerOneIdJoinCardRelatedByShop($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByShop', $join_behavior);

        return $this->getGamesRelatedByPlayerOneId($query, $con);
    }

    /**
     * Clears out the collGamesRelatedByPlayerTwoId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Player The current object (for fluent API support)
     * @see        addGamesRelatedByPlayerTwoId()
     */
    public function clearGamesRelatedByPlayerTwoId()
    {
        $this->collGamesRelatedByPlayerTwoId = null; // important to set this to null since that means it is uninitialized
        $this->collGamesRelatedByPlayerTwoIdPartial = null;

        return $this;
    }

    /**
     * reset is the collGamesRelatedByPlayerTwoId collection loaded partially
     *
     * @return void
     */
    public function resetPartialGamesRelatedByPlayerTwoId($v = true)
    {
        $this->collGamesRelatedByPlayerTwoIdPartial = $v;
    }

    /**
     * Initializes the collGamesRelatedByPlayerTwoId collection.
     *
     * By default this just sets the collGamesRelatedByPlayerTwoId collection to an empty array (like clearcollGamesRelatedByPlayerTwoId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGamesRelatedByPlayerTwoId($overrideExisting = true)
    {
        if (null !== $this->collGamesRelatedByPlayerTwoId && !$overrideExisting) {
            return;
        }
        $this->collGamesRelatedByPlayerTwoId = new PropelObjectCollection();
        $this->collGamesRelatedByPlayerTwoId->setModel('Game');
    }

    /**
     * Gets an array of Game objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Player is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Game[] List of Game objects
     * @throws PropelException
     */
    public function getGamesRelatedByPlayerTwoId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByPlayerTwoIdPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByPlayerTwoId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByPlayerTwoId) {
                // return empty collection
                $this->initGamesRelatedByPlayerTwoId();
            } else {
                $collGamesRelatedByPlayerTwoId = GameQuery::create(null, $criteria)
                    ->filterByPlayerRelatedByPlayerTwoId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collGamesRelatedByPlayerTwoIdPartial && count($collGamesRelatedByPlayerTwoId)) {
                      $this->initGamesRelatedByPlayerTwoId(false);

                      foreach ($collGamesRelatedByPlayerTwoId as $obj) {
                        if (false == $this->collGamesRelatedByPlayerTwoId->contains($obj)) {
                          $this->collGamesRelatedByPlayerTwoId->append($obj);
                        }
                      }

                      $this->collGamesRelatedByPlayerTwoIdPartial = true;
                    }

                    $collGamesRelatedByPlayerTwoId->getInternalIterator()->rewind();

                    return $collGamesRelatedByPlayerTwoId;
                }

                if ($partial && $this->collGamesRelatedByPlayerTwoId) {
                    foreach ($this->collGamesRelatedByPlayerTwoId as $obj) {
                        if ($obj->isNew()) {
                            $collGamesRelatedByPlayerTwoId[] = $obj;
                        }
                    }
                }

                $this->collGamesRelatedByPlayerTwoId = $collGamesRelatedByPlayerTwoId;
                $this->collGamesRelatedByPlayerTwoIdPartial = false;
            }
        }

        return $this->collGamesRelatedByPlayerTwoId;
    }

    /**
     * Sets a collection of GameRelatedByPlayerTwoId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $gamesRelatedByPlayerTwoId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Player The current object (for fluent API support)
     */
    public function setGamesRelatedByPlayerTwoId(PropelCollection $gamesRelatedByPlayerTwoId, PropelPDO $con = null)
    {
        $gamesRelatedByPlayerTwoIdToDelete = $this->getGamesRelatedByPlayerTwoId(new Criteria(), $con)->diff($gamesRelatedByPlayerTwoId);


        $this->gamesRelatedByPlayerTwoIdScheduledForDeletion = $gamesRelatedByPlayerTwoIdToDelete;

        foreach ($gamesRelatedByPlayerTwoIdToDelete as $gameRelatedByPlayerTwoIdRemoved) {
            $gameRelatedByPlayerTwoIdRemoved->setPlayerRelatedByPlayerTwoId(null);
        }

        $this->collGamesRelatedByPlayerTwoId = null;
        foreach ($gamesRelatedByPlayerTwoId as $gameRelatedByPlayerTwoId) {
            $this->addGameRelatedByPlayerTwoId($gameRelatedByPlayerTwoId);
        }

        $this->collGamesRelatedByPlayerTwoId = $gamesRelatedByPlayerTwoId;
        $this->collGamesRelatedByPlayerTwoIdPartial = false;

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
    public function countGamesRelatedByPlayerTwoId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collGamesRelatedByPlayerTwoIdPartial && !$this->isNew();
        if (null === $this->collGamesRelatedByPlayerTwoId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGamesRelatedByPlayerTwoId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getGamesRelatedByPlayerTwoId());
            }
            $query = GameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayerRelatedByPlayerTwoId($this)
                ->count($con);
        }

        return count($this->collGamesRelatedByPlayerTwoId);
    }

    /**
     * Method called to associate a Game object to this object
     * through the Game foreign key attribute.
     *
     * @param    Game $l Game
     * @return Player The current object (for fluent API support)
     */
    public function addGameRelatedByPlayerTwoId(Game $l)
    {
        if ($this->collGamesRelatedByPlayerTwoId === null) {
            $this->initGamesRelatedByPlayerTwoId();
            $this->collGamesRelatedByPlayerTwoIdPartial = true;
        }

        if (!in_array($l, $this->collGamesRelatedByPlayerTwoId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddGameRelatedByPlayerTwoId($l);

            if ($this->gamesRelatedByPlayerTwoIdScheduledForDeletion and $this->gamesRelatedByPlayerTwoIdScheduledForDeletion->contains($l)) {
                $this->gamesRelatedByPlayerTwoIdScheduledForDeletion->remove($this->gamesRelatedByPlayerTwoIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	GameRelatedByPlayerTwoId $gameRelatedByPlayerTwoId The gameRelatedByPlayerTwoId object to add.
     */
    protected function doAddGameRelatedByPlayerTwoId($gameRelatedByPlayerTwoId)
    {
        $this->collGamesRelatedByPlayerTwoId[]= $gameRelatedByPlayerTwoId;
        $gameRelatedByPlayerTwoId->setPlayerRelatedByPlayerTwoId($this);
    }

    /**
     * @param	GameRelatedByPlayerTwoId $gameRelatedByPlayerTwoId The gameRelatedByPlayerTwoId object to remove.
     * @return Player The current object (for fluent API support)
     */
    public function removeGameRelatedByPlayerTwoId($gameRelatedByPlayerTwoId)
    {
        if ($this->getGamesRelatedByPlayerTwoId()->contains($gameRelatedByPlayerTwoId)) {
            $this->collGamesRelatedByPlayerTwoId->remove($this->collGamesRelatedByPlayerTwoId->search($gameRelatedByPlayerTwoId));
            if (null === $this->gamesRelatedByPlayerTwoIdScheduledForDeletion) {
                $this->gamesRelatedByPlayerTwoIdScheduledForDeletion = clone $this->collGamesRelatedByPlayerTwoId;
                $this->gamesRelatedByPlayerTwoIdScheduledForDeletion->clear();
            }
            $this->gamesRelatedByPlayerTwoIdScheduledForDeletion[]= clone $gameRelatedByPlayerTwoId;
            $gameRelatedByPlayerTwoId->setPlayerRelatedByPlayerTwoId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerTwoId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerTwoIdJoinCardRelatedByDiscard($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByDiscard', $join_behavior);

        return $this->getGamesRelatedByPlayerTwoId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerTwoId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerTwoIdJoinCardRelatedByDraw($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByDraw', $join_behavior);

        return $this->getGamesRelatedByPlayerTwoId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerTwoId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerTwoIdJoinTurnRelatedByLastTurnId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('TurnRelatedByLastTurnId', $join_behavior);

        return $this->getGamesRelatedByPlayerTwoId($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related GamesRelatedByPlayerTwoId from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Game[] List of Game objects
     */
    public function getGamesRelatedByPlayerTwoIdJoinCardRelatedByShop($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = GameQuery::create(null, $criteria);
        $query->joinWith('CardRelatedByShop', $join_behavior);

        return $this->getGamesRelatedByPlayerTwoId($query, $con);
    }

    /**
     * Clears out the collTurns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Player The current object (for fluent API support)
     * @see        addTurns()
     */
    public function clearTurns()
    {
        $this->collTurns = null; // important to set this to null since that means it is uninitialized
        $this->collTurnsPartial = null;

        return $this;
    }

    /**
     * reset is the collTurns collection loaded partially
     *
     * @return void
     */
    public function resetPartialTurns($v = true)
    {
        $this->collTurnsPartial = $v;
    }

    /**
     * Initializes the collTurns collection.
     *
     * By default this just sets the collTurns collection to an empty array (like clearcollTurns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTurns($overrideExisting = true)
    {
        if (null !== $this->collTurns && !$overrideExisting) {
            return;
        }
        $this->collTurns = new PropelObjectCollection();
        $this->collTurns->setModel('Turn');
    }

    /**
     * Gets an array of Turn objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Player is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Turn[] List of Turn objects
     * @throws PropelException
     */
    public function getTurns($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTurnsPartial && !$this->isNew();
        if (null === $this->collTurns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTurns) {
                // return empty collection
                $this->initTurns();
            } else {
                $collTurns = TurnQuery::create(null, $criteria)
                    ->filterByPlayer($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTurnsPartial && count($collTurns)) {
                      $this->initTurns(false);

                      foreach ($collTurns as $obj) {
                        if (false == $this->collTurns->contains($obj)) {
                          $this->collTurns->append($obj);
                        }
                      }

                      $this->collTurnsPartial = true;
                    }

                    $collTurns->getInternalIterator()->rewind();

                    return $collTurns;
                }

                if ($partial && $this->collTurns) {
                    foreach ($this->collTurns as $obj) {
                        if ($obj->isNew()) {
                            $collTurns[] = $obj;
                        }
                    }
                }

                $this->collTurns = $collTurns;
                $this->collTurnsPartial = false;
            }
        }

        return $this->collTurns;
    }

    /**
     * Sets a collection of Turn objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $turns A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Player The current object (for fluent API support)
     */
    public function setTurns(PropelCollection $turns, PropelPDO $con = null)
    {
        $turnsToDelete = $this->getTurns(new Criteria(), $con)->diff($turns);


        $this->turnsScheduledForDeletion = $turnsToDelete;

        foreach ($turnsToDelete as $turnRemoved) {
            $turnRemoved->setPlayer(null);
        }

        $this->collTurns = null;
        foreach ($turns as $turn) {
            $this->addTurn($turn);
        }

        $this->collTurns = $turns;
        $this->collTurnsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Turn objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Turn objects.
     * @throws PropelException
     */
    public function countTurns(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTurnsPartial && !$this->isNew();
        if (null === $this->collTurns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTurns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTurns());
            }
            $query = TurnQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPlayer($this)
                ->count($con);
        }

        return count($this->collTurns);
    }

    /**
     * Method called to associate a Turn object to this object
     * through the Turn foreign key attribute.
     *
     * @param    Turn $l Turn
     * @return Player The current object (for fluent API support)
     */
    public function addTurn(Turn $l)
    {
        if ($this->collTurns === null) {
            $this->initTurns();
            $this->collTurnsPartial = true;
        }

        if (!in_array($l, $this->collTurns->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTurn($l);

            if ($this->turnsScheduledForDeletion and $this->turnsScheduledForDeletion->contains($l)) {
                $this->turnsScheduledForDeletion->remove($this->turnsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	Turn $turn The turn object to add.
     */
    protected function doAddTurn($turn)
    {
        $this->collTurns[]= $turn;
        $turn->setPlayer($this);
    }

    /**
     * @param	Turn $turn The turn object to remove.
     * @return Player The current object (for fluent API support)
     */
    public function removeTurn($turn)
    {
        if ($this->getTurns()->contains($turn)) {
            $this->collTurns->remove($this->collTurns->search($turn));
            if (null === $this->turnsScheduledForDeletion) {
                $this->turnsScheduledForDeletion = clone $this->collTurns;
                $this->turnsScheduledForDeletion->clear();
            }
            $this->turnsScheduledForDeletion[]= clone $turn;
            $turn->setPlayer(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Player is new, it will return
     * an empty collection; or if this Player has previously
     * been saved, it will retrieve related Turns from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Player.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Turn[] List of Turn objects
     */
    public function getTurnsJoinGameRelatedByGameId($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TurnQuery::create(null, $criteria);
        $query->joinWith('GameRelatedByGameId', $join_behavior);

        return $this->getTurns($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->username = null;
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
            if ($this->collGamesRelatedByPlayerOneId) {
                foreach ($this->collGamesRelatedByPlayerOneId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGamesRelatedByPlayerTwoId) {
                foreach ($this->collGamesRelatedByPlayerTwoId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTurns) {
                foreach ($this->collTurns as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collGamesRelatedByPlayerOneId instanceof PropelCollection) {
            $this->collGamesRelatedByPlayerOneId->clearIterator();
        }
        $this->collGamesRelatedByPlayerOneId = null;
        if ($this->collGamesRelatedByPlayerTwoId instanceof PropelCollection) {
            $this->collGamesRelatedByPlayerTwoId->clearIterator();
        }
        $this->collGamesRelatedByPlayerTwoId = null;
        if ($this->collTurns instanceof PropelCollection) {
            $this->collTurns->clearIterator();
        }
        $this->collTurns = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PlayerPeer::DEFAULT_STRING_FORMAT);
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
