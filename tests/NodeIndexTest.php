<?php

require_once 'Neo4jRestTestCase.php';

use Neo4jRest\Node as Node;
use Neo4jRest\Neo4jRest_HttpException as Neo4jRest_HttpException;
use Neo4jRest\Neo4jRest_NotFoundException as Neo4jRest_NotFoundException;

/**
 * Test class for NodeIndex.
 * Generated by PHPUnit on 2011-04-19 at 02:46:57.
 */
class NodeIndexTest extends Neo4jRestTestCase
{
    /**
     * @var NodeIndex
     */
    protected $index;
    protected $indexMgr;
    protected $indexName;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->indexName = 'TestIndex';
        $this->indexMgr = $this->graphDb->index();
        $this->index = $this->indexMgr->forNodes($this->indexName); 
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * 
     */
    public function testGetEntityType()
    {
        $index = $this->index;
        
        $type = $index->getEntityType();
        
        $this->assertEquals('Neo4jRest\Node', $type);
    }
    
    /**
     * @todo Implement testAdd().
     */
    public function testAdd()
    {
        $node = new Node($this->graphDb);
        $node->save();
        $key = mt_rand();
        $value = mt_rand();
        
        $this->index->add($node, $key, $value);
        
        $nodes = $this->index->get($key, $value);
        
        $this->assertEquals($node, $nodes[0]);
        
        // Clean up.
        $this->index->remove($node, $key, $value);
        
    }

    /**
     * @todo Implement testRemove().
     */
    public function testRemove()
    {
        // We get the node back before removing the index
        $node = new Node($this->graphDb);
        $node->save();
        $key = mt_rand();
        $value = mt_rand();
        
        $this->index->add($node, $key, $value);
        
        $nodes = $this->index->get($key, $value);
        
        $this->assertEquals($node, $nodes[0]);
        
        // But not after.
        $this->index->remove($node, $key, $value);

        $e = NULL;
        try {
            $nodes = $this->index->get($key, $value);
        }
        catch (Neo4jRest_NotFoundException $e) {
            $this->assertEquals(400, $e->getCode());
        }
        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_NotFoundException', $e);
        
        // Try the same using no value.
        // We get the node back before removing the index
        $node = new Node($this->graphDb);
        $node->save();
        $key = mt_rand();
        $value = mt_rand();
        
        $this->index->add($node, $key, $value);
        
        $nodes = $this->index->get($key, $value);
        
        $this->assertEquals($node, $nodes[0]);
        
        // But not after.
        $this->index->remove($node, $key);

        $e = NULL;
        try {
            $nodes = $this->index->get($key, $value);
        }
        catch (Neo4jRest_NotFoundException $e) {
            $this->assertEquals(400, $e->getCode());
        }
        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_NotFoundException', $e);
        
        // Try the same using no key and no value
        // We get the node back before removing the index
        $node = new Node($this->graphDb);
        $node->save();
        $key = mt_rand();
        $value = mt_rand();
        
        $this->index->add($node, $key, $value);
        
        $nodes = $this->index->get($key, $value);
        
        $this->assertEquals($node, $nodes[0]);
        
        // But not after.
        $this->index->remove($node);

        $e = NULL;
        try {
            $nodes = $this->index->get($key, $value);
        }
        catch (Neo4jRest_NotFoundException $e) {
            $this->assertEquals(400, $e->getCode());
        }
        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_NotFoundException', $e);

        // Try to test raising a general Http exception.
        $node = new Node($this->graphDb);
        $node->save();        
        $e = NULL;
        try {

            $nodeIndex = new MockNodeIndex('TestIndex', $this->graphDb);
                      
            $nodeIndex->remove($node, 'Key', 'Value');
        }
        catch (Neo4jRest_HttpException $e) {
        } 
        
        $node->delete();
        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_HttpException', $e);
        
        
        // Try to test raising a 404 Not Found exception.
        // TODO: Ooops REST API or documentation is broken.  Skip for now.
/*        
        $node = new Node($this->graphDb);
        $node->save();        
        $e = NULL;
        try {
            $nodes = $this->index->remove($node, 'bogus key', 'bogus value');
        }
        catch (Neo4jRest_NotFoundException $e) {
            $this->assertEquals(404, $e->getCode());
        }
        
        $node->delete();
        
        $this->assertInstanceOf('Neo4jRest_NotFoundException', $e);
*/        
                
    }

    /**
     * @todo Implement testGet().
     */
    public function testGet()
    {
        // Basic test case.
        $node = new Node($this->graphDb);
        $node->save();
        $key = mt_rand();
        $value = mt_rand();
        
        $this->index->add($node, $key, $value);
        
        $nodes = $this->index->get($key, $value);

        $this->assertEquals($node, $nodes[0]);

        $this->index->remove($node, $key, $value); // Clean up.

        // Now that the index isn't there we should get a Not Found Exception.
        try {
            $nodes = $this->index->get($key, $value);
        }
        catch (Neo4jRest_NotFoundException $e) {
            $this->assertEquals(400, $e->getCode());
        }
                        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_NotFoundException', $e);    
        
        $node->delete(); // Clean up.
        
        // Make sure we generate a general Http exception if the uri
        // is bogus.
        $node = new Node($this->graphDb);
        $node->save();        
        $e = NULL;
        try {

            $nodeIndex = new MockNodeIndex($this->indexName, $this->graphDb);
            $nodeIndex->get('Key', 'Value');
        }
        catch (Neo4jRest_HttpException $e) {
        }        
        $node->delete();
        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_HttpException', $e);        
        
    }

    /**
     * 
     */
    public function testQuery()
    {
        // Basic test case.
        $node = new Node($this->graphDb);
        $node->save();
        $key = 'A Key';
        $value = 'A Value With Spaces';
        
        $this->index->add($node, $key, $value);
        
        $nodes = $this->index->query($key, 'A Value With Spac*');

        $this->assertEquals($node, $nodes[0]);

        $this->index->remove($node, $key, $value); // Clean up.
        $node->delete(); // Clean up.
        
        // Make sure we generate a general Http exception if the uri
        // is bogus.
        $node = new Node($this->graphDb);
        $node->save();        
        $e = NULL;
        try {

            $nodeIndex = new MockNodeIndex($this->indexName, $this->graphDb);
            $nodeIndex->query('Key', 'Value');
        }
        catch (Neo4jRest_HttpException $e) {
        }        
        $node->delete();
        
        $this->assertInstanceOf('Neo4jRest\Neo4jRest_HttpException', $e);        
        
    }

    /**
     * @todo Implement testGetName().
     */
    public function testGetName()
    {
        $this->assertEquals($this->indexName, $this->index->getName());
    }

    /**
     * 
     */
    public function testGetUri()
    {
        $uri = $this->index->getUri();
        
        $this->assertInternalType('string', $uri);
        $this->assertStringStartsWith($this->graphDb->getBaseUri(), $uri);
    }
}

/**
 * 
 * Mock object used to generate a general Http exception for Index 
 * based classes.
 * 
 * @author tchaffee
 *
 */
class MockNodeIndex extends Neo4jRest\NodeIndex {
    
    // Override the getUri function so it returns a bogus Uri.
    function getUri() {
        return 'x';
    }
}    

?>
