<?php
/**
 * File containing the eZMarkHashing class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZMarkHashing ezmarkhashing.php
  \brief The class eZMarkHashing does

*/

class eZMarkHashing extends eZBenchmarkCase
{
    public function __construct( $name = false )
    {
        parent::__construct( $name );
        $this->addMark( 'markMD5', 'MD5 hash', ['repeat_count' => 1000] );
        $this->addMark( 'markCRC32', 'CRC32 hash', ['repeat_count' => 1000] );
    }

    function prime( &$tr )
    {
        $this->Text = implode( '_', ['240', 'test', 'some_key', 'more'] );
    }

    function markMD5( &$tr )
    {
        md5( (string) $this->Text );
    }

    function markCRC32( &$tr )
    {
        eZSys::ezcrc32( $this->Text );
    }

    public $Text;
}

?>
