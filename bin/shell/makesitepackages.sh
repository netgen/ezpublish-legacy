#!/bin/bash

. ./bin/shell/common.sh
. ./bin/shell/packagescommon.sh

if ! which php &>/dev/null; then
    echo "No PHP executable found, please add it to the path"
    exit 1
fi

TMPDIR="/tmp/ez-$USER/packages"

[ -d $TMPDIR ] && rm -rf "$TMPDIR"
mkdir -p $TMPDIR || exit 1

PMBIN="./ezpm.php"

SITE_PACKAGES="$TMPDIR/extra.tmp"
SITE_PACKAGES_EXPORT="$TMPDIR/extra"
OUTPUT_REPOSITORY="$TMPDIR/sites"
EXPORT_PATH="kernel/setup/packages"
PACKAGE_FILES="packages/var"
CREATE_SITESTYLES="true"
CREATE_SITES="true"

# Check parameters
for arg in $*; do
    case $arg in
	--help|-h)
	    echo "Usage: $0 [options]"
	    echo
	    echo "Options: -h"
	    echo "         --help                     This message"
	    echo "         --build-root=DIR           Set build root, default is /tmp"
	    echo "         --with-svn-server[=SERVER] Checkout fresh repository"
	    echo "         --with-release=NAME        Checkout a previous release, default is trunk"
	    echo "         --skip-site-creation       Do not build sites*"
	    echo "         --skip-php-check           Do not check PHP for syntax correctnes*"
	    echo
	    echo "* Warning: Using these options will not make a valid distribution"
            echo
            echo "Example:"
            echo "$0 --release-sdk --with-svn-server"
	    exit 1
	    ;;
	-q)
	    QUIET="-q"
	    ;;
	--site=*)
	    if echo $arg | grep -e "--site=" >/dev/null; then
		SITE=`echo $arg | sed 's/--site=//'`
	    fi
	    ;;
	--export-path*)
	    if echo $arg | grep -e "--export-path=" >/dev/null; then
		EXPORT_PATH=`echo $arg | sed 's/--export-path=//'`
	    fi
	    ;;
	*)
	    echo "$arg: unkown option specified"
            $0 -h
	    exit 1
	    ;;
    esac;
done

[ -d $EXPORT_PATH ] || { echo "The export path $EXPORT_PATH does not exist"; exit 1; }

[ -z $QUIET ] && echo "Placing packages in $SITE_PACKAGES"

## Common initialization

rm -rf "$OUTPUT_REPOSITORY"
mkdir -p "$OUTPUT_REPOSITORY" || exit 1

rm -rf "$SITE_PACKAGES"
mkdir -p "$SITE_PACKAGES" || exit 1

rm -rf "$SITE_PACKAGES_EXPORT"
mkdir -p "$SITE_PACKAGES_EXPORT" || exit 1

## Shop sitestyles

if [[ -z $SITE || $SITE = 'shop' ]]; then

    mkdir -p "$SITE_PACKAGES_EXPORT/shop"
    ./ezpm.php -r "$SITE_PACKAGES/shop" $QUIET \
	create shop_blue "Blue CSS design for shop" "$VERSION"  -- \
	set shop_blue type sitestyle -- \
	add shop_blue file -n cssfile design/shop/stylesheets/shop-blue.css -- \
	add shop_blue thumbnail -n thumbnail design/shop/images/shop-blue.png -- \
	add shop_blue file -n image design/shop/images/blue/tab_corner-top_left.gif -- \
	add shop_blue file -n image design/shop/images/blue/tab_corner-top_right.gif -- \
	add shop_blue file -n image design/shop/images/shopping-menu-icon.gif -- \
	export shop_blue -d "$SITE_PACKAGES_EXPORT/shop" -- \
	create shop_red "Red CSS design for shop" "$VERSION" -- \
	set shop_red type sitestyle -- \
	add shop_red file -n cssfile design/shop/stylesheets/shop.css -- \
	add shop_red thumbnail -n thumbnail design/shop/images/shop-red.png -- \
	add shop_red file -n image design/shop/images/header-background-top_right.gif -- \
	add shop_red file -n image design/shop/images/header-background-top_left.gif -- \
	add shop_red file -n image design/shop/images/left_sidebar-background.gif -- \
	add shop_red file -n image design/shop/images/tab_corner-top_left.gif -- \
	add shop_red file -n image design/shop/images/tab_corner-top_right.gif -- \
	add shop_red file -n image design/shop/images/shopping-menu-icon.gif -- \
	add shop_red file -n image design/shop/images/half_white-transparent.gif -- \
	export shop_red -d "$SITE_PACKAGES_EXPORT/shop" || exit 1
