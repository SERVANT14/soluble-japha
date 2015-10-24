<?php

namespace Soluble\Japha\Db;

use Soluble\Japha\Bridge\Adapter as Adapter;

class DriverManagerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var string
     */
    protected $servlet_address;

    /**
     * @var Adapter
     */
    protected $adapter;
    
    /**
     *
     * @var DriverManager
     */
    protected $driverManager;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        \SolubleTestFactories::startJavaBridgeServer();
        $this->servlet_address = \SolubleTestFactories::getJavaBridgeServerAddress();
        $this->adapter = new Adapter(array(
            'driver' => 'Pjb62',
            'servlet_address' => $this->servlet_address,
        ));
        $this->driverManager = new DriverManager($this->adapter);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testCreateConnectionThrowsClassNotFoundException() {
        $this->setExpectedException('Soluble\Japha\Bridge\Exception\ClassNotFoundException');
        //$this->driverManager->createConnection()
        $config = \SolubleTestFactories::getDatabaseConfig();
        $host = $config['hostname'];
        $db = $config['database'];
        $user = $config['username'];
        $password = $config["password"];
        $dsn = "jdbc:mysql://$host/$db?user=$user&password=$password";
        $conn = $this->driverManager->createConnection($dsn, 'com.nuvolia.jdbc.JDBC4Connection');
    }

    public function testCreateConnectionThrowsSqlException() {
        $this->setExpectedException('Soluble\Japha\Bridge\Exception\SqlException');
        //$this->driverManager->createConnection()
        $config = \SolubleTestFactories::getDatabaseConfig();
        $host = $config['hostname'];
        $db = $config['database'];
        $user = $config['username'];
        $password = $config["password"];
        $dsn = "jdbc:invaliddbdriver://$host/$db?user=$user&password=$password";
        $conn = $this->driverManager->createConnection($dsn, 'com.mysql.jdbc.Driver');
    }

    public function testCreateConnectionThrowsInvalidArgumentException() {
        $this->setExpectedException('Soluble\Japha\Bridge\Exception\InvalidArgumentException');
        $dsn = "";
        $conn = $this->driverManager->createConnection($dsn, 'com.nuvolia.jdbc.JDBC4Connection');
    }

    public function testGetDriverManager() {
        $dm = $this->driverManager->getDriverManager();
        $this->assertInstanceOf('Soluble\Japha\Interfaces\JavaObject', $dm);
        $className = $this->adapter->getDriver()->getClassName($dm);
        $this->assertEquals('java.sql.DriverManager', $className);
        //$this->assertTrue($this->adapter->isInstanceOf($dm, 'java.sql.DriverManager'));
    }

    public function testCreateConnection() {
        //$this->driverManager->createConnection()
        $config = \SolubleTestFactories::getDatabaseConfig();
        $host = $config['hostname'];
        $db = $config['database'];
        $user = $config['username'];
        $password = $config["password"];

        $dsn = "jdbc:mysql://$host/$db?user=$user&password=$password";

        $conn = $this->driverManager->createConnection($dsn);

        $className = $this->adapter->getDriver()->getClassName($conn);
        $this->assertEquals('com.mysql.jdbc.JDBC4Connection', $className);
    }
    
}
