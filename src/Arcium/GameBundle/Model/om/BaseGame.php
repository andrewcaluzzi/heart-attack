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
use Arcium\GameBundle\Model\CardQuery;
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
     * The value for the draw field.
     * @var        int
     */
    protected $draw;

    /**
     * The value for the discard field.
     * @var        int
     */
    protected $discard;

    /**
     * The value for the shop field.
     * @var        int
     */
    protected $shop;

    /**
     * The value for the player_one_id field.
     * @var        int
     */
    protected $player_one_id;

    /**
     * The value for the player_two_id field.
     * @var        int
     */
    protected $player_two_id;

    /**
     * The value for the last_turn_id field.
     * @var        int
     */
    protected $last_turn_id;

    /**
     * @var        Card
     */
    protected $aCardRelatedByDiscard;

    /**
     * @var        Card
     */
    protected $aCardRelatedByDraw;

    /**
     * @var        Turn
     */
    protected $aTurnRelatedByLastTurnId;

    /**
     * @var        Player
     */
    protected $aPlayerRelatedByPlayerOneId;

    /**
     * @var        Player
     */
    protected $aPlayerRelatedByPlayerTwoId;

    /**
     * @var        Card
     */
    protected $aCardRelatedByShop;

    /**
     * @var        PropelObjectCollection|Turn[] Collection to store aggregation of Turn objects.
     */
    protected $collTurnsRelatedByGameId;
    protected $collTurnsRelatedByGameIdPartial;

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
    protected $turnsRelatedByGameIdScheduledForDeletion = null;

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
     * Get the [draw] column value.
     *
     * @return int
     */
    public function getDraw()
    {

        return $this->draw;
    }

    /**
     * Get the [discard] column value.
     *
     * @return int
     */
    public function getDiscard()
    {

        return $this->discard;
    }

    /**
     * Get the [shop] column value.
     *
     * @return int
     */
    public function getShop()
    {

        return $this->shop;
    }

    /**
     * Get the [player_one_id] column value.
     *
     * @return int
     */
    public function getPlayerOneId()
    {

        return $this->player_one_id;
    }

    /**
     * Get the [player_two_id] column value.
     *
     * @return int
     */
    public function getPlayerTwoId()
    {

        return $this->player_two_id;
    }

    /**
     * Get the [last_turn_id] column value.
     *
     * @return int
     */
    public function getLastTurnId()
    {

        return $this->last_turn_id;
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
     * Set the value of [draw] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setDraw($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->draw !== $v) {
            $this->draw = $v;
            $this->modifiedColumns[] = GamePeer::DRAW;
        }

        if ($this->aCardRelatedByDraw !== null && $this->aCardRelatedByDraw->getId() !== $v) {
            $this->aCardRelatedByDraw = null;
        }


        return $this;
    } // setDraw()

    /**
     * Set the value of [discard] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setDiscard($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->discard !== $v) {
            $this->discard = $v;
            $this->modifiedColumns[] = GamePeer::DISCARD;
        }

        if ($this->aCardRelatedByDiscard !== null && $this->aCardRelatedByDiscard->getId() !== $v) {
            $this->aCardRelatedByDiscard = null;
        }


        return $this;
    } // setDiscard()

    /**
     * Set the value of [shop] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setShop($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->shop !== $v) {
            $this->shop = $v;
            $this->modifiedColumns[] = GamePeer::SHOP;
        }

        if ($this->aCardRelatedByShop !== null && $this->aCardRelatedByShop->getId() !== $v) {
            $this->aCardRelatedByShop = null;
        }


        return $this;
    } // setShop()

    /**
     * Set the value of [player_one_id] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setPlayerOneId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->player_one_id !== $v) {
            $this->player_one_id = $v;
            $this->modifiedColumns[] = GamePeer::PLAYER_ONE_ID;
        }

        if ($this->aPlayerRelatedByPlayerOneId !== null && $this->aPlayerRelatedByPlayerOneId->getId() !== $v) {
            $this->aPlayerRelatedByPlayerOneId = null;
        }


        return $this;
    } // setPlayerOneId()

    /**
     * Set the value of [player_two_id] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setPlayerTwoId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->player_two_id !== $v) {
            $this->player_two_id = $v;
            $this->modifiedColumns[] = GamePeer::PLAYER_TWO_ID;
        }

        if ($this->aPlayerRelatedByPlayerTwoId !== null && $this->aPlayerRelatedByPlayerTwoId->getId() !== $v) {
            $this->aPlayerRelatedByPlayerTwoId = null;
        }


        return $this;
    } // setPlayerTwoId()

    /**
     * Set the value of [last_turn_id] column.
     *
     * @param  int $v new value
     * @return Game The current object (for fluent API support)
     */
    public function setLastTurnId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->last_turn_id !== $v) {
            $this->last_turn_id = $v;
            $this->modifiedColumns[] = GamePeer::LAST_TURN_ID;
        }

        if ($this->aTurnRelatedByLastTurnId !== null && $this->aTurnRelatedByLastTurnId->getId() !== $v) {
            $this->aTurnRelatedByLastTurnId = null;
        }


        return $this;
    } // setLastTurnId()

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
            $this->draw = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->discard = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->shop = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->player_one_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->player_two_id = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->last_turn_id = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 7; // 7 = GamePeer::NUM_HYDRATE_COLUMNS.

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

        if ($this->aCardRelatedByDraw !== null && $this->draw !== $this->aCardRelatedByDraw->getId()) {
            $this->aCardRelatedByDraw = null;
        }
        if ($this->aCardRelatedByDiscard !== null && $this->discard !== $this->aCardRelatedByDiscard->getId()) {
            $this->aCardRelatedByDiscard = null;
        }
        if ($this->aCardRelatedByShop !== null && $this->shop !== $this->aCardRelatedByShop->getId()) {
            $this->aCardRelatedByShop = null;
        }
        if ($this->aPlayerRelatedByPlayerOneId !== null && $this->player_one_id !== $this->aPlayerRelatedByPlayerOneId->getId()) {
            $this->aPlayerRelatedByPlayerOneId = null;
        }
        if ($this->aPlayerRelatedByPlayerTwoId !== null && $this->player_two_id !== $this->aPlayerRelatedByPlayerTwoId->getId()) {
            $this->aPlayerRelatedByPlayerTwoId = null;
        }
        if ($this->aTurnRelatedByLastTurnId !== null && $this->last_turn_id !== $this->aTurnRelatedByLastTurnId->getId()) {
            $this->aTurnRelatedByLastTurnId = null;
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

            $this->aCardRelatedByDiscard = null;
            $this->aCardRelatedByDraw = null;
            $this->aTurnRelatedByLastTurnId = null;
            $this->aPlayerRelatedByPlayerOneId = null;
            $this->aPlayerRelatedByPlayerTwoId = null;
            $this->aCardRelatedByShop = null;
            $this->collTurnsRelatedByGameId = null;

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

            if ($this->aCardRelatedByDiscard !== null) {
                if ($this->aCardRelatedByDiscard->isModified() || $this->aCardRelatedByDiscard->isNew()) {
                    $affectedRows += $this->aCardRelatedByDiscard->save($con);
                }
                $this->setCardRelatedByDiscard($this->aCardRelatedByDiscard);
            }

            if ($this->aCardRelatedByDraw !== null) {
                if ($this->aCardRelatedByDraw->isModified() || $this->aCardRelatedByDraw->isNew()) {
                    $affectedRows += $this->aCardRelatedByDraw->save($con);
                }
                $this->setCardRelatedByDraw($this->aCardRelatedByDraw);
            }

            if ($this->aTurnRelatedByLastTurnId !== null) {
                if ($this->aTurnRelatedByLastTurnId->isModified() || $this->aTurnRelatedByLastTurnId->isNew()) {
                    $affectedRows += $this->aTurnRelatedByLastTurnId->save($con);
                }
                $this->setTurnRelatedByLastTurnId($this->aTurnRelatedByLastTurnId);
            }

            if ($this->aPlayerRelatedByPlayerOneId !== null) {
                if ($this->aPlayerRelatedByPlayerOneId->isModified() || $this->aPlayerRelatedByPlayerOneId->isNew()) {
                    $affectedRows += $this->aPlayerRelatedByPlayerOneId->save($con);
                }
                $this->setPlayerRelatedByPlayerOneId($this->aPlayerRelatedByPlayerOneId);
            }

            if ($this->aPlayerRelatedByPlayerTwoId !== null) {
                if ($this->aPlayerRelatedByPlayerTwoId->isModified() || $this->aPlayerRelatedByPlayerTwoId->isNew()) {
                    $affectedRows += $this->aPlayerRelatedByPlayerTwoId->save($con);
                }
                $this->setPlayerRelatedByPlayerTwoId($this->aPlayerRelatedByPlayerTwoId);
            }

            if ($this->aCardRelatedByShop !== null) {
                if ($this->aCardRelatedByShop->isModified() || $this->aCardRelatedByShop->isNew()) {
                    $affectedRows += $this->aCardRelatedByShop->save($con);
                }
                $this->setCardRelatedByShop($this->aCardRelatedByShop);
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

            if ($this->turnsRelatedByGameIdScheduledForDeletion !== null) {
                if (!$this->turnsRelatedByGameIdScheduledForDeletion->isEmpty()) {
                    TurnQuery::create()
                        ->filterByPrimaryKeys($this->turnsRelatedByGameIdScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->turnsRelatedByGameIdScheduledForDeletion = null;
                }
            }

            if ($this->collTurnsRelatedByGameId !== null) {
                foreach ($this->collTurnsRelatedByGameId as $referrerFK) {
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
        if ($this->isColumnModified(GamePeer::DRAW)) {
            $modifiedColumns[':p' . $index++]  = '`draw`';
        }
        if ($this->isColumnModified(GamePeer::DISCARD)) {
            $modifiedColumns[':p' . $index++]  = '`discard`';
        }
        if ($this->isColumnModified(GamePeer::SHOP)) {
            $modifiedColumns[':p' . $index++]  = '`shop`';
        }
        if ($this->isColumnModified(GamePeer::PLAYER_ONE_ID)) {
            $modifiedColumns[':p' . $index++]  = '`player_one_id`';
        }
        if ($this->isColumnModified(GamePeer::PLAYER_TWO_ID)) {
            $modifiedColumns[':p' . $index++]  = '`player_two_id`';
        }
        if ($this->isColumnModified(GamePeer::LAST_TURN_ID)) {
            $modifiedColumns[':p' . $index++]  = '`last_turn_id`';
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
                    case '`draw`':
                        $stmt->bindValue($identifier, $this->draw, PDO::PARAM_INT);
                        break;
                    case '`discard`':
                        $stmt->bindValue($identifier, $this->discard, PDO::PARAM_INT);
                        break;
                    case '`shop`':
                        $stmt->bindValue($identifier, $this->shop, PDO::PARAM_INT);
                        break;
                    case '`player_one_id`':
                        $stmt->bindValue($identifier, $this->player_one_id, PDO::PARAM_INT);
                        break;
                    case '`player_two_id`':
                        $stmt->bindValue($identifier, $this->player_two_id, PDO::PARAM_INT);
                        break;
                    case '`last_turn_id`':
                        $stmt->bindValue($identifier, $this->last_turn_id, PDO::PARAM_INT);
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

            if ($this->aCardRelatedByDiscard !== null) {
                if (!$this->aCardRelatedByDiscard->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCardRelatedByDiscard->getValidationFailures());
                }
            }

            if ($this->aCardRelatedByDraw !== null) {
                if (!$this->aCardRelatedByDraw->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCardRelatedByDraw->getValidationFailures());
                }
            }

            if ($this->aTurnRelatedByLastTurnId !== null) {
                if (!$this->aTurnRelatedByLastTurnId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aTurnRelatedByLastTurnId->getValidationFailures());
                }
            }

            if ($this->aPlayerRelatedByPlayerOneId !== null) {
                if (!$this->aPlayerRelatedByPlayerOneId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayerRelatedByPlayerOneId->getValidationFailures());
                }
            }

            if ($this->aPlayerRelatedByPlayerTwoId !== null) {
                if (!$this->aPlayerRelatedByPlayerTwoId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPlayerRelatedByPlayerTwoId->getValidationFailures());
                }
            }

            if ($this->aCardRelatedByShop !== null) {
                if (!$this->aCardRelatedByShop->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCardRelatedByShop->getValidationFailures());
                }
            }


            if (($retval = GamePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collTurnsRelatedByGameId !== null) {
                    foreach ($this->collTurnsRelatedByGameId as $referrerFK) {
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
                return $this->getDraw();
                break;
            case 2:
                return $this->getDiscard();
                break;
            case 3:
                return $this->getShop();
                break;
            case 4:
                return $this->getPlayerOneId();
                break;
            case 5:
                return $this->getPlayerTwoId();
                break;
            case 6:
                return $this->getLastTurnId();
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
            $keys[1] => $this->getDraw(),
            $keys[2] => $this->getDiscard(),
            $keys[3] => $this->getShop(),
            $keys[4] => $this->getPlayerOneId(),
            $keys[5] => $this->getPlayerTwoId(),
            $keys[6] => $this->getLastTurnId(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCardRelatedByDiscard) {
                $result['CardRelatedByDiscard'] = $this->aCardRelatedByDiscard->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCardRelatedByDraw) {
                $result['CardRelatedByDraw'] = $this->aCardRelatedByDraw->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aTurnRelatedByLastTurnId) {
                $result['TurnRelatedByLastTurnId'] = $this->aTurnRelatedByLastTurnId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPlayerRelatedByPlayerOneId) {
                $result['PlayerRelatedByPlayerOneId'] = $this->aPlayerRelatedByPlayerOneId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPlayerRelatedByPlayerTwoId) {
                $result['PlayerRelatedByPlayerTwoId'] = $this->aPlayerRelatedByPlayerTwoId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aCardRelatedByShop) {
                $result['CardRelatedByShop'] = $this->aCardRelatedByShop->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collTurnsRelatedByGameId) {
                $result['TurnsRelatedByGameId'] = $this->collTurnsRelatedByGameId->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
                $this->setDraw($value);
                break;
            case 2:
                $this->setDiscard($value);
                break;
            case 3:
                $this->setShop($value);
                break;
            case 4:
                $this->setPlayerOneId($value);
                break;
            case 5:
                $this->setPlayerTwoId($value);
                break;
            case 6:
                $this->setLastTurnId($value);
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
        if (array_key_exists($keys[1], $arr)) $this->setDraw($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDiscard($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setShop($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPlayerOneId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setPlayerTwoId($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setLastTurnId($arr[$keys[6]]);
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
        if ($this->isColumnModified(GamePeer::DRAW)) $criteria->add(GamePeer::DRAW, $this->draw);
        if ($this->isColumnModified(GamePeer::DISCARD)) $criteria->add(GamePeer::DISCARD, $this->discard);
        if ($this->isColumnModified(GamePeer::SHOP)) $criteria->add(GamePeer::SHOP, $this->shop);
        if ($this->isColumnModified(GamePeer::PLAYER_ONE_ID)) $criteria->add(GamePeer::PLAYER_ONE_ID, $this->player_one_id);
        if ($this->isColumnModified(GamePeer::PLAYER_TWO_ID)) $criteria->add(GamePeer::PLAYER_TWO_ID, $this->player_two_id);
        if ($this->isColumnModified(GamePeer::LAST_TURN_ID)) $criteria->add(GamePeer::LAST_TURN_ID, $this->last_turn_id);

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
        $copyObj->setDraw($this->getDraw());
        $copyObj->setDiscard($this->getDiscard());
        $copyObj->setShop($this->getShop());
        $copyObj->setPlayerOneId($this->getPlayerOneId());
        $copyObj->setPlayerTwoId($this->getPlayerTwoId());
        $copyObj->setLastTurnId($this->getLastTurnId());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getTurnsRelatedByGameId() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTurnRelatedByGameId($relObj->copy($deepCopy));
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
     * Declares an association between this object and a Card object.
     *
     * @param                  Card $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCardRelatedByDiscard(Card $v = null)
    {
        if ($v === null) {
            $this->setDiscard(NULL);
        } else {
            $this->setDiscard($v->getId());
        }

        $this->aCardRelatedByDiscard = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Card object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByDiscard($this);
        }


        return $this;
    }


    /**
     * Get the associated Card object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Card The associated Card object.
     * @throws PropelException
     */
    public function getCardRelatedByDiscard(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCardRelatedByDiscard === null && ($this->discard !== null) && $doQuery) {
            $this->aCardRelatedByDiscard = CardQuery::create()->findPk($this->discard, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCardRelatedByDiscard->addGamesRelatedByDiscard($this);
             */
        }

        return $this->aCardRelatedByDiscard;
    }

    /**
     * Declares an association between this object and a Card object.
     *
     * @param                  Card $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCardRelatedByDraw(Card $v = null)
    {
        if ($v === null) {
            $this->setDraw(NULL);
        } else {
            $this->setDraw($v->getId());
        }

        $this->aCardRelatedByDraw = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Card object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByDraw($this);
        }


        return $this;
    }


    /**
     * Get the associated Card object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Card The associated Card object.
     * @throws PropelException
     */
    public function getCardRelatedByDraw(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCardRelatedByDraw === null && ($this->draw !== null) && $doQuery) {
            $this->aCardRelatedByDraw = CardQuery::create()->findPk($this->draw, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCardRelatedByDraw->addGamesRelatedByDraw($this);
             */
        }

        return $this->aCardRelatedByDraw;
    }

    /**
     * Declares an association between this object and a Turn object.
     *
     * @param                  Turn $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setTurnRelatedByLastTurnId(Turn $v = null)
    {
        if ($v === null) {
            $this->setLastTurnId(NULL);
        } else {
            $this->setLastTurnId($v->getId());
        }

        $this->aTurnRelatedByLastTurnId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Turn object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByLastTurnId($this);
        }


        return $this;
    }


    /**
     * Get the associated Turn object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Turn The associated Turn object.
     * @throws PropelException
     */
    public function getTurnRelatedByLastTurnId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aTurnRelatedByLastTurnId === null && ($this->last_turn_id !== null) && $doQuery) {
            $this->aTurnRelatedByLastTurnId = TurnQuery::create()->findPk($this->last_turn_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aTurnRelatedByLastTurnId->addGamesRelatedByLastTurnId($this);
             */
        }

        return $this->aTurnRelatedByLastTurnId;
    }

    /**
     * Declares an association between this object and a Player object.
     *
     * @param                  Player $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayerRelatedByPlayerOneId(Player $v = null)
    {
        if ($v === null) {
            $this->setPlayerOneId(NULL);
        } else {
            $this->setPlayerOneId($v->getId());
        }

        $this->aPlayerRelatedByPlayerOneId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Player object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByPlayerOneId($this);
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
    public function getPlayerRelatedByPlayerOneId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayerRelatedByPlayerOneId === null && ($this->player_one_id !== null) && $doQuery) {
            $this->aPlayerRelatedByPlayerOneId = PlayerQuery::create()->findPk($this->player_one_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayerRelatedByPlayerOneId->addGamesRelatedByPlayerOneId($this);
             */
        }

        return $this->aPlayerRelatedByPlayerOneId;
    }

    /**
     * Declares an association between this object and a Player object.
     *
     * @param                  Player $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPlayerRelatedByPlayerTwoId(Player $v = null)
    {
        if ($v === null) {
            $this->setPlayerTwoId(NULL);
        } else {
            $this->setPlayerTwoId($v->getId());
        }

        $this->aPlayerRelatedByPlayerTwoId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Player object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByPlayerTwoId($this);
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
    public function getPlayerRelatedByPlayerTwoId(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aPlayerRelatedByPlayerTwoId === null && ($this->player_two_id !== null) && $doQuery) {
            $this->aPlayerRelatedByPlayerTwoId = PlayerQuery::create()->findPk($this->player_two_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPlayerRelatedByPlayerTwoId->addGamesRelatedByPlayerTwoId($this);
             */
        }

        return $this->aPlayerRelatedByPlayerTwoId;
    }

    /**
     * Declares an association between this object and a Card object.
     *
     * @param                  Card $v
     * @return Game The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCardRelatedByShop(Card $v = null)
    {
        if ($v === null) {
            $this->setShop(NULL);
        } else {
            $this->setShop($v->getId());
        }

        $this->aCardRelatedByShop = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Card object, it will not be re-added.
        if ($v !== null) {
            $v->addGameRelatedByShop($this);
        }


        return $this;
    }


    /**
     * Get the associated Card object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Card The associated Card object.
     * @throws PropelException
     */
    public function getCardRelatedByShop(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCardRelatedByShop === null && ($this->shop !== null) && $doQuery) {
            $this->aCardRelatedByShop = CardQuery::create()->findPk($this->shop, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCardRelatedByShop->addGamesRelatedByShop($this);
             */
        }

        return $this->aCardRelatedByShop;
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
        if ('TurnRelatedByGameId' == $relationName) {
            $this->initTurnsRelatedByGameId();
        }
    }

    /**
     * Clears out the collTurnsRelatedByGameId collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Game The current object (for fluent API support)
     * @see        addTurnsRelatedByGameId()
     */
    public function clearTurnsRelatedByGameId()
    {
        $this->collTurnsRelatedByGameId = null; // important to set this to null since that means it is uninitialized
        $this->collTurnsRelatedByGameIdPartial = null;

        return $this;
    }

    /**
     * reset is the collTurnsRelatedByGameId collection loaded partially
     *
     * @return void
     */
    public function resetPartialTurnsRelatedByGameId($v = true)
    {
        $this->collTurnsRelatedByGameIdPartial = $v;
    }

    /**
     * Initializes the collTurnsRelatedByGameId collection.
     *
     * By default this just sets the collTurnsRelatedByGameId collection to an empty array (like clearcollTurnsRelatedByGameId());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTurnsRelatedByGameId($overrideExisting = true)
    {
        if (null !== $this->collTurnsRelatedByGameId && !$overrideExisting) {
            return;
        }
        $this->collTurnsRelatedByGameId = new PropelObjectCollection();
        $this->collTurnsRelatedByGameId->setModel('Turn');
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
    public function getTurnsRelatedByGameId($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTurnsRelatedByGameIdPartial && !$this->isNew();
        if (null === $this->collTurnsRelatedByGameId || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTurnsRelatedByGameId) {
                // return empty collection
                $this->initTurnsRelatedByGameId();
            } else {
                $collTurnsRelatedByGameId = TurnQuery::create(null, $criteria)
                    ->filterByGameRelatedByGameId($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTurnsRelatedByGameIdPartial && count($collTurnsRelatedByGameId)) {
                      $this->initTurnsRelatedByGameId(false);

                      foreach ($collTurnsRelatedByGameId as $obj) {
                        if (false == $this->collTurnsRelatedByGameId->contains($obj)) {
                          $this->collTurnsRelatedByGameId->append($obj);
                        }
                      }

                      $this->collTurnsRelatedByGameIdPartial = true;
                    }

                    $collTurnsRelatedByGameId->getInternalIterator()->rewind();

                    return $collTurnsRelatedByGameId;
                }

                if ($partial && $this->collTurnsRelatedByGameId) {
                    foreach ($this->collTurnsRelatedByGameId as $obj) {
                        if ($obj->isNew()) {
                            $collTurnsRelatedByGameId[] = $obj;
                        }
                    }
                }

                $this->collTurnsRelatedByGameId = $collTurnsRelatedByGameId;
                $this->collTurnsRelatedByGameIdPartial = false;
            }
        }

        return $this->collTurnsRelatedByGameId;
    }

    /**
     * Sets a collection of TurnRelatedByGameId objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $turnsRelatedByGameId A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Game The current object (for fluent API support)
     */
    public function setTurnsRelatedByGameId(PropelCollection $turnsRelatedByGameId, PropelPDO $con = null)
    {
        $turnsRelatedByGameIdToDelete = $this->getTurnsRelatedByGameId(new Criteria(), $con)->diff($turnsRelatedByGameId);


        $this->turnsRelatedByGameIdScheduledForDeletion = $turnsRelatedByGameIdToDelete;

        foreach ($turnsRelatedByGameIdToDelete as $turnRelatedByGameIdRemoved) {
            $turnRelatedByGameIdRemoved->setGameRelatedByGameId(null);
        }

        $this->collTurnsRelatedByGameId = null;
        foreach ($turnsRelatedByGameId as $turnRelatedByGameId) {
            $this->addTurnRelatedByGameId($turnRelatedByGameId);
        }

        $this->collTurnsRelatedByGameId = $turnsRelatedByGameId;
        $this->collTurnsRelatedByGameIdPartial = false;

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
    public function countTurnsRelatedByGameId(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTurnsRelatedByGameIdPartial && !$this->isNew();
        if (null === $this->collTurnsRelatedByGameId || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTurnsRelatedByGameId) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getTurnsRelatedByGameId());
            }
            $query = TurnQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByGameRelatedByGameId($this)
                ->count($con);
        }

        return count($this->collTurnsRelatedByGameId);
    }

    /**
     * Method called to associate a Turn object to this object
     * through the Turn foreign key attribute.
     *
     * @param    Turn $l Turn
     * @return Game The current object (for fluent API support)
     */
    public function addTurnRelatedByGameId(Turn $l)
    {
        if ($this->collTurnsRelatedByGameId === null) {
            $this->initTurnsRelatedByGameId();
            $this->collTurnsRelatedByGameIdPartial = true;
        }

        if (!in_array($l, $this->collTurnsRelatedByGameId->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTurnRelatedByGameId($l);

            if ($this->turnsRelatedByGameIdScheduledForDeletion and $this->turnsRelatedByGameIdScheduledForDeletion->contains($l)) {
                $this->turnsRelatedByGameIdScheduledForDeletion->remove($this->turnsRelatedByGameIdScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param	TurnRelatedByGameId $turnRelatedByGameId The turnRelatedByGameId object to add.
     */
    protected function doAddTurnRelatedByGameId($turnRelatedByGameId)
    {
        $this->collTurnsRelatedByGameId[]= $turnRelatedByGameId;
        $turnRelatedByGameId->setGameRelatedByGameId($this);
    }

    /**
     * @param	TurnRelatedByGameId $turnRelatedByGameId The turnRelatedByGameId object to remove.
     * @return Game The current object (for fluent API support)
     */
    public function removeTurnRelatedByGameId($turnRelatedByGameId)
    {
        if ($this->getTurnsRelatedByGameId()->contains($turnRelatedByGameId)) {
            $this->collTurnsRelatedByGameId->remove($this->collTurnsRelatedByGameId->search($turnRelatedByGameId));
            if (null === $this->turnsRelatedByGameIdScheduledForDeletion) {
                $this->turnsRelatedByGameIdScheduledForDeletion = clone $this->collTurnsRelatedByGameId;
                $this->turnsRelatedByGameIdScheduledForDeletion->clear();
            }
            $this->turnsRelatedByGameIdScheduledForDeletion[]= clone $turnRelatedByGameId;
            $turnRelatedByGameId->setGameRelatedByGameId(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Game is new, it will return
     * an empty collection; or if this Game has previously
     * been saved, it will retrieve related TurnsRelatedByGameId from storage.
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
    public function getTurnsRelatedByGameIdJoinPlayer($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TurnQuery::create(null, $criteria);
        $query->joinWith('Player', $join_behavior);

        return $this->getTurnsRelatedByGameId($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->draw = null;
        $this->discard = null;
        $this->shop = null;
        $this->player_one_id = null;
        $this->player_two_id = null;
        $this->last_turn_id = null;
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
            if ($this->collTurnsRelatedByGameId) {
                foreach ($this->collTurnsRelatedByGameId as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCardRelatedByDiscard instanceof Persistent) {
              $this->aCardRelatedByDiscard->clearAllReferences($deep);
            }
            if ($this->aCardRelatedByDraw instanceof Persistent) {
              $this->aCardRelatedByDraw->clearAllReferences($deep);
            }
            if ($this->aTurnRelatedByLastTurnId instanceof Persistent) {
              $this->aTurnRelatedByLastTurnId->clearAllReferences($deep);
            }
            if ($this->aPlayerRelatedByPlayerOneId instanceof Persistent) {
              $this->aPlayerRelatedByPlayerOneId->clearAllReferences($deep);
            }
            if ($this->aPlayerRelatedByPlayerTwoId instanceof Persistent) {
              $this->aPlayerRelatedByPlayerTwoId->clearAllReferences($deep);
            }
            if ($this->aCardRelatedByShop instanceof Persistent) {
              $this->aCardRelatedByShop->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collTurnsRelatedByGameId instanceof PropelCollection) {
            $this->collTurnsRelatedByGameId->clearIterator();
        }
        $this->collTurnsRelatedByGameId = null;
        $this->aCardRelatedByDiscard = null;
        $this->aCardRelatedByDraw = null;
        $this->aTurnRelatedByLastTurnId = null;
        $this->aPlayerRelatedByPlayerOneId = null;
        $this->aPlayerRelatedByPlayerTwoId = null;
        $this->aCardRelatedByShop = null;
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