fi

## Blog sitestyles

if [[ -z $SITE || $SITE = 'blog' ]]; then

    mkdir -p "$SITE_PACKAGES_EXPORT/blog"
    ./ezpm.php -r "$SITE_PACKAGES/blog" $QUIET \
	create blog_blue "Blue CSS design for blog" "$VERSION" -- \
	set blog_blue type sitestyle -- \
	add blog_blue file -n cssfile design/blog/stylesheets/blog_blue.css -- \
	add blog_blue thumbnail -n thumbnail design/blog/images/blog-blue.png -- \
	export blog_blue -d "$SITE_PACKAGES_EXPORT/blog" -- \
	create blog_red "Blue CSS design for blog" "$VERSION" -- \
	set blog_red type sitestyle -- \
	add blog_red file -n cssfile design/blog/stylesheets/blog_red.css -- \
	add blog_red thumbnail -n thumbnail design/blog/images/blog-red.png -- \
	export blog_red -d "$SITE_PACKAGES_EXPORT/blog" || exit 1
fi

## Gallery sitestyles

if [[ -z $SITE || $SITE = 'gallery' ]]; then

    mkdir -p "$SITE_PACKAGES_EXPORT/gallery"
    ./ezpm.php -r "$SITE_PACKAGES/gallery" $QUIET \
	create gallery_gray "Gray CSS design for gallery" "$VERSION" -- \
	set gallery_gray type sitestyle -- \
	add gallery_gray file -n cssfile design/gallery/stylesheets/gallery_gray.css -- \
	add gallery_gray thumbnail -n thumbnail design/gallery/images/gallery_gray.jpg -- \
	add gallery_gray file -n image design/gallery/images/gray_info.png -- \
	add gallery_gray file -n image design/gallery/images/gray_header.png -- \
	export gallery_gray -d "$SITE_PACKAGES_EXPORT/gallery" -- \
	create gallery_blue "Blue CSS design for gallery" "$VERSION" -- \
	set gallery_blue type sitestyle -- \
	add gallery_blue file -n cssfile design/gallery/stylesheets/gallery_blue.css -- \
	add gallery_blue thumbnail -n thumbnail design/gallery/images/gallery_blue.jpg -- \
	export gallery_blue -d "$SITE_PACKAGES_EXPORT/gallery" || exit 1
fi

## News sitestyles

if [[ -z $SITE || $SITE = 'news' ]]; then

    mkdir -p "$SITE_PACKAGES_EXPORT/news"
    ./ezpm.php -r "$SITE_PACKAGES/news" $QUIET \
	create news_blue "Blue CSS design for news" "$VERSION" -- \
	set news_blue type sitestyle -- \
	add news_blue file -n cssfile design/news/stylesheets/news_blue.css -- \
	add news_blue thumbnail -n thumbnail design/news/images/news_blue.jpg -- \
	export news_blue -d "$SITE_PACKAGES_EXPORT/news" -- \
	create news_brown "Brown CSS design for news" "$VERSION" -- \
	set news_brown type sitestyle -- \
	add news_brown file -n cssfile design/news/stylesheets/news.css -- \
	add news_brown thumbnail -n thumbnail design/news/images/news_brown.jpg -- \
	export news_brown -d "$SITE_PACKAGES_EXPORT/news" || exit 1
fi

## Intranet sitestyles

