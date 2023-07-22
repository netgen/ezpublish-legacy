<?php

class eZFlowMCFetch implements eZFlowFetchInterface
{
    public function fetch( $parameters, $publishedAfter, $publishedBeforeOrAt )
    {
        if ( isset( $parameters['Source'] ) )
        {
            $nodeID = $parameters['Source'];
            $node = eZContentObjectTreeNode::fetch( $nodeID, false, false ); // not as an object
            if ( $node && $node['modified_subnode'] <= $publishedAfter )
            {
                return [];
            }
        }
        else
        {
            $nodeID = 0;
        }

        $subTreeParameters = [];
        $subTreeParameters['AsObject'] = false;
        $subTreeParameters['SortBy'] = ['published', false]; // first the latest
        $subTreeParameters['AttributeFilter'] = ['and', ['published', '>', $publishedAfter], ['published', '<=', $publishedBeforeOrAt]];

        if ( isset( $parameters['Classes'] ) )
        {
            $subTreeParameters['ClassFilterType'] = 'include';
            $subTreeParameters['ClassFilterArray'] = explode( ',', (string) $parameters['Classes'] );
        }
        
        // Do not fetch hidden nodes even when ShowHiddenNodes=true
        $subTreeParameters['AttributeFilter'] = ['and', ['visibility', '=', true]];

        $nodes = eZContentObjectTreeNode::subTreeByNodeID( $subTreeParameters, $nodeID );
        
        if ( $nodes === null )
            return [];
        
        $fetchResult = [];
        foreach( $nodes as $node )
        {
            $fetchResult[] = ['object_id' => $node['id'], 'node_id' => $node['node_id'], 'ts_publication' => $node['published']];
        }

        return $fetchResult;
    }
}

?>
