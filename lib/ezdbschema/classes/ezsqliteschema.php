<?php

class eZSQLiteSchema extends eZDBSchemaInterface
{
    function __construct( $params )
    {
        parent::__construct( $params );
    }

    /*!
     \reimp
    */
    function schema( $params = array() )
    {
        $params = array_merge( array( 'meta_data' => false,
                                      'format' => 'generic' ),
                               $params );
        $schema = array();

        if ( $this->Schema === false )
        {
            $tableArray = $this->DBInstance->arrayQuery( "SHOW TABLES" );

            foreach( $tableArray as $tableNameArray )
            {
                $table_name = current( $tableNameArray );
                if ( !isset( $params['table_include'] ) or
                     ( is_array( $params['table_include'] ) and
                       in_array( $table_name, $params['table_include'] ) ) )
                {
                    $schema_table['name'] = $table_name;
                    $schema_table['fields'] = $this->fetchTableFields( $table_name, $params );
                    $schema_table['indexes'] = $this->fetchTableIndexes( $table_name, $params );

                    $schema[$table_name] = $schema_table;
                }
            }
            $this->transformSchema( $schema, $params['format'] == 'local' );
            ksort( $schema );
            $this->Schema = $schema;
        }
        else
        {
            $this->transformSchema( $this->Schema, $params['format'] == 'local' );
            $schema = $this->Schema;
        }
        return $schema;
    }

    /*!
     \private

     \param table name
     */
    private function fetchTableFields( $table, $params )
    {
        $fields = array();

        $resultArray = $this->DBInstance->arrayQuery( "DESCRIBE $table" );

        foreach( $resultArray as $row )
        {
            $field = array();
            $field['type'] = $this->parseType ( $row['Type'], $field['length'] );
            if ( !$field['length'] )
            {
                unset( $field['length'] );
            }
            $field['not_null'] = 0;
            if ( $row['Null'] != 'YES' )
            {
                $field['not_null'] = '1';
            }
            $field['default'] = false;
            if ( !$field['not_null'] )
            {
                if ( $row['Default'] === null )
                    $field['default'] = null;
                else
                    $field['default'] = (string)$row['Default'];
            }
            else
            {
                $field['default'] = (string)$row['Default'];
            }

            $numericTypes = array( 'float', 'int' );
            $blobTypes = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
            $charTypes = array( 'varchar', 'char' );
            if ( in_array( $field['type'], $charTypes ) )
            {
                if ( !$field['not_null'] )
                {
                    if ( $field['default'] === null )
                    {
                        $field['default'] = null;
                    }
                    else if ( $field['default'] === false )
                    {
                        $field['default'] = '';
                    }
                }
            }
            else if ( in_array( $field['type'], $numericTypes ) )
            {
                if ( $field['default'] === false )
                {
                    if ( $field['not_null'] )
                    {
                        $field['default'] = 0;
                    }
                }
                else if ( $field['type'] == 'int' )
                {
                    if ( $field['not_null'] or
                         is_numeric( $field['default'] ) )
                        $field['default'] = (int)$field['default'];
                }
                else if ( $field['type'] == 'float' or
                          is_numeric( $field['default'] ) )
                {
                    if ( $field['not_null'] or
                         is_numeric( $field['default'] ) )
                        $field['default'] = (float)$field['default'];
                }
            }
            else if ( in_array( $field['type'], $blobTypes ) )
            {
                // We do not want default for blobs.
                $field['default'] = false;
            }

            if ( substr ( $row['Extra'], 'auto_increment' ) !== false )
            {
                unset( $field['length'] );
                $field['not_null'] = 0;
                $field['default'] = false;
                $field['type'] = 'auto_increment';
            }

            if ( !$field['not_null'] )
                unset( $field['not_null'] );

            $fields[$row['Field']] = $field;
        }
        ksort( $fields );

        return $fields;
    }

    /*!
     * \private
     */
    private function fetchTableIndexes( $table, $params )
    {
        $metaData = false;
        if ( isset( $params['meta_data'] ) )
        {
            $metaData = $params['meta_data'];
        }

        $indexes = array();

        $resultArray = $this->DBInstance->arrayQuery( "SHOW INDEX FROM $table" );

        foreach( $resultArray as $row )
        {
            $kn = $row['Key_name'];

            if ( $kn == 'PRIMARY' )
            {
                $indexes[$kn]['type'] = 'primary';
            }
            else
            {
                $indexes[$kn]['type'] = $row['Non_unique'] ? 'non-unique' : 'unique';
            }

            $indexFieldDef = array( 'name' => $row['Column_name'] );

            // Include length if one is defined
            if ( $row['Sub_part'] )
            {
                $indexFieldDef['mysql:length'] = (int)$row['Sub_part'];
            }

            // Check if we have any entries other than 'name', if not we skip the array definition
            if ( count( array_diff( array_keys( $indexFieldDef ), array( 'name' ) ) ) == 0 )
            {
                $indexFieldDef = $indexFieldDef['name'];
            }
            $indexes[$kn]['fields'][$row['Seq_in_index'] - 1] = $indexFieldDef;
        }
        ksort( $indexes );

        return $indexes;
    }

