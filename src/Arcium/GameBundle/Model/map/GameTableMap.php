<?php

namespace Arcium\GameBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'games' table.
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
class GameTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Arcium.GameBundle.Model.map.GameTableMap';

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
        $this->setName('games');
        $this->setPhpName('Game');
        $this->setClassname('Arcium\\GameBundle\\Model\\Game');
        $this->setPackage('src.Arcium.GameBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('deck', 'Deck', 'LONGVARCHAR', false, null, null);
        $this->addColumn('discard', 'Discard', 'LONGVARCHAR', false, null, null);
        $this->addColumn('shop', 'Shop', 'LONGVARCHAR', false, null, null);
        $this->addForeignKey('playerOne', 'Playerone', 'INTEGER', 'players', 'id', true, 10, null);
        $this->addColumn('playerOneHand', 'Playeronehand', 'LONGVARCHAR', false, null, null);
        $this->addForeignKey('playerTwo', 'Playertwo', 'INTEGER', 'players', 'id', true, 10, null);
        $this->addColumn('playerTwoHand', 'Playertwohand', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('PlayerRelatedByPlayerone', 'Arcium\\GameBundle\\Model\\Player', RelationMap::MANY_TO_ONE, array('playerOne' => 'id', ), null, 'CASCADE');
        $this->addRelation('PlayerRelatedByPlayertwo', 'Arcium\\GameBundle\\Model\\Player', RelationMap::MANY_TO_ONE, array('playerTwo' => 'id', ), null, 'CASCADE');
        $this->addRelation('Turn', 'Arcium\\GameBundle\\Model\\Turn', RelationMap::ONE_TO_MANY, array('id' => 'game_id', ), null, 'CASCADE', 'Turns');
    } // buildRelations()

} // GameTableMap
