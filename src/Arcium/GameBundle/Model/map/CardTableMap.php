<?php

namespace Arcium\GameBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'cards' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Arcium.GameBundle.Model.map
 */
class CardTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Arcium.GameBundle.Model.map.CardTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('cards');
        $this->setPhpName('Card');
        $this->setClassname('Arcium\\GameBundle\\Model\\Card');
        $this->setPackage('src.Arcium.GameBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('game_id', 'GameId', 'INTEGER', true, 10, null);
        $this->addColumn('player_id', 'PlayerId', 'INTEGER', false, 10, null);
        $this->addColumn('cards', 'Cards', 'LONGVARCHAR', true, null, null);
        $this->addColumn('type', 'Type', 'CHAR', false, null, null);
        $this->getColumn('type', false)->setValueSet(array (
  0 => 'PILE',
  1 => 'HAND',
  2 => 'PLAY',
));
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('GameRelatedByDiscard', 'Arcium\\GameBundle\\Model\\Game', RelationMap::ONE_TO_MANY, array('id' => 'discard', ), null, 'CASCADE', 'GamesRelatedByDiscard');
        $this->addRelation('GameRelatedByDraw', 'Arcium\\GameBundle\\Model\\Game', RelationMap::ONE_TO_MANY, array('id' => 'draw', ), null, 'CASCADE', 'GamesRelatedByDraw');
        $this->addRelation('GameRelatedByShop', 'Arcium\\GameBundle\\Model\\Game', RelationMap::ONE_TO_MANY, array('id' => 'shop', ), null, 'CASCADE', 'GamesRelatedByShop');
    } // buildRelations()

} // CardTableMap
