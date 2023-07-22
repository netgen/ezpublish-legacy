<?php
/**
 * File containing the eZBenchmarkrunner class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZBenchmarkrunner ezbenchmarkrunner.php
  \brief The class eZBenchmarkrunner does

*/

class eZBenchmarkrunner
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->IsSuccessful = false;
        $this->DefaultRepeatCount = 50;
    }

    function run( &$benchmark, $display = false )
    {
        $this->Results = [];
        $this->CurrentResult = false;
        if ( is_subclass_of( $benchmark, 'ezbenchmarkunit' ) )
        {
            $markList = $benchmark->markList();
            foreach ( $markList as $mark )
            {
                $type = $this->markEntryType( $benchmark, $mark );
                if ( $type )
                {
                    $mark['type'] = $type;
                    $this->prepareMarkEntry( $benchmark, $mark );

                    $this->runMarkEntry( $benchmark, $mark );

                    $this->finalizeMarkEntry( $benchmark, $mark, $display );
                }
                else
                    $this->addToCurrentResult( $mark,
                                               "Unknown mark type for mark " . $benchmark->name() . '::' . $mark['name'] );
            }
        }
        else
        {
            eZDebug::writeWarning( "Tried to run test on an object which is not subclassed from eZBenchmarkCase", __METHOD__ );
        }
    }

    function markEntryType( $benchmark, $entry )
    {
        if ( isset( $entry['method'] ) and
             isset( $entry['object'] ) )
        {
            return 'method';
        }
        else if ( isset( $entry['function'] ) )
        {
            return 'function';
        }
        return false;
    }

    function prepareMarkEntry( &$benchmark, $entry )
    {
        $this->setCurrentMarkName( $benchmark->name() . '::' . $entry['name'] );
        $this->resetCurrentResult();
    }

    function finalizeMarkEntry( &$benchmark, $entry, $display )
    {
        $currentResult = $this->addCurrentResult();
        $this->setCurrentMarkName( false );

        if ( $display )
            $this->display( $currentResult );
    }

    function runMarkEntry( &$benchmark, $entry )
    {
        switch ( $entry['type'] )
        {
            case 'method':
            {
                $object =& $entry['object'];
                $method =& $entry['method'];
                if ( method_exists( $object, $method ) )
                {
                    if ( method_exists( $object, 'prime' ) )
                    {
                        $entry['prime_start'] = ['memory' => memory_get_usage(), 'time' => microtime()];
                        $object->prime( $this );
                        $entry['prime_end'] = ['memory' => memory_get_usage(), 'time' => microtime()];
                    }

                    $repeatCount = $this->DefaultRepeatCount;
                    if ( isset( $entry['repeat_count'] ) )
                        $repeatCount = $entry['repeat_count'];

                    $entry['start'] = ['memory' => memory_get_usage(), 'time' => microtime()];
                    for ( $i = 0; $i < $repeatCount; ++$i )
                    {
                        $object->$method( $this, $entry['parameter'] );
                    }
                    $entry['end'] = ['memory' => memory_get_usage(), 'time' => microtime()];

                    if ( method_exists( $object, 'cleanup' ) )
                        $object->cleanup( $this );

                    $this->processRecording( $benchmark, $entry, $repeatCount );
                }
                else
                {
                    $this->addToCurrentResult( $entry,
                                               "Method $method does not exist for mark object(" . $object::class . ")" );
                }
            } break;

            case 'function':
            {
                $function = $entry['function'];
                if ( function_exists( $function ) )
                {
                    $repeatCount = $this->DefaultRepeatCount;
                    if ( isset( $entry['repeat_count'] ) )
                        $repeatCount = $entry['repeat_count'];

                    $entry['start'] = ['memory' => memory_get_usage(), 'time' => microtime()];
                    for ( $i = 0; $i < $repeatCount; ++$i )
                    {
                        $function( $this, $entry['parameter'] );
                    }
                    $entry['end'] = ['memory' => memory_get_usage(), 'time' => microtime()];

                    $this->processRecording( $benchmark, $entry, $repeatCount );
                }
                else
                {
                    $this->addToCurrentResult( $entry,
                                               "Function $function does not exist" );
                }
            } break;
        }
    }

    function processRecording( &$benchmark, &$entry, $repeatCount )
    {
        $memoryDiff = $entry['end']['memory'] - $entry['start']['memory'];
        $startTime = explode( " ", (string) $entry['start']['time'] );
        preg_match( "@0\.([0-9]+)@", "" . $startTime[0], $t1 );
        $startTime = $startTime[1] . "." . $t1[1];
        $endTime = explode( " ", (string) $entry['end']['time'] );
        preg_match( "@0\.([0-9]+)@", "" . $endTime[0], $t1 );
        $endTime = $endTime[1] . "." . $t1[1];
        $timeDiff = $endTime - $startTime;

        $entry['result'] = ['memory' => $memoryDiff, 'time' => $timeDiff];
        $entry['normalized'] = ['time' => $timeDiff / $repeatCount];

        $memoryDiff = $entry['prime_end']['memory'] - $entry['prime_start']['memory'];
        $startTime = explode( " ", (string) $entry['prime_start']['time'] );
        preg_match( "@0\.([0-9]+)@", "" . $startTime[0], $t1 );
        $startTime = $startTime[1] . "." . $t1[1];
        $endTime = explode( " ", (string) $entry['prime_end']['time'] );
        preg_match( "@0\.([0-9]+)@", "" . $endTime[0], $t1 );
        $endTime = $endTime[1] . "." . $t1[1];
        $timeDiff = $endTime - $startTime;

        $entry['prime'] = ['memory' => $memoryDiff, 'time' => $timeDiff];

        $this->addToCurrentResult( $entry );
    }

    /*!
     \virtual
     \protected
     Called whenever a test is run, can be overriden to print out the test result immediately.
    */
    function display( $result, $repeatCount )
    {
    }

    /*!
     \return an array with all the results from the last run.
    */
    function resultList()
    {
        return $this->Results;
    }

    /*!
     \protected
      Adds a result for mark \a $markName with optional message \a $message.
    */
    function addToCurrentResult( $entry, $message = false )
    {
        $markName = $entry['name'];
        if ( !is_array( $this->CurrentResult ) )
        {
             $this->CurrentResult = ['name' => $markName, 'start' => false, 'end' => false, 'result' => false, 'normalized' => false, 'prime' => false, 'messages' => []];
        }
        $repeatCount = $this->DefaultRepeatCount;
        if ( isset( $entry['repeat_count'] ) )
            $repeatCount = $entry['repeat_count'];
        $this->CurrentResult['repeat_count'] = $repeatCount;

        if ( isset( $entry['start'] ) )
            $this->CurrentResult['start'] = $entry['start'];
        if ( isset( $entry['end'] ) )
            $this->CurrentResult['end'] = $entry['end'];
        if ( isset( $entry['result'] ) )
            $this->CurrentResult['result'] = $entry['result'];
        if ( isset( $entry['normalized'] ) )
            $this->CurrentResult['normalized'] = $entry['normalized'];
        if ( isset( $entry['prime'] ) )
            $this->CurrentResult['prime'] = $entry['prime'];

        if ( $message )
            $this->CurrentResult['messages'][] = ['text' => $message];
    }

    /*!
     \protected
     Adds the current result to the result list and resets the current result data.
    */
    function addCurrentResult()
    {
        if ( is_array( $this->CurrentResult ) )
            $this->Results[] = $this->CurrentResult;
        return $this->CurrentResult;
    }

    /*!
     \protected
     Resets the current result data.
    */
    function resetCurrentResult()
    {
        $this->CurrentResult = ['name' => $this->currentMarkName(), 'messages' => []];
    }

    /*!
     \return the name of the currently running mark or \c false if no mark.
    */
    function currentMarkName()
    {
        return $this->CurrentMarkName;
    }

    /*!
     \protected
     Sets the name of the currently running mark to \a $name.
    */
    function setCurrentMarkName( $name )
    {
        $this->CurrentMarkName = $name;
    }

    /// \privatesection
    /// An array with test results.
    public $Results = [];
    /// The current result
    public $CurrentResult = false;
    /// The name of the currently running mark or \c false
    public $CurrentMarkName;
}

?>
