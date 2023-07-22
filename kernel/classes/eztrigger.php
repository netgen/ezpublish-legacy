<?php
/**
 * File containing the eZTrigger class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZTrigger eztrigger.php
  \brief The class eZTrigger does

*/
class eZTrigger extends eZPersistentObject
{
    final public const STATUS_CRON_JOB = 0;
    final public const WORKFLOW_DONE = 1;
    final public const WORKFLOW_CANCELLED = 2;
    final public const NO_CONNECTED_WORKFLOWS = 3;
    final public const FETCH_TEMPLATE = 4;
    final public const REDIRECT = 5;
    final public const WORKFLOW_RESET = 6;
    final public const FETCH_TEMPLATE_REPEAT = 7;

    static function definition()
    {
        return ["fields" => ['id' => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'module_name' => ['name' => 'ModuleName', 'datatype' => 'string', 'default' => '', 'required' => true], 'function_name' => ['name' => 'FunctionName', 'datatype' => 'string', 'default' => '', 'required' => true], 'connect_type' => ['name' => 'ConnectType', 'datatype' => 'string', 'default' => '', 'required' => true], 'workflow_id' => ['name' => 'WorkflowID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZWorkflow', 'foreign_attribute' => 'id', 'multiplicity' => '1..*'], 'name' => ['name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true]], "class_name" => "eZTrigger", "keys" => ['id'], 'function_attributes' => ['allowed_workflows' => 'fetchAllowedWorkflows'], "increment_key" => "id", "name" => "eztrigger"];
    }

    /*!
     Get array containing allowed workflows for this trigger.

     \return array containing allowed workflows
    */
    function fetchAllowedWorkflows()
    {
        $connectionType = '*';
        if ( $this->attribute( 'connect_type') == 'b' )
        {
            $connectionType = 'before';
        }
        else if ( $this->attribute( 'connect_type') == 'a' )
        {
            $connectionType = 'after';
        }

        return eZWorkflow::fetchLimited( $this->attribute( 'module_name' ),
                                         $this->attribute( 'function_name' ),
                                         $connectionType );
    }

    static function fetch( $triggerID )
    {
        return eZPersistentObject::fetchObject( eZTrigger::definition(),
                                                null,
                                                ['id' => $triggerID],
                                                true);
    }

    static function fetchList( $parameters = [], $asObject = true )
    {
        $filterArray = [];
        if ( array_key_exists('module', $parameters ) && $parameters[ 'module' ] != '*' )
        {
            $filterArray['module_name'] = $parameters['module'];
        }
        if ( array_key_exists('function', $parameters ) && $parameters[ 'function' ] != '*' )
        {
            $filterArray['function_name'] = $parameters['function'];
        }
        if ( array_key_exists('connectType', $parameters ) && $parameters[ 'connectType' ] != '*' )
        {
            $filterArray['connect_type'] = $parameters['connectType'];
        }
        if ( array_key_exists('name', $parameters ) && $parameters[ 'name' ] != '' )
        {
            $filterArray['name'] = $parameters['name'];
        }
        return eZPersistentObject::fetchObjectList( eZTrigger::definition(),
                                                    null,
                                                    $filterArray, ['module_name' => 'asc', 'function_name' => 'asc', 'connect_type' => 'asc'],
                                                    null,
                                                    $asObject );
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function runTrigger( $name, $moduleName, $function, $parameters, $keys = null )
    {
        $trigger = eZPersistentObject::fetchObject( eZTrigger::definition(),
                                                    null,
                                                    ['name' => $name, 'module_name' => $moduleName, 'function_name' => $function],
                                                    true );
        if ( $trigger !== NULL )
        {
            $workflowID = $trigger->attribute( 'workflow_id' );
            $workflow = eZWorkflow::fetch( $workflowID );
            if ( $keys != null )
            {
                $keys[] = 'workflow_id';
            }

            $parameters['workflow_id']     = $workflowID;
            $parameters['trigger_name']    = $name;
            $parameters['module_name']     = $moduleName;
            $parameters['module_function'] = $function;
            // It is very important that the user_id is set correctly.
            // If it was not supplied by the calling code we will use
            // the currently logged in user.
            if ( !isset( $parameters['user_id'] ) or
                 $parameters['user_id'] == 0 )
            {
                $user = eZUser::currentUser();
                $parameters['user_id'] = $user->attribute( 'contentobject_id' );
            }
            $processKey = eZWorkflowProcess::createKey( $parameters, $keys );

//            $searchKey = eZWorkflowProcess::createKey( $keyArray );

            $workflowProcessList = eZWorkflowProcess::fetchListByKey( $processKey );

            if ( (is_countable($workflowProcessList) ? count( $workflowProcessList ) : 0) > 0 )
            {
                $existingWorkflowProcess = $workflowProcessList[0];
                $existingWorkflowStatus = $existingWorkflowProcess->attribute( 'status' );


                switch( $existingWorkflowStatus )
                {
                    case eZWorkflow::STATUS_FAILED:
                    case eZWorkflow::STATUS_CANCELLED:
                    case eZWorkflow::STATUS_NONE:
                    case eZWorkflow::STATUS_BUSY:
                    {
                        $existingWorkflowProcess->removeThis();
                        return ['Status' => eZTrigger::WORKFLOW_CANCELLED, 'Result' => null];
                    } break;
                    case eZWorkflow::STATUS_FETCH_TEMPLATE:
                    case eZWorkflow::STATUS_FETCH_TEMPLATE_REPEAT:
                    case eZWorkflow::STATUS_REDIRECT:
                    case eZWorkflow::STATUS_RESET:
                    {
                        return eZTrigger::runWorkflow( $existingWorkflowProcess );
//                        return eZTrigger::FETCH_TEMPLATE;
                    } break;
                    case eZWorkflow::STATUS_DEFERRED_TO_CRON:
                    {
                        return eZTrigger::runWorkflow( $existingWorkflowProcess );
/*                        return array( 'Status' => eZTrigger::STATUS_CRON_JOB,

                                      'Result' => array( 'content' => 'Operation halted during execution.<br/>Refresh page to continue<br/><br/><b>Note: The halt is just a temporary test</b><br/>',
                                                         'path' => array( array( 'text' => 'Operation halt',
                                                                            'url' => false ) ) ) );
*/                  } break;
                    case eZWorkflow::STATUS_DONE:
                    {
                        $existingWorkflowProcess->removeThis();
                        return ['Status' => eZTrigger::WORKFLOW_DONE, 'Result' => null];
                    }
                }
                return ['Status' => eZTrigger::WORKFLOW_CANCELLED, 'Result' => null];
            }else
            {
//                print( "\n starting new workflow process \n");
//                var_dump( $keyArray );
//                print( " $workflowID, $userID, $objectID, $version, $nodeID, \n ");
            }
            $workflowProcess = eZWorkflowProcess::create( $processKey, $parameters );

            $workflowProcess->store();

            return eZTrigger::runWorkflow( $workflowProcess );

        }
        else
        {
            return ['Status' => eZTrigger::NO_CONNECTED_WORKFLOWS, 'Result' => null];
        }
    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
     */
    static function runWorkflow( $workflowProcess )
    {
        $eventLog = null;
        $workflow = eZWorkflow::fetch( $workflowProcess->attribute( "workflow_id" ) );
        $workflowEvent = null;

        $workflowStatus = $workflowProcess->run( $workflow, $workflowEvent, $eventLog );

        $db = eZDB::instance();
        $db->begin();
        $workflowProcess->store();

        switch ( $workflowStatus )
        {
            case eZWorkflow::STATUS_FAILED:
            case eZWorkflow::STATUS_CANCELLED:
            case eZWorkflow::STATUS_NONE:
            case eZWorkflow::STATUS_BUSY:
            {
                $workflowProcess->removeThis();
                $db->commit();
                return ['Status' => eZTrigger::WORKFLOW_CANCELLED, 'Result' => null];
            } break;
            case eZWorkflow::STATUS_FETCH_TEMPLATE:
            case eZWorkflow::STATUS_FETCH_TEMPLATE_REPEAT:
            {
                $tpl = eZTemplate::factory();
                $result = [];
                foreach ( array_keys( $workflowProcess->Template['templateVars'] ) as $key )
                {
                    $value = $workflowProcess->Template['templateVars'][$key];
                    $tpl->setVariable( $key, $value );
                }
                $result['content'] = $tpl->fetch( $workflowProcess->Template['templateName'] );
                if ( isset( $workflowProcess->Template['path'] ) )
                    $result['path'] = $workflowProcess->Template['path'];

                    $db->commit();
                if ( $workflowStatus == eZWorkflow::STATUS_FETCH_TEMPLATE )
                {
                    $triggerStatus = eZTrigger::FETCH_TEMPLATE;
                }
                elseif ( $workflowStatus == eZWorkflow::STATUS_FETCH_TEMPLATE_REPEAT )
                {
                    $triggerStatus = eZTrigger::FETCH_TEMPLATE_REPEAT;
                }
                return ['Status' => $triggerStatus, 'WorkflowProcess' => $workflowProcess, 'Result' => $result];
            } break;
            case eZWorkflow::STATUS_REDIRECT:
            {
//                var_dump( $workflowProcess->RedirectUrl  );
                $db->commit();
                return ['Status' => eZTrigger::REDIRECT, 'WorkflowProcess' => $workflowProcess, 'Result' => $workflowProcess->RedirectUrl];

            } break;
            case eZWorkflow::STATUS_DEFERRED_TO_CRON:
            {

                $db->commit();
                return ['Status' => eZTrigger::STATUS_CRON_JOB, 'WorkflowProcess' => $workflowProcess, 'Result' => ['content' => 'Deffered to cron. Operation halted during execution. <br/>Refresh page to continue<br/><br/><b>Note: The halt is just a temporary test</b><br/>', 'path' => [['text' => 'Operation halt', 'url' => false]]]];
/*
                return array( 'Status' => eZTrigger::STATUS_CRON_JOB,
                              'Result' => $workflowProcess->attribute( 'id') );
*/
            } break;
            case eZWorkflow::STATUS_RESET:
            {
                $db->commit();
                return ['Status' => eZTrigger::WORKFLOW_RESET, 'WorkflowProcess' => $workflowProcess, 'Result' => ['content' => 'Workflow was reset', 'path' => [['text' => 'Operation halt', 'url' => false]]]];
            } break;
            case eZWorkflow::STATUS_DONE:
            {
                $workflowProcess->removeThis();
                $db->commit();
                return ['Status' => eZTrigger::WORKFLOW_DONE, 'Result' => null];
            }
        }

        $db->commit();
        return ['Status' => eZTrigger::WORKFLOW_CANCELLED, 'Result' => null];



    }

    /*!
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function createNew( $moduleName, $functionName, $connectType, $workflowID, $name = false )
    {
        if ( !$name )
        {
            if ( $connectType == 'b' )
            {
                $name = 'pre_';
            }
            else if ( $connectType == 'a' )
            {
                $name = 'post_';
            }
            $name .= $functionName;
        }
        $trigger = new eZTrigger( ['module_name' => $moduleName, 'function_name' => $functionName, 'connect_type' => $connectType, 'workflow_id' => $workflowID, 'name' => $name] );
        $trigger->store();
        return $trigger;
    }

    /*!
     Removes triggers which uses the given workflowID.
     \note Transaction unsafe. If you call several transaction unsafe methods you must enclose
     the calls within a db transaction; thus within db->begin and db->commit.
    */
    static function removeTriggerForWorkflow( $workFlowID )
    {
        $db = eZDB::instance();
        $workFlowID = (int)$workFlowID;
        $db->query( "DELETE FROM eztrigger WHERE workflow_id=$workFlowID" );
    }
}

?>
