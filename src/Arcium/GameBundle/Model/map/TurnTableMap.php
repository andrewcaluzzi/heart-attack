<?php

namespace Arcium\GameBundle\Model\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'turns' table.
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
class TurnTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Arcium.GameBundle.Model.map.TurnTableMap';

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
        $this->setName('turns');
        $this->setPhpName('Turn');
        $this->setClassname('Arcium\\GameBundle\\Model\\Turn');
        $this->setPackage('src.Arcium.GameBundle.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('game_id', 'GameId', 'INTEGER', 'games', 'id', true, 10, null);
        $this->addForeignKey('player_id', 'PlayerId', 'INTEGER', 'players', 'id', true, 10, null);
        $this->addColumn('phase', 'Phase', 'CHAR', true, null, null);
        $this->getColumn('phase', false)->setValueSet(array (
  0 => 'SETUP',
  1 => 'ATTACK',
  2 => 'SHOP',
  3 => 'CLEANUP',
));
        $this->addColumn('cards', 'Cards', 'VARCHAR', false, 100, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Game', 'Arcium\\GameBundle\\Model\\Game', RelationMap::MANY_TO_ONE, array('game_id' => 'id', ), null, 'CASCADE');
        $this->addRelation('Player', 'Arcium\\GameBundle\\Model\\Player', RelationMap::MANY_TO_ONE, array('player_id' => 'id', ), null, 'CASCADE');
    } // buildRelations()

} // TurnTableMap
