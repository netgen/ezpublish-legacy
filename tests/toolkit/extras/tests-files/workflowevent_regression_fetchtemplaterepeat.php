<?php
/**
 * File containing the WorkflowEventRegressionFetchTemplateRepeatType class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class WorkflowEventRegressionFetchTemplateRepeatType extends eZWorkflowEventType
{
    final public const WORKFLOW_TYPE_STRING = 'fetchtemplaterepeat';
    function __construct()
    {
        parent::__construct( WorkflowEventRegressionFetchTemplateRepeatType::WORKFLOW_TYPE_STRING, "WorkflowEventRegressionFetchTemplateRepeatType test" );
        $this->setTriggerTypes( ['content' => ['publish' => ['before']]] );
    }

    function execute( $process, $event )
    {
        if ( !isset( $_POST['CompletePublishing'] ) )
        {
            $index = eZSys::indexFile( true );
            $requestUri = eZSys::indexFile( false ) . eZSys::requestUri();
            $replace = "@" . preg_quote( $index ) . "@i";
            $requestUri = preg_replace( [$replace], [''], $requestUri, 1 );

            $process->Template = ['templateName' => 'file:' . __DIR__ . basename( __FILE__, '.php' ) .'.tpl', 'templateVars' => ['uri' => $requestUri], 'path' => [['url' => false, 'text' => 'Workflow event regression: fetch template repeat']]];
            return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;
        }
        else
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }
    }
}

eZWorkflowEventType::registerEventType(
    WorkflowEventRegressionFetchTemplateRepeatType::WORKFLOW_TYPE_STRING, "WorkflowEventRegressionFetchTemplateRepeatType" );
?>