    function parseType( $type_info, &$length_info )
    {
        preg_match ( "@([a-z ]*)(\(([0-9]*|[0-9]*,[0-9]*)\))?@", $type_info, $matches );
        if ( isset( $matches[3] ) )
        {
            $length_info = $matches[3];
            if ( is_numeric( $length_info ) )
                $length_info = (int)$length_info;
        }
        return $matches[1];
    }

    /*!
     * \private
     */
    function generateAddIndexSql( $table_name, $index_name, $def, $params, $isEmbedded = false )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;
        $sql = '';

        // Will be set to true when primary key is inside CREATE TABLE
        if ( !$isEmbedded )
        {
            $sql .= "CREATE ";
            $sql .= " ";
        }

        switch ( $def['type'] )
        {
            case 'primary':
            {
                $sql .= 'PRIMARY KEY';
            } break;

            case 'non-unique':
            {
                $sql .= "INDEX $index_name";
            } break;

            case 'unique':
            {
                $sql .= "UNIQUE INDEX $index_name";
            } break;
        }

        if ( !$isEmbedded )
        {
            $sql .= " ON $table_name ";
        }

        $sql .= $diffFriendly ? " (\n    " : " ( " ;
        $fields = $def['fields'];
        $i = 0;
        foreach ( $fields as $fieldDef )
        {
            if ( $i > 0 )
            {
                $sql .= $diffFriendly ? ",\n    " : ', ';
            }
            if ( is_array( $fieldDef ) )
            {
                $sql .= $fieldDef['name'];
                if ( isset( $fieldDef['sqlite:length'] ) )
                {
                    if ( $diffFriendly )
                    {
                        $sql .= "(\n";
                        $sql .= "    " . str_repeat( ' ', strlen( $fieldDef['name'] ) );
                    }
                    else
                    {
                        $sql .= "( ";
                    }
                    $sql .= $fieldDef['sqlite:length'];
                    if ( $diffFriendly )
                    {
                        $sql .= ")";
                    }
                    else
                    {
                        $sql .= " )";
                    }
                }
            }
            else
            {
                $sql .= $fieldDef;
            }
            ++$i;
        }
        $sql .= $diffFriendly ? "\n)" : " )";

