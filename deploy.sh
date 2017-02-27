#!/usr/bin/env bash

# The slug of your WordPress.org plugin
PLUGIN_SLUG="nwa"


# GITHUB user who owns the repo
GITHUB_REPO_OWNER="Navionics"

# GITHUB Repository name
GITHUB_REPO_NAME="webapi_wordpress_plugin"


if [ -z "$1" ] ; then echo "Please provide the tag name to deploy "; exit 1; fi

# GITHUB tag to use
GITHUB_TAG_NAME=$1
VERSION=$GITHUB_TAG_NAME


MAINFILE=${PLUGIN_SLUG}.php

# ----- STOP EDITING HERE -----






# VARS
ROOT_PATH=$(pwd)"/"
TEMP_GITHUB_REPO=${PLUGIN_SLUG}"-git"
TEMP_SVN_REPO=${PLUGIN_SLUG}"-svn"
SVN_REPO="http://plugins.svn.wordpress.org/"${PLUGIN_SLUG}"/"
GIT_REPO="git@github.com:"${GITHUB_REPO_OWNER}"/"${GITHUB_REPO_NAME}".git"








check_env () {

    # Check if subversion is installed before getting all worked up
    if ! which svn >/dev/null; then
        echo "You'll need to install subversion before proceeding. Exiting....";
        exit 1;
    fi


}

clean_repo () {
    # DELETE OLD TEMP DIRS
    rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO
    rm -Rf $ROOT_PATH$TEMP_SVN_REPO
}


prepare_git_repo (){
    ###
    # PREPARE GIT REPOSITORY
    ###
    # CLONE GIT DIR
    echo "Cloning GIT repository from GITHUB"
    git clone --progress $GIT_REPO $TEMP_GITHUB_REPO || { echo "Unable to clone repo."; exit 1; }
}



prepare_svn_repo (){

    # CHECKOUT SVN DIR IF NOT EXISTS
    if [[ ! -d $TEMP_SVN_REPO ]];
    then
        echo "Checking out WordPress.org plugin repository"
        svn checkout $SVN_REPO $TEMP_SVN_REPO || { echo "Unable to checkout repo."; exit 1; }
    fi
}


prepare_for_release (){

    # MOVE INTO GIT DIR
    cd $ROOT_PATH$TEMP_GITHUB_REPO

    # Switch Branch
    echo "Switching to branch"
    git checkout ${GITHUB_TAG_NAME} || { echo "Unable to checkout branch."; exit 1; }


    # Check version in readme.txt is the same as plugin file after translating both to unix line breaks to work around grep's failure to identify mac line breaks
    NEWVERSION1=`grep "^Stable tag:" README.txt | awk -F' ' '{print $NF}'`
    echo "readme.txt version: $NEWVERSION1"
    NEWVERSION2=`grep "^ \* Version:" $MAINFILE | awk -F' ' '{print $NF}'`
    echo "$MAINFILE version: $NEWVERSION2"

    if [ "$NEWVERSION1" != "$NEWVERSION2" ]; then echo "Version in readme.txt & $MAINFILE don't match. Exiting...."; exit 1; fi


   if [ "$NEWVERSION1" != "$GITHUB_TAG_NAME" ]; then echo "Version don't match with tags, Exiting...."; exit 1; fi


    echo "Versions match in readme.txt and $MAINFILE. Let's proceed..."

    # REMOVE UNWANTED FILES & FOLDERS
    echo "Removing unwanted files"
    rm -Rf .git
    rm -Rf .github
    rm -Rf tests
    rm -Rf apigen
    rm -f .gitattributes
    rm -f .gitignore
    rm -f .gitmodules
    rm -f .travis.yml
    rm -f Gruntfile.js
    rm -f package.json
    rm -f .jscrsrc
    rm -f .jshintrc
    rm -f composer.json
    rm -f phpunit.xml
    rm -f phpunit.xml.dist
    rm -f README.md
    rm -f .coveralls.yml
    rm -f .editorconfig
    rm -f .scrutinizer.yml
    rm -f apigen.neon
    rm -f CHANGELOG.txt
    rm -f CONTRIBUTING.md
    rm -f deploy.sh
}



release_it() {
    # MOVE INTO SVN DIR
    cd $ROOT_PATH$TEMP_SVN_REPO

    # UPDATE SVN
    echo "Updating SVN"
    svn update || { echo "Unable to update SVN."; exit 1; }

    # DELETE TRUNK
    echo "Replacing trunk"
    rm -Rf trunk/

    # COPY GIT DIR TO TRUNK
    cp -R $ROOT_PATH$TEMP_GITHUB_REPO trunk/

    # DO THE ADD ALL NOT KNOWN FILES UNIX COMMAND
    svn add --force * --auto-props --parents --depth infinity -q

    # DO THE REMOVE ALL DELETED FILES UNIX COMMAND
    MISSING_PATHS=$( svn status | sed -e '/^!/!d' -e 's/^!//' )

    # iterate over filepaths
    for MISSING_PATH in $MISSING_PATHS; do
        svn rm --force "$MISSING_PATH"
    done

    # COPY TRUNK TO TAGS/$VERSION
    echo "Copying trunk to new tag"
    svn copy trunk tags/${VERSION} || { echo "Unable to create tag."; exit 1; }

    # DO SVN COMMIT
    clear
    echo "Showing SVN status"
    svn status

    # PROMPT USER
    echo ""
    read -p "PRESS [ENTER] TO COMMIT RELEASE "${VERSION}" TO WORDPRESS.ORG AND GITHUB"
    echo ""

    # DEPLOY
    echo ""
    echo "Committing to WordPress.org...this may take a while..."
    svn commit -m "Release "${VERSION}", see readme.txt for the changelog." || { echo "Unable to commit."; exit 1; }

}







echo "************ [1/7] Check enviroment ************"
check_env
echo "************ [2/7] Clean repository space ************"
clean_repo
echo "************ [3/7] Get git repository ************"
prepare_git_repo
echo "************ [4/7] Get svn repository ************"
prepare_svn_repo
echo "************ [5/7] Prepare code for release ************"
prepare_for_release
echo "************ [6/7] Release It ************"
release_it
echo "************ [7/7] Done Clean Up ************ "
clean_repo

echo "RELEASER DONE :D"