if [[ -z $SITE || $SITE = 'intranet' ]]; then

    mkdir -p "$SITE_PACKAGES_EXPORT/intranet"
    ./ezpm.php -r "$SITE_PACKAGES/intranet" $QUIET \
	create intranet_gray "Gray CSS design for intranet" "$VERSION" -- \
	set intranet_gray type sitestyle -- \
	add intranet_gray file -n cssfile design/intranet/stylesheets/intranet.css -- \
	add intranet_gray thumbnail -n thumbnail design/intranet/images/intranet_gray.png -- \
	add intranet_gray file -n image design/intranet/images/06_intranet_logo.png -- \
	export intranet_gray -d "$SITE_PACKAGES_EXPORT/intranet" -- \
	create intranet_red "Red CSS design for intranet" "$VERSION" -- \
	set intranet_red type sitestyle -- \
	add intranet_red file -n cssfile design/intranet/stylesheets/intranet_red.css -- \
	add intranet_red thumbnail -n thumbnail design/intranet/images/intranet_red.png -- \
	add intranet_red file -n image design/intranet/images/green_logo.png -- \
	export intranet_red -d "$SITE_PACKAGES_EXPORT/intranet" || exit 1
fi

## Corporate sitestyles

if [[ -z $SITE || $SITE = 'corporate' ]]; then

    mkdir -p "$SITE_PACKAGES_EXPORT/corporate"
    ./ezpm.php -r "$SITE_PACKAGES/corporate" $QUIET \
	create corporate_blue "Blue CSS design for corporate" "$VERSION" -- \
	set corporate_blue type sitestyle -- \
	add corporate_blue file -n cssfile design/corporate/stylesheets/corporate-blue.css -- \
	add corporate_blue file -n image design/corporate/images/header_image.gif -- \
	add corporate_blue file -n image design/corporate/images/infobox_image.gif -- \
	add corporate_blue file -n image design/corporate/images/header_image-repeat.gif -- \
	add corporate_blue thumbnail -n thumbnail design/corporate/images/corporate-blue.png -- \
	export corporate_blue -d "$SITE_PACKAGES_EXPORT/corporate" -- \
	create corporate_green "Green design for corporate" "$VERSION" -- \
	set corporate_green type sitestyle -- \
	add corporate_green file -n cssfile design/corporate/stylesheets/corporate-green.css -- \
	add corporate_green file -n image design/corporate/images/sidebar-background.gif -- \
	add corporate_green file -n image design/corporate/images/header_image-green.jpg -- \
	add corporate_green thumbnail -n thumbnail design/corporate/images/corporate-green.png -- \
	export corporate_green -d "$SITE_PACKAGES_EXPORT/corporate" || exit 1
fi


## Forum sitestyles

if [[ -z $SITE || $SITE = 'forum' ]]; then
    mkdir -p "$SITE_PACKAGES_EXPORT/forum"
    ./ezpm.php -r "$SITE_PACKAGES/forum" $QUIET \
	create forum_red "Red CSS design for forum" "$VERSION" -- \
	set forum_red type sitestyle -- \
	add forum_red file -n cssfile design/forum/stylesheets/forum.css -- \
	add forum_red file -n image design/forum/images/forum_header-red.gif -- \
	add forum_red thumbnail -n thumbnail design/forum/images/forum_red.jpg -- \
	export forum_red -d "$SITE_PACKAGES_EXPORT/forum" -- \
	create forum_blue "Blue CSS design for forum" "$VERSION" -- \
	set forum_blue type sitestyle -- \
	add forum_blue file -n cssfile design/forum/stylesheets/forum_blue.css -- \
	add forum_blue thumbnail -n thumbnail design/forum/images/forum_blue.jpg -- \
	add forum_blue file -n image design/forum/images/forum_header-blue.gif -- \
	export forum_red -d "$SITE_PACKAGES_EXPORT/forum" || exit 1
fi

## Plain site
## Various from the other sites and must initialized by itself

