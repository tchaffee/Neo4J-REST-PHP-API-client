<?php

require_once 'Neo4jRestTestCase.php';

/**
 * Test class for RelationshipDescription.
 * Generated by PHPUnit on 2011-04-23 at 09:19:02.
 */
class RelationshipDescriptionTest extends Neo4jRestTestCase
{
    /**
     * @var RelationshipDescription
     */
    protected $relDesc;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->relDesc = new Neo4jRest\RelationshipDescription('KNOWS');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function test__Contruct() {
        // Using direction
        $this->relDesc = new Neo4jRest\RelationshipDescription('KNOWS', 
            Neo4jRest\Relationship::DIRECTION_OUT);
    }
    
    /**
     * 
     */
    public function testAdd()
    {
        // Before it should have only 1 description.
        $this->assertEquals(1, sizeof($this->relDesc->get()));
        
        // No direction 
        $this->relDesc->add('loves');
        $relDescs = $this->relDesc->get();
        $this->assertEquals(2, sizeof($relDescs));

        // With direction
        $this->relDesc->add('is a friend of', 
           Neo4jRest\Relationship::DIRECTION_OUT);
        $relDescs = $this->relDesc->get();
        $this->assertEquals(3, sizeof($relDescs));
    }

    /**
     * 
     */
    public function testGet()
    {
        // Base case
        $relDescs = $this->relDesc->get();
        $this->assertArrayHasKey('type', $relDescs[0]);

        // With direction
        $this->relDesc->add('is a friend of', 
           Neo4jRest\Relationship::DIRECTION_OUT);
        $relDescs = $this->relDesc->get();
        $this->assertArrayHasKey('type', $relDescs[1]);
        $this->assertEquals('is a friend of', $relDescs[1]['type']);
        $this->assertArrayHasKey('direction', $relDescs[1]);
        $this->assertEquals(Neo4jRest\Relationship::DIRECTION_OUT, 
            $relDescs[1]['direction']);
    }
}
?>
