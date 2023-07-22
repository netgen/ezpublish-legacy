<?php
/**
 * File containing the eZWorkflowFunctionCollection class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZWorkflowFunctionCollection ezworkflowfunctioncollection.php
  \brief The class eZWorkflowFunctionCollection does

*/

class eZWorkflowFunctionCollection
{
    function fetchWorkflowStatuses()
    {
        return ['result' => eZWorkflow::statusNameMap()];
    }

    function fetchWorkflowTypeStatuses()
    {
        return ['result' => eZWorkflowType::statusNameMap()];
    }

}

?>