if [[ -z $SITE || $SITE = 'plain' ]]; then
    ./ezpm.php -r "$OUTPUT_REPOSITORY" $QUIET \
	create plain "Plain" "$VERSION" -- \
	set plain type site -- \
	add plain design -n design plain -- \
	add plain ini -r siteaccess -v plain -n user_siteaccess settings/siteaccess/plain -- \
	add plain ini -r siteaccess -v admin -n admin_siteaccess settings/siteaccess/admin -- \
	add plain sql -d mysql kernel/sql/mysql/kernel_schema.sql -- \
	add plain sql -d mysql kernel/sql/common/cleandata.sql -- \
	add plain sql -d postgresql kernel/sql/postgresql/kernel_schema.sql -- \
	add plain sql -d postgresql kernel/sql/common/cleandata.sql -- \
	add plain sql -d postgresql kernel/sql/postgresql/setval.sql -- \
	add plain thumbnail design/plain/images/plain.gif || exit 1
fi

for site in $PACKAGES; do
    [[ -z $SITE || $SITE = $site ]] || continue

    [ -z $QUIET ] && echo "Creating $site"
    ./ezpm.php -r "$OUTPUT_REPOSITORY" $QUIET \
	create $site "`cat $PACKAGE_FILES/$site/description.txt`" "$VERSION" -- \
	set $site type site -- \
	add $site design -n design $site -- \
 	add $site ini -r siteaccess -v $site -n user_siteaccess "settings/siteaccess/"$site"_user" -- \
 	add $site ini -r siteaccess -v admin -n admin_siteaccess "settings/siteaccess/"$site"_admin" -- \
	add $site sql -d mysql kernel/sql/mysql/kernel_schema.sql -- \
	add $site sql -d mysql packages/sql/data/$site.sql -- \
	add $site sql -d postgresql kernel/sql/postgresql/kernel_schema.sql -- \
	add $site sql -d postgresql packages/sql/data/$site.sql -- \
	add $site sql -d postgresql kernel/sql/postgresql/setval.sql || exit 1

    if [ -f "$PACKAGE_FILES/$site/thumbnail.png" ]; then
	[ -z $QUIET ] && echo "Adding PNG thumbnail to $site"
	"$PMBIN" -r "$OUTPUT_REPOSITORY" $QUIET \
	    add $site thumbnail "$PACKAGE_FILES/$site/thumbnail.png" || exit 1
    elif [ -f "$PACKAGE_FILES/$site/thumbnail.gif" ]; then
	[ -z $QUIET ] && echo "Adding GIF thumbnail to $site"
	"$PMBIN" -r "$OUTPUT_REPOSITORY" $QUIET \
	    add $site thumbnail "$PACKAGE_FILES/$site/thumbnail.gif" || exit 1
    else
	[ -z $QUIET ] && echo "No thumbnail for $site"
    fi

     if [ -d "$PACKAGE_FILES/$site/storage/images" ]; then
 	[ -z $QUIET ] && echo "Adding images to $site"
 	"$PMBIN" -r "$OUTPUT_REPOSITORY" $QUIET \
 	    add $site file -p "var/$site/storage" "$PACKAGE_FILES/$site/storage/images" || exit 1
    fi

    if [ -f "$PACKAGE_FILES/$site/cache/expiry.php" ]; then
	[ -z $QUIET ] && echo "Adding expiry file to $site"
	"$PMBIN" -r "$OUTPUT_REPOSITORY" $QUIET \
	    add $site file -p "var/$site/cache" "$PACKAGE_FILES/$site/cache/expiry.php" || exit 1
    fi

done

for site in $ALL_PACKAGES; do
    [[ -z $SITE || $SITE = $site ]] || continue

    [ -z $QUIET ] && echo -n "Adding packages to $site"
    if [ -d "$SITE_PACKAGES_EXPORT/$site" ]; then
	[ -z $QUIET ] && echo

	for package in "$SITE_PACKAGES_EXPORT/$site"/*; do
	    "$PMBIN" -r "$OUTPUT_REPOSITORY" $QUIET \
		add "$site" file -p "var/$site/storage/packages" "$package" || exit 1
	done
    else
	[ -z $QUIET ] && echo ", no packages"
    fi

    if [ -d "$OUTPUT_REPOSITORY/$site" ]; then
	$PMBIN -r "$OUTPUT_REPOSITORY" $QUIET \
	    export $site -d "$EXPORT_PATH" || exit 1
    fi

done