        if ( !$isEmbedded )
        {
            return $sql . ";\n";
        }
        return $sql;
    }

    /*!
     * \private
     */
    function generateFieldDef( $field_name, $def, &$skip_primary, $params = null )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;

        $sql_def = $field_name . ' ';
        $defaultText = "DEFAULT";

        if ( $def['type'] != 'auto_increment' )
        {
            $defList = array();
            $type = $def['type'];
            if ( $type === 'int' )
            {
                $type = 'INTEGER';
            }
            if ( isset( $def['length'] ) )
            {
                $type .= "({$def['length']})";
            }
            $defList[] = $type;
            if ( $type !== 'int' && isset( $def['not_null'] ) && ( $def['not_null'] ) )
            {
                $defList[] = 'NOT NULL';
            }
            if ( array_key_exists( 'default', $def ) )
            {
                if ( $def['default'] === null )
                {
                    $defList[] = "$defaultText NULL";
                }
                else if ( $def['default'] !== false )
                {
                    $defList[] = "$defaultText '{$def['default']}'";
                }
            }
            else if ( $def['type'] == 'varchar' )
            {
                $defList[] = "$defaultText ''";
            }
            $sql_def .= join( $diffFriendly ? "\n    " : " ", $defList );
            $skip_primary = false;
        }
        else
        {
            $incrementText = ""; /*"PRIMARY KEY"*/
            if ( $diffFriendly )
            {
                $sql_def .= "INTEGER\n    $incrementText";
            }
            else
            {
                $sql_def .= "INTEGER $incrementText";
            }
            $skip_primary = true;
        }
        return $sql_def;
    }

    /*!
     \reimp
     \note Calls generateTableSQL() with \a $asArray set to \c false
    */
    function generateTableSchema( $tableName, $table, $params )
    {
        return $this->generateTableSQL( $tableName, $table, $params, false, false );
    }

    /*!
     \reimp
     \note Calls generateTableSQL() with \a $asArray set to \c true
    */
    function generateTableSQLList( $tableName, $table, $params, $separateTypes )
    {
        return $this->generateTableSQL( $tableName, $table, $params, true, $separateTypes );
    }

    /*!
     \private

     \param $asArray If \c true all SQLs are return in an array,
                     if not they are returned as a string.
     \note When returned as array the SQLs will not have a semi-colon to end the statement
    */
    function generateTableSQL( $tableName, $tableDef, $params, $asArray, $separateTypes = false )
    {
        $diffFriendly = isset( $params['diff_friendly'] ) ? $params['diff_friendly'] : false;
        $mysqlCompatible = isset( $params['compatible_sql'] ) ? $params['compatible_sql'] : false;

        if ( $asArray )
        {
            if ( $separateTypes )
            {
                $sqlList = array( 'tables' => array() );
            }
            else
            {
                $sqlList = array();
            }
        }

        $sql = '';
        $skip_pk = false;
        $sql_fields = array();
        $sql .= "CREATE TABLE $tableName (\n";

        $fields = $tableDef['fields'];

        foreach ( $fields as $field_name => $field_def )
        {
            $sql_fields[] = '  ' . self::generateFieldDef( $field_name, $field_def, $skip_pk_flag, $params );
            if ( $skip_pk_flag )
            {
                $skip_pk = true;
            }
        }

        // Make sure the order is as defined by 'offset'
        $indexes = $tableDef['indexes'];

        // We need to add all keys in table definition
        foreach ( $indexes as $index_name => $index_def )
        {
            if ( $index_def['type'] == 'primary' )
            {
                $sql_fields[] = ( $diffFriendly ? '' : '  ' ) . self::generateAddIndexSql( $tableName, $index_name, $index_def, $params, true );
            }
        }
        $sql .= join( ",\n", $sql_fields );
        $sql .= "\n)";

        // Add some extra table options if they are required
        $extraOptions = array();
        if ( isset( $params['table_type'] ) and $params['table_type'] )
        {
            $typeName = $this->tableStorageTypeName( $params['table_type'] );
            if ( $typeName )
            {
                $extraOptions[] = "TYPE=" . $typeName;
            }
        }

        if ( isset( $tableDef['options'] ) )
        {
            foreach( $tableDef['options'] as $optionType => $optionValue )
            {
                $optionText = $this->generateTableOption( $tableName, $tableDef, $optionType, $optionValue, $params );
                if ( $optionText )
                    $extraOptions[] = $optionText;
            }
        }

        if ( count( $extraOptions ) > 0 )
        {
            $sql .= " " . implode( $diffFriendly ? "\n" : " ", $extraOptions );
        }

        if ( $asArray )
        {
            if ( $separateTypes )
            {
                $sqlList['tables'][] = $sql . ";";
            }
            else
            {
                $sqlList[] = $sql . ";";
            }

            foreach ( $indexes as $index_name => $index_def )
            {
                if ( $index_def['type'] != 'primary' )
                {
                    $sqlList[] = ( $diffFriendly ? '' : '  ' ) . self::generateAddIndexSql( $tableName, $index_name, $index_def, $params, false );
                }
            }
        }
        else
        {
            $sql .= ";\n";

            if ( $mysqlCompatible )
            {
                $sql .= "\n\n\n";
            }

            foreach ( $indexes as $index_name => $index_def )
            {
                if ( $index_def['type'] != 'primary' )
                {
                    $sql .= ( $diffFriendly ? '' : '  ' ) . self::generateAddIndexSql( $tableName, $index_name, $index_def, $params, false ) . "\n\n\n";
                }
            }
        }

        return $asArray ? $sqlList : $sql;
    }

    /*!
     * \private
     */
    function generateDropTable( $table, $params )
    {
        return "DROP TABLE $table;\n";
    }

    /*!
     \reimp
    */
    function isMultiInsertSupported()
    {
        return false;
    }

    /*!
     \reimp
    */
    function escapeSQLString( $value )
    {
        if ( $this->DBInstance instanceof eZDBInterface )
        {
            return $this->DBInstance->escapeString( $value );
        }

        return $value;
    }

    /*!
     \reimp
    */
    function schemaType()
    {
        return 'sqlite';
    }

    /*!
     \reimp
    */
    function schemaName()
    {
        return 'SQLite';
    }
}

?>