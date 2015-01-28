<?php

namespace Arcium\GameBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'players' table.
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
class PlayerTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Arcium.GameBundle.Model.map.PlayerTableMap';

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
        $this->setName('players');
        $this->setPhpName('Player');
        $this->setClassname('Arcium\\GameBundle\\Model\\Player');
        $this->setPackage('src.Arcium.GameBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('name', 'Name', 'VARCHAR', true, 100, null);
        $this->addColumn('username', 'Username', 'VARCHAR', true, 100, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('GameRelatedByPlayerone', 'Arcium\\GameBundle\\Model\\Game', RelationMap::ONE_TO_MANY, array('id' => 'playerOne', ), null, 'CASCADE', 'GamesRelatedByPlayerone');
        $this->addRelation('GameRelatedByPlayertwo', 'Arcium\\GameBundle\\Model\\Game', RelationMap::ONE_TO_MANY, array('id' => 'playerTwo', ), null, 'CASCADE', 'GamesRelatedByPlayertwo');
        $this->addRelation('Turn', 'Arcium\\GameBundle\\Model\\Turn', RelationMap::ONE_TO_MANY, array('id' => 'player_id', ), null, 'CASCADE', 'Turns');
    } // buildRelations()

} // PlayerTableMap
