<?php

/**
 * @package chamilo.webservices
 */
require_once __DIR__.'/../inc/global.inc.php';

// Create the server instance
$server = new soap_server();
$server->soap_defencoding = 'UTF-8';

// Initialize WSDL support
$server->configureWSDL('WSBlocklyGames', 'urn:WSBlocklyGames');

/* Register WSSaveBlocklyAttempt function */
// Register the data structures used by the service
$server->wsdl->addComplexType(
    'blocklyAttempt',
    'complexType',
    'struct',
    'all',
    '',
    [
        'exe_id' => ['name' => 'exe_id', 'type' => 'xsd:string'],
        'question_id' => ['name' => 'question_id', 'type' => 'xsd:string'],
        'user_id' => ['name' => 'user_id', 'type' => 'xsd:string'],
        'course_id' => ['name' => 'course_id', 'type' => 'xsd:string'],
        'session_id' => ['name' => 'session_id', 'type' => 'xsd:string'],
        'choice' => ['name' => 'choice', 'type' => 'xsd:string'],
    ]
);

// Register the method to expose
$server->register(
    'WSSaveBlocklyAttempt', // method name
    ['blocklyAttempt' => 'tns:blocklyAttempt'], // input parameters
    ['return' => 'xsd:string'], // output parameters
    'urn:WSRegistration', // namespace
    'urn:WSRegistration#WSSaveBlocklyAttempt', // soapaction
    'rpc', // style
    'encoded', // use
    'This service saves the status of Blockly-Games Execution Attempt'  // documentation
);

// Define the method WSSaveBlocklyAttempt
function WSSaveBlocklyAttempt($params)
{
    global $debug;

    $exe_id = $params['exe_id'];
    $questionScore = 0;
    $answer = $params['choice'];
    $question_id = $params['question_id'];
    $user_id = $params['user_id'];
    $course_id = $params['course_id'];
    $session_id = $params['session_id'];

    Event::saveQuestionAttempt($questionScore,
                               $answer,
                               $question_id,
                               $exe_id, 0, 0, true, null,
                               $user_id,
                               $course_id,
                               $session_id);

    return $choice;
}

// Use the request to (try to) invoke the service
$GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';

// If you send your data in utf8 then this value must be false.
$server->decode_utf8 = false;
$server->service($HTTP_RAW_POST_DATA);
