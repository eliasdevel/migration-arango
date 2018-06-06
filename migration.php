<?php 
require('vendor/triagens/arangodb/autoload.php');
\ArangoDBClient\Autoloader::init();
// use the following line when using Composer
// require __DIR__ . '/vendor/composer/autoload.php';

// use the following line when using git

// set up some aliases for less typing later
use ArangoDBClient\Collection as ArangoCollection;
use ArangoDBClient\CollectionHandler as ArangoCollectionHandler;
use ArangoDBClient\Connection as ArangoConnection;
use ArangoDBClient\ConnectionOptions as ArangoConnectionOptions;
use ArangoDBClient\DocumentHandler as ArangoDocumentHandler;
use ArangoDBClient\Document as ArangoDocument;
use ArangoDBClient\Exception as ArangoException;
use ArangoDBClient\Export as ArangoExport;
use ArangoDBClient\ConnectException as ArangoConnectException;
use ArangoDBClient\ClientException as ArangoClientException;
use ArangoDBClient\ServerException as ArangoServerException;
use ArangoDBClient\Statement as ArangoStatement;
use ArangoDBClient\UpdatePolicy as ArangoUpdatePolicy;

// set up some basic connection options
$connectionOptions = [
    // database name
    ArangoConnectionOptions::OPTION_DATABASE => '_system',
    // server endpoint to connect to
    ArangoConnectionOptions::OPTION_ENDPOINT => 'tcp://127.0.0.1:8529',
    // authorization type to use (currently supported: 'Basic')
    ArangoConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
    // user for basic authorization
    ArangoConnectionOptions::OPTION_AUTH_USER => 'root',
    // password for basic authorization
    ArangoConnectionOptions::OPTION_AUTH_PASSWD => '123456',
    // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
    ArangoConnectionOptions::OPTION_CONNECTION => 'Keep-Alive',
    // connect timeout in seconds
    ArangoConnectionOptions::OPTION_TIMEOUT => 3,
    // whether or not to reconnect when a keep-alive connection has timed out on server
    ArangoConnectionOptions::OPTION_RECONNECT => true,
    // optionally create new collections when inserting documents
    ArangoConnectionOptions::OPTION_CREATE => true,
    // optionally create new collections when inserting documents
    ArangoConnectionOptions::OPTION_UPDATE_POLICY => ArangoUpdatePolicy::LAST,
];


// turn on exception logging (logs to whatever PHP is configured)
//ArangoException::enableLogging();


    $connection = new ArangoConnection($connectionOptions);
$mysqli = new mysqli("localhost", "root", "123456", "sis_banco_dados");



if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}



function migrate_table($mysqli,$table_name,$connection){
$migrated = ['acervos','autores','editoras'];
	if(!in_array($table_name,$migrated)){

	    $collectionHandler = new ArangoCollectionHandler($connection);

    	// clean up first
    	if ($collectionHandler->has($table_name)) {
        	$collectionHandler->drop($table_name);
    	}
	
	 // create a new collection
	$userCollection = new ArangoCollection();
	$userCollection->setName($table_name);
	$id = $collectionHandler->create($userCollection);

	
	$r = $mysqli->query("Select * From $table_name ");
	 $handler = new ArangoDocumentHandler($connection);

	while($row = mysqli_fetch_assoc($r)){
		 // create a new document
	        $document = new ArangoDocument();

		foreach($row as $k => $v){
			$document->$k = utf8_encode($v);
		}
		$handler->save($table_name, $document);
//		 var_dump($row);

    		
    	}

	}
	


}


        $r = $mysqli->query("show tables; ");
        while($row = mysqli_fetch_assoc($r)){
                 var_dump($row['Tables_in_sis_banco_dados']);
		migrate_table($mysqli,$row['Tables_in_sis_banco_dados'],$connection);
               
        }

//migrate_table($mysqli,'tipos_acervos',$connection);

