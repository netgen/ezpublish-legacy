<?php
/**
 * File containing the eZContentObjectTreeNodeRegression class
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class eZContentObjectTreeNodeRegression extends ezpDatabaseTestCase
{
    protected $backupGlobals = false;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "eZContentObjectTreeNode Regression Tests" );
    }

    /**
     * Test for regression #13497:
     * attribute operator throws a PHP fatal error on a node without parent in a displayable language
     *
     * Situation:
     *  - siteaccess with one language (fre-FR) and ShowUntranslatedObjects disabled
     *  - parent content node in another language (eng-GB) with always available disabled
     *  - content node in the siteaccess' language (fre-FR)
     *  - fetch this fre-FR node from anywhere, and call attribute() on it
     *
     * Result:
     *  - Fatal error: Call to a member function attribute() on a non-object in
     *    kernel/classes/ezcontentobjecttreenode.php on line 4225
     *
     * Explanation: the error actually comes from the can_remove_location attribute
     */
    public function testIssue13497()
    {
        $bkpLanguages = eZContentLanguage::prioritizedLanguageCodes();

        // Create a folder in english only
        $folder = new ezpObject( "folder", 2, 14, 1, 'eng-GB' );
        $folder->setAlwaysAvailableLanguageID( false );
        $folder->name = "Parent for " . __FUNCTION__;
        $folder->publish();

        $locale = eZLocale::instance( 'fre-FR' );
        $translation = eZContentLanguage::addLanguage( $locale->localeCode(), $locale->internationalLanguageName() );

        // Create an article in french only, as a subitem of the previously created folder
        $article = new ezpObject( "article", $folder->attribute( 'main_node_id' ), 14, 1, 'fre-FR' );
        $article->title = "Object for " . __FUNCTION__;
        $article->short_description = "Description of test for " . __FUNCTION__;
        $article->publish();

        $articleNodeID = $article->attribute( 'main_node_id' );

        // INi changes: set language to french only, untranslatedobjects disabled
        ezpINIHelper::setINISetting( 'site.ini', 'RegionalSettings', 'ContentObjectLocale', 'fre-FR' );
        // ezpINIHelper::setINISetting( 'site.ini', 'RegionalSettings', 'SiteLanguageList', array( 'fre-FR' ) );
        eZContentLanguage::setPrioritizedLanguages( ['fre-FR'] );
        ezpINIHelper::setINISetting( 'site.ini', 'RegionalSettings', 'ShowUntranslatedObjects', 'disabled' );
        eZContentLanguage::expireCache();

        // This should crash
        eZContentObjectTreeNode::fetch( $articleNodeID )->attribute( 'can_remove_location' );

        ezpINIHelper::restoreINISettings();

        // re-expire cache for further tests
        eZContentLanguage::setPrioritizedLanguages( $bkpLanguages );
        $article->remove();
        $translation->removeThis();
        eZContentLanguage::expireCache();

        ezpINIHelper::restoreINISettings();
    }

    /**
     * Test for regression #15211
     *
     * The issue was reported as happening when a fetch_alias was called without
     * a second parameter. It turns out that this was just wrong, but led to the
     * following: if eZContentObjectTreeNode::createAttributeFilterSQLStrings
     * is called with the first parameter ($attributeFilter) is a string, a fatal
     * error "Cannot unset string offsets" is thrown.
     *
     * Test: Call this function with a string as the first parameter. Without the
     * fix, a fatal error occurs, while an empty filter is returned once fixed.
     */
    public function testIssue15211()
    {
        $attributeFilter = "somestring";

        // Without the fix, this is a fatal error
        $filterSQL = eZContentObjectTreeNode::createAttributeFilterSQLStrings(
            $attributeFilter );

        static::assertInternalType('array', $filterSQL);
        static::assertArrayHasKey('from', $filterSQL);
        static::assertArrayHasKey('where', $filterSQL);
    }

    /**
     * Test for issue #15062:
     * StateGroup Policy limitation SQL can break the 30 characters oracle
     * limitation for identifiers
     */
    public function testIssue15062()
    {
        $policyLimitationArray = [['StateGroup_abcdefghijkl' => [1], 'StateGroup_abcdefghiklmnop' => [1, 2, 3]], ['StateGroup_abcdefghijkl' => [2, 3]]];

        $sqlArray = eZContentObjectTreeNode::createPermissionCheckingSQL( $policyLimitationArray );

        static::assertInternalType('array', $sqlArray);
        static::assertArrayHasKey('from', $sqlArray);

        // we need to check that each identifier in the 'from' of this array
        // doesn't exceed 30 characters
        $matches = explode( ', ', str_replace( "\r\n", '', (string) $sqlArray['from'] ) );
        foreach( $matches as $match )
        {
            if ( $match == '' )
                continue;
            [$table, $alias] = explode( ' ', $match );
            static::assertTrue(strlen( $alias ) <= 30, "Identifier {$alias} exceeds the 30 characters limit");
        }
    }

    /**
     * Regression test for issue #15561:
     * eZContentObjectTreeNode::fetch() SQL error when conditions argument is given
     */
    public function testIssue15561()
    {
        // Create a new node so that we have a known object with a known ID
        $object = new ezpObject( 'article', 2 );
        $object->title = __FUNCTION__;
        $object->publish();
        $nodeID = $object->attribute( 'main_node_id' );

        $node = eZContentObjectTreeNode::fetch( $nodeID, false, true,
            ['contentobject_version' => 1] );

        static::assertInstanceOf('eZContentObjectTreeNode', $node);
    }


    /**
     * Regression test for issue #16737
     * 1) Test executing the sql and verify that it doesn't have database error.
     * 2) Test the sorting in class_name, class_name with contentobject_id
     * The test should pass in mysql, postgresql and oracle
     */
    public function testIssue16737()
    {


        //test generated result of createSortingSQLStrings
        $sortList = [['class_name', true]];
        $result = eZContentObjectTreeNode::createSortingSQLStrings( $sortList );
        static::assertEquals(', ezcontentclass_name.name as contentclass_name', strtolower( (string) $result['attributeTargetSQL'] ));
        static::assertEquals('contentclass_name asc', strtolower( (string) $result['sortingFields'] ));

        $sortListTwo = [['class_name', false], ['class_identifier', true]];
        $result = eZContentObjectTreeNode::createSortingSQLStrings( $sortListTwo );
        static::assertEquals(', ezcontentclass_name.name as contentclass_name', strtolower( (string) $result['attributeTargetSQL'] ));
        static::assertEquals('contentclass_name desc, ezcontentclass.identifier asc', strtolower( (string) $result['sortingFields'] ));

        //test trash node with classname
        $sortBy = [['class_name', true], ['contentobject_id', true]];
        $params = ['SortBy', $sortBy];
        $result = eZContentObjectTrashNode::trashList( $params );
        static::assertEquals([], $result);
        $result = eZContentObjectTrashNode::trashList( $params, true );
        static::assertEquals(0, $result);  //if there is an error, there will be fatal error message

        //test subtreenode with classname
        $parent = new ezpObject( 'folder', 1 );
        $parent->publish();
        $parentNodeID = $parent->mainNode->node_id;

        $article = new ezpObject( 'article', $parentNodeID );
        $article->publish();

        $link = new ezpObject( 'link', $parentNodeID );
        $link->publish();

        $folder = new ezpObject( 'folder', $parentNodeID );
        $folder->publish();

        $folder2 = new ezpObject( 'folder', $parentNodeID );
        $folder2->publish();

        $sortBy = [['class_name', false]];
        $params = ['SortBy' => $sortBy];
        $result = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeID );
        static::assertEquals($article->mainNode->node_id, $result[count( (array) $result )-1]->attribute( 'node_id' ));

        $sortBy = [['class_name', false], ['contentobject_id', false]];
        $params = ['SortBy' => $sortBy];
        $result = eZContentObjectTreeNode::subTreeByNodeID( $params, $parentNodeID );
        static::assertEquals($folder2->mainNode->node_id, $result[1]->attribute( 'node_id' ));
    }

    /**
     * Regression test for issue #16949
     * 1) Test there is no pending object in sub objects
     * 2) Test there is one pending object in sub objects
     */
    public function testIssue16949()
    {
        //create object
        $top = new ezpObject( 'article', 2 );
        $top->title = 'TOP ARTICLE';
        $top->publish();
        $child = new ezpObject( 'article', $top->mainNode->node_id );
        $child->title = 'THIS IS AN ARTICLE';
        $child->publish();

        $adminUser = eZUser::fetchByName( 'admin' );
        $adminUserID = $adminUser->attribute( 'contentobject_id' );
        $currentUser = eZUser::currentUser();
        $currentUserID = eZUser::currentUserID();
        eZUser::setCurrentlyLoggedInUser( $adminUser, $adminUserID );

        $result = eZContentObjectTreeNode::subtreeRemovalInformation( [$top->mainNode->node_id] );
        static::assertFalse($result['has_pending_object']);
        $workflowArticle = new ezpObject( 'article', $top->mainNode->node_id );
        $workflowArticle->title = 'THIS IS AN ARTICLE WITH WORKFLOW';
        $workflowArticle->publish();
        $version = $workflowArticle->currentVersion();
        $version->setAttribute( 'status', eZContentObjectVersion::STATUS_PENDING );
        $version->store();
        $result = eZContentObjectTreeNode::subtreeRemovalInformation( [$top->mainNode->node_id] );
        static::assertTrue($result['has_pending_object']);

        eZUser::setCurrentlyLoggedInUser( $currentUser, $currentUserID );
    }

    /**
     * Regression test for issue {@see #17632 http://issues.ez.no/17632}
     *
     * In a multi language environment, a node fetched with a language other than the prioritized one(s) will return the
     * URL alias in the prioritized language
     */
    public function testIssue17632()
    {
        $bkpLanguages = eZContentLanguage::prioritizedLanguageCodes();

        $strNameEngGB = __FUNCTION__ . " eng-GB";
        $strNameFreFR = __FUNCTION__ . " fre-FR";

        // add a secondary language
        $locale = eZLocale::instance( 'fre-FR' );
        $translation = eZContentLanguage::addLanguage( $locale->localeCode(), $locale->internationalLanguageName() );

        // set the prioritize language list to contain english
        // ezpINIHelper::setINISetting( 'site.ini', 'RegionalSettings', 'SiteLanguageList', array( 'fre-FR' ) );
        eZContentLanguage::setPrioritizedLanguages( ['fre-FR'] );

        // Create an object with data in fre-FR and eng-GB
        $folder = new ezpObject( 'folder', 2, 14, 1, 'eng-GB' );
        $folder->publish();

        // Workaround as setting folder->name directly doesn't produce the expected result
        $folder->addTranslation( 'eng-GB', ['name' => $strNameEngGB] );
        $folder->addTranslation( 'fre-FR', ['name' => $strNameFreFR] );

        $nodeId = $folder->main_node_id;

        // fetch the node with no default parameters. Should return the french URL Alias
        $node = eZContentObjectTreeNode::fetch( $nodeId );
        self::assertEquals( 'testIssue17632-fre-FR' , $node->attribute( 'url_alias' ) );

        // fetch the node in english. Should return the english URL Alias
        $node = eZContentObjectTreeNode::fetch( $nodeId, 'eng-GB' );
        self::assertEquals( 'testIssue17632-eng-GB' , $node->attribute( 'url_alias' ) );

        $folder->remove();
        $translation->removeThis();
        ezpINIHelper::restoreINISettings();
        eZContentLanguage::setPrioritizedLanguages( $bkpLanguages );
    }

    /**
     * Regression test for issue {@see #23753 http://issues.ez.no/23753}
     *
     * In a multi language environment, an untranslated node fetched with default language will return the
     * full URL alias in the language of the node.
     */
    public function testIssue23753()
    {
        $bkpLanguages = eZContentLanguage::prioritizedLanguageCodes();

        $strNameEngGB = __FUNCTION__ . " eng-GB";
        $strNameFreFR = __FUNCTION__ . " fre-FR";

        // add a secondary language
        $locale = eZLocale::instance( 'fre-FR' );
        $translation = eZContentLanguage::addLanguage( $locale->localeCode(), $locale->internationalLanguageName() );

        // set the prioritize language list to contain english
        eZContentLanguage::setPrioritizedLanguages( ['fre-FR', 'eng-GB'] );

        // Create an object with data in fre-FR and eng-GB
        $folder = new ezpObject( 'folder', 2, 14, 1, 'eng-GB' );
        $folder->publish();

        // Workaround as setting folder->name directly doesn't produce the expected result
        $folder->addTranslation( 'eng-GB', ['name' => $strNameEngGB] );
        $folder->addTranslation( 'fre-FR', ['name' => $strNameFreFR] );


        $article = new ezpObject( 'article', $folder->main_node_id, 14, 1, 'eng-GB' );
        $article->publish();

        // Workaround as setting article->name directly doesn't produce the expected result
        $article->addTranslation( 'eng-GB', ['title' => $strNameEngGB] );

        $nodeId = $article->main_node_id;

        // fetch the node with no default parameters. Should return the french URL Alias when applicable
        $node = eZContentObjectTreeNode::fetch( $nodeId );
        self::assertEquals( 'testIssue23753-fre-FR/testIssue23753-eng-GB' , $node->attribute( 'url_alias' ) );

        // fetch the node in english. Should return the full english URL Alias
        $node = eZContentObjectTreeNode::fetch( $nodeId, 'eng-GB' );
        self::assertEquals( 'testIssue23753-eng-GB/testIssue23753-eng-GB' , $node->attribute( 'url_alias' ) );

        // Test that PathPrefix is correctly removed from UrlAlias
        ezpINIHelper::setINISetting( 'site.ini', 'SiteAccessSettings', 'PathPrefix', 'testIssue23753-fre-FR' );
        $node = eZContentObjectTreeNode::fetch( $nodeId );
        self::assertEquals( 'testIssue23753-eng-GB' , $node->attribute( 'url_alias' ) );

        $folder->remove();
        $translation->removeThis();
        ezpINIHelper::restoreINISettings();
        eZContentLanguage::setPrioritizedLanguages( $bkpLanguages );
    }
}
?>
