<?php
/**
 * File containing the eZCollaborationSimpleMessage class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZCollaborationSimpleMessage ezcollaborationsimplemessage.php
  \brief The class eZCollaborationSimpleMessage does

*/

class eZCollaborationSimpleMessage extends eZPersistentObject
{
    static function definition()
    {
        return ['fields' => ['id' => ['name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'message_type' => ['name' => 'MessageType', 'datatype' => 'string', 'default' => '', 'required' => true], 'data_text1' => ['name' => 'DataText1', 'datatype' => 'text', 'default' => '', 'required' => true], 'data_text2' => ['name' => 'DataText2', 'datatype' => 'text', 'default' => '', 'required' => true], 'data_text3' => ['name' => 'DataText3', 'datatype' => 'text', 'default' => '', 'required' => true], 'data_int1' => ['name' => 'DataInt1', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'data_int2' => ['name' => 'DataInt2', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'data_int3' => ['name' => 'DataInt3', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'data_float1' => ['name' => 'DataFloat1', 'datatype' => 'float', 'default' => 0, 'required' => true], 'data_float2' => ['name' => 'DataFloat2', 'datatype' => 'float', 'default' => 0, 'required' => true], 'data_float3' => ['name' => 'DataFloat3', 'datatype' => 'float', 'default' => 0, 'required' => true], 'creator_id' => ['name' => 'CreatorID', 'datatype' => 'integer', 'default' => 0, 'required' => true, 'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id', 'multiplicity' => '1..*'], 'created' => ['name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true], 'modified' => ['name' => 'Modified', 'datatype' => 'integer', 'default' => 0, 'required' => true]], 'keys' => ['id'], 'function_attributes' => ['participant' => 'participant'], 'increment_key' => 'id', 'class_name' => 'eZCollaborationSimpleMessage', 'name' => 'ezcollab_simple_message'];
    }

    static function create( $type, $text = false, $creatorID = false )
    {
        $date_time = time();
        if ( $creatorID === false )
        {
            $user = eZUser::currentUser();
            $creatorID = $user->attribute( 'contentobject_id' );
        }
        return new eZCollaborationSimpleMessage( ['message_type' => $type, 'data_text1' => $text, 'creator_id' => $creatorID, 'created' => $date_time, 'modified' => $date_time] );
    }

    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( eZCollaborationSimpleMessage::definition(),
                                                null,
                                                ["id" => $id],
                                                $asObject );
    }

    function participant()
    {
        // TODO: Get participant trough participant link from item
        return null;
    }

    /// \privatesection
    public $ID;
    public $ParticipantID;
    public $Created;
    public $Modified;
    public $DataText1;
    public $DataText2;
    public $DataText3;
    public $DataInt1;
    public $DataInt2;
    public $DataInt3;
    public $DataFloat1;
    public $DataFloat2;
    public $DataFloat3;
}

?>
