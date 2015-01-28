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
use Arcium\GameBundle\Model\GamePeer;
use Arcium\GameBundle\Model\GameQuery;
use Arcium\GameBundle\Model\Player;
use Arcium\GameBundle\Model\PlayerQuery;
use Arcium\GameBundle\Model\Turn;
use Arcium\GameBundle\Model\TurnQuery;

abstract class BaseGame extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Arcium\\GameBundle\\Model\\GamePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        GamePeer
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
     * The value for the deck field.
     * @var        string
     */
    protected $deck;

    /**
     * The value for the discard field.
     * @var        string
     */
    protected $discard;

    /**
     * The value for the shop field.
     * @var        string
     */
    protected $shop;

    /**
     * The value for the playerone field.
     * @var        int
     */
    protected $playerone;

    /**
     * The value for the playeronehand field.
     * @var        string
     */
    protected $playeronehand;

    /**
     * The value for the playertwo field.
     * @var        int
     */
    protected $playertwo;

    /**
     * The value for the playertwohand field.
     * @var        string
     */
    protected $playertwohand;

    /**
     * @var        Player
     */
    protected $aPlayerRelatedByPlayerone;

    /**
     * @var        Player
     */
    protected $aPlayerRelatedByPlayertwo;

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
     * Get the [deck] column value.
     *
     * @return string
     */
    public function getDeck()
    {

        return $this->deck;
    }

    /**
     * Get the [discard] column value.
     *
     * @return string
     */
    public function getDiscard()
    {

        return $this->discard;
    }

    /**
     * Get the [shop] column value.
     *
     * @return string
     */
    public function getShop()
    {

        return $this->shop;
    }

    /**
     * Get the [playerone] column value.
     *
     * @return int
     */
    public function getPlayerone()
    {

        return $this->playerone;
    }

    /**
     * Get the [playeronehand] column value.
     *
     * @return string
     */
    public function getPlayeronehand()
    {

        return $this->playeronehand;
    }

    /**
     * Get the [playertwo] column value.
     *
     * @return int
     */
    public function getPlayertwo()
    {

        return $this->playertwo;
    }

    /**
     * Get the [playertwohand] column value.
     *
     * @return string
     */
    public function getPlayertwohand()
    {

        return $this->playertwohand;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = GamePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [deck] column.
     *
     * @param  string $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setDeck($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->deck !== $v) {
            $this->deck = $v;
            $this->modifiedColumns[] = GamePeer::DECK;
        }


        return $this;
    } // setDeck()

    /**
     * Set the value of [discard] column.
     *
     * @param  string $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setDiscard($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->discard !== $v) {
            $this->discard = $v;
            $this->modifiedColumns[] = GamePeer::DISCARD;
        }


        return $this;
    } // setDiscard()

    /**
     * Set the value of [shop] column.
     *
     * @param  string $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setShop($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->shop !== $v) {
            $this->shop = $v;
            $this->modifiedColumns[] = GamePeer::SHOP;
        }


        return $this;
    } // setShop()

    /**
     * Set the value of [playerone] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setPlayerone($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->playerone !== $v) {
            $this->playerone = $v;
            $this->modifiedColumns[] = GamePeer::PLAYERONE;
        }

        if ($this->aPlayerRelatedByPlayerone !== null && $this->aPlayerRelatedByPlayerone->getId() !== $v) {
            $this->aPlayerRelatedByPlayerone = null;
        }


        return $this;
    } // setPlayerone()

    /**
     * Set the value of [playeronehand] column.
     *
     * @param  string $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setPlayeronehand($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->playeronehand !== $v) {
            $this->playeronehand = $v;
            $this->modifiedColumns[] = GamePeer::PLAYERONEHAND;
        }


        return $this;
    } // setPlayeronehand()

    /**
     * Set the value of [playertwo] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setPlayertwo($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->playertwo !== $v) {
            $this->playertwo = $v;
            $this->modifiedColumns[] = GamePeer::PLAYERTWO;
        }

        if ($this->aPlayerRelatedByPlayertwo !== null && $this->aPlayerRelatedByPlayertwo->getId() !== $v) {
            $this->aPlayerRelatedByPlayertwo = null;
        }


        return $this;
    } // setPlayertwo()

    /**
     * Set the value of [playertwohand] column.
     *
     * @param  string $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setPlayertwohand($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->playertwohand !== $v) {
            $this->playertwohand = $v;
            $this->modifiedColumns[] = GamePeer::PLAYERTWOHAND;
        }


        return $this;
    } // setPlayertwohand()

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
            $this->deck = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->discard = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->shop = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->playerone = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->playeronehand = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->playertwo = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->playertwohand = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 8; // 8 = GamePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Game object", $e);
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

        if ($this->aPlayerRelatedByPlayerone !== null && $this->playerone !== $this->aPlayerRelatedByPlayerone->getId()) {
            $this->aPlayerRelatedByPlayerone = null;
        }
        if ($this->aPlayerRelatedByPlayertwo !== null && $this->playertwo !== $this->aPlayerRelatedByPlayertwo->getId()) {
            $this->aPlayerRelatedByPlayertwo = null;
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
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = GamePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPlayerRelatedByPlayerone = null;
            $this->aPlayerRelatedByPlayertwo = null;
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
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = GameQuery::create()
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
            $con = Propel::getConnection(GamePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                GamePeer::addInstanceToPool($this);
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

            if ($this->aPlayerRelatedByPlayerone !== null) {
                if ($this->aPlayerRelatedByPlayerone->isModified() || $this->aPlayerRelatedByPlayerone->isNew()) {
                    $affectedRows += $this->aPlayerRelatedByPlayerone->save($con);
                }
                $this->setPlayerRelatedByPlayerone($this->aPlayerRelatedByPlayerone);
            }

            if ($this->aPlayerRelatedByPlayertwo !== null) {
                if ($this->aPlayerRelatedByPlayertwo->isModified() || $this->aPlayerRelatedByPlayertwo->isNew()) {
                    $affectedRows += $this->aPlayerRelatedByPlayertwo->save($con);
                }
                $this->setPlayerRelatedByPlayertwo($this->aPlayerRelatedByPlayertwo);
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

        $this->modifiedColumns[] = GamePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . GamePeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(GamePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`id`';
        }
        if ($this->isColumnModified(GamePeer::DECK)) {
            $modifiedColumns[':p' . $index++]  = '`deck`';
        }
        if ($this->isColumnModified(GamePeer::DISCARD)) {
            $modifiedColumns[':p' . $index++]  = '`discard`';
        }
        if ($this->isColumnModified(GamePeer::SHOP)) {
            $modifiedColumns[':p' . $index++]  = '`shop`';
        }
        if ($this->isColumnModified(GamePeer::PLAYERONE)) {
            $modifiedColumns[':p' . $index++]  = '`playerOne`';
        }
        if ($this->isColumnModified(GamePeer::PLAYERONEHAND)) {
            $modifiedColumns[':p' . $index++]  = '`playerOneHand`';
        }
        if ($this->isColumnModified(GamePeer::PLAYERTWO)) {
            $modifiedColumns[':p' . $index++]  = '`playerTwo`';
        }
        if ($this->isColumnModified(GamePeer::PLAYERTWOHAND)) {
            $modifiedColumns[':p' . $index++]  = '`playerTwoHand`';
        }

        $sql = sprintf(
            'INSERT INTO `games` (%s) VALUES (%s)',
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
                    case '`deck`':
                        $stmt->bindValue($identifier, $this->deck, PDO::PARAM_STR);
                        break;
                    case '`discard`':
                        $stmt->bindValue($identifier, $this->discard, PDO::PARAM_STR);
                        break;
                    case '`shop`':
                        $stmt->bindValue($identifier, $this->shop, PDO::PARAM_STR);
                        break;
                    case '`playerOne`':
                        $stmt->bindValue($identifier, $this->playerone, PDO::PARAM_INT);
                        break;
                    case '`playerOneHand`':
                        $stmt->bindValue($identifier, $this->playeronehand, PDO::PARAM_STR);
                        break;
                    case '`playerTwo`':
                        $stmt->bindValue($identifier, $this->playertwo, PDO::PARAM_INT);
                        break;
                    case '`playerTwoHand`':
                        $stmt->bindValue($identifier, $this->playertwohand, PDO::PARAM_STR);
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

            if ($this->aPlayerRelatedByPlayerone !== null) {
                if (!$this->aPlayerRelatedByPlayerone->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayerRelatedByPlayerone->getValidationFailures());
                }
            }

            if ($this->aPlayerRelatedByPlayertwo !== null) {
                if (!$this->aPlayerRelatedByPlayertwo->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayerRelatedByPlayertwo->getValidationFailures());
                }
            }


            if (($retval = GamePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
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
        $pos = GamePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDeck();
                break;
            case 2:
                return $this->getDiscard();
                break;
            case 3:
                return $this->getShop();
                break;
            case 4:
                return $this->getPlayerone();
                break;
            case 5:
                return $this->getPlayeronehand();
                break;
            case 6:
                return $this->getPlayertwo();
                break;
            case 7:
                return $this->getPlayertwohand();
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
        if (isset($alreadyDumpedObjects['Game'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Game'][$this->getPrimaryKey()] = true;
        $keys = GamePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDeck(),
            $keys[2] => $this->getDiscard(),
            $keys[3] => $this->getShop(),
            $keys[4] => $this->getPlayerone(),
            $keys[5] => $this->getPlayeronehand(),
            $keys[6] => $this->getPlayertwo(),
            $keys[7] => $this->getPlayertwohand(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aPlayerRelatedByPlayerone) {
                $result['PlayerRelatedByPlayerone'] = $this->aPlayerRelatedByPlayerone->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPlayerRelatedByPlayertwo) {
                $result['PlayerRelatedByPlayertwo'] = $this->aPlayerRelatedByPlayertwo->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = GamePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDeck($value);
                break;
            case 2:
                $this->setDiscard($value);
                break;
            case 3:
                $this->setShop($value);
                break;
            case 4:
                $this->setPlayerone($value);
                break;
            case 5:
                $this->setPlayeronehand($value);
                break;
            case 6:
                $this->setPlayertwo($value);
                break;
            case 7:
                $this->setPlayertwohand($value);
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
        $keys = GamePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDeck($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDiscard($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setShop($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPlayerone($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setPlayeronehand($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setPlayertwo($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setPlayertwohand($arr[$keys[7]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(GamePeer::DATABASE_NAME);

        if ($this->isColumnModified(GamePeer::ID)) $criteria->add(GamePeer::ID, $this->id);
        if ($this->isColumnModified(GamePeer::DECK)) $criteria->add(GamePeer::DECK, $this->deck);
        if ($this->isColumnModified(GamePeer::DISCARD)) $criteria->add(GamePeer::DISCARD, $this->discard);
        if ($this->isColumnModified(GamePeer::SHOP)) $criteria->add(GamePeer::SHOP, $this->shop);
        if ($this->isColumnModified(GamePeer::PLAYERONE)) $criteria->add(GamePeer::PLAYERONE, $this->playerone);
        if ($this->isColumnModified(GamePeer::PLAYERONEHAND)) $criteria->add(GamePeer::PLAYERONEHAND, $this->playeronehand);
        if ($this->isColumnModified(GamePeer::PLAYERTWO)) $criteria->add(GamePeer::PLAYERTWO, $this->playertwo);
        if ($this->isColumnModified(GamePeer::PLAYERTWOHAND)) $criteria->add(GamePeer::PLAYERTWOHAND, $this->playertwohand);

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
        $criteria = new Criteria(GamePeer::DATABASE_NAME);
        $criteria->add(GamePeer::ID, $this->id);

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
     * @param object $copyObj An object of Game (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDeck($this->getDeck());
        $copyObj->setDiscard($this->getDiscard());
        $copyObj->setShop($this->getShop());
        $copyObj->setPlayerone($this->getPlayerone());
        $copyObj->setPlayeronehand($this->getPlayeronehand());
        $copyObj->setPlayertwo($this->getPlayertwo());
        $copyObj->setPlayertwohand($this->getPlayertwohand());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

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
     * @return Game Clone of current object.
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
     * @return GamePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new GamePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Player object.
     *
     * @param                  Player $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayerRelatedByPlayerone(Player $v = null)
    {
        if ($v === null) {
            $this->setPlayerone(NULL);
        } else {
            $this->setPlayerone($v->getId());
        }

        $this->aPlayerRelatedByPlayerone = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Player object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByPlayerone($this);
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
    public function getPlayerRelatedByPlayerone(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayerRelatedByPlayerone === null && ($this->playerone !== null) && $doQuery) {
            $this->aPlayerRelatedByPlayerone = PlayerQuery::create()->findPk($this->playerone, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayerRelatedByPlayerone->addGamesRelatedByPlayerone($this);
             */
        }

        return $this->aPlayerRelatedByPlayerone;
    }

    /**
     * Declares an association between this object and a Player object.
     *
     * @param                  Player $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayerRelatedByPlayertwo(Player $v = null)
    {
        if ($v === null) {
            $this->setPlayertwo(NULL);
        } else {
            $this->setPlayertwo($v->getId());
        }

        $this->aPlayerRelatedByPlayertwo = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Player object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByPlayertwo($this);
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
    public function getPlayerRelatedByPlayertwo(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayerRelatedByPlayertwo === null && ($this->playertwo !== null) && $doQuery) {
            $this->aPlayerRelatedByPlayertwo = PlayerQuery::create()->findPk($this->playertwo, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayerRelatedByPlayertwo->addGamesRelatedByPlayertwo($this);
             */
        }

        return $this->aPlayerRelatedByPlayertwo;
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
        if ('Turn' == $relationName) {
            $this->initTurns();
        }
    }

    /**
     * Clears out the collTurns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Game The current object (for fluent API support)
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
     * If this Game is new, it will return
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
                    ->filterByGame($this)
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
     * @return Game The current object (for fluent API support)
     */
    public function setTurns(PropelCollection $turns, PropelPDO $con = null)
    {
        $turnsToDelete = $this->getTurns(new Criteria(), $con)->diff($turns);


        $this->turnsScheduledForDeletion = $turnsToDelete;

        foreach ($turnsToDelete as $turnRemoved) {
            $turnRemoved->setGame(null);
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
                ->filterByGame($this)
                ->count($con);
        }

        return count($this->collTurns);
    }

    /**
     * Method called to associate a Turn object to this object
     * through the Turn foreign key attribute.
     *
     * @param    Turn $l Turn
     * @return Game The current object (for fluent API support)
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
        $turn->setGame($this);
    }

    /**
     * @param	Turn $turn The turn object to remove.
     * @return Game The current object (for fluent API support)
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
            $turn->setGame(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Game is new, it will return
     * an empty collection; or if this Game has previously
     * been saved, it will retrieve related Turns from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Game.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Turn[] List of Turn objects
     */
    public function getTurnsJoinPlayer($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TurnQuery::create(null, $criteria);
        $query->joinWith('Player', $join_behavior);

        return $this->getTurns($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->deck = null;
        $this->discard = null;
        $this->shop = null;
        $this->playerone = null;
        $this->playeronehand = null;
        $this->playertwo = null;
        $this->playertwohand = null;
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
            if ($this->collTurns) {
                foreach ($this->collTurns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aPlayerRelatedByPlayerone instanceof Persistent) {
              $this->aPlayerRelatedByPlayerone->clearAllReferences($deep);
            }
            if ($this->aPlayerRelatedByPlayertwo instanceof Persistent) {
              $this->aPlayerRelatedByPlayertwo->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collTurns instanceof PropelCollection) {
            $this->collTurns->clearIterator();
        }
        $this->collTurns = null;
        $this->aPlayerRelatedByPlayerone = null;
        $this->aPlayerRelatedByPlayertwo = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(GamePeer::DEFAULT_STRING_FORMAT);
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
