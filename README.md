Lobby
=====

[![Join the chat at https://gitter.im/subins2000/lobby](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/subins2000/lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/LobbyOS/lobby.svg?branch=dev)](https://travis-ci.org/LobbyOS/lobby)
[![Facebook Group](http://searchengineland.com/figz/wp-content/seloads/2011/02/facebook-logo-rectangle.gif =80x30)](https://www.facebook.com/groups/LobbyOS)

The default branch is `dev` which means, most stuff are in development mode and can't be guaranteed to work.

[Learn More here](https://lobby.subinsb.com)

[Download & Install](https://lobby.subinsb.com/download)

[Documentation](https://lobby.subinsb.com/docs)

## Branches

### master

The current stable version of Lobby.

### dev

The latest developments are in this branch.

WARNING (Unstable) - This repo is for testing and may contain additional components (apps, themes etc.) that are under testing and does not represent the downloadable version of Lobby. You can get the stable version from [here](http://lobby.subinsb.com/download)

## Some things to note

* The git repo has apps along with **ledit** that is in development. No guarantee that it will work.
* The docs are not complete and I will be glad if you help. :-)

## To Do

* Documentation
* "How To Create An App" Tutorials
* Submit More Innovative Apps to Lobby Store

## How To Do A New Release

* Modify `lobby.json`
* Remove logs, apps except **ledit**
* Get the list of files removed :
  ```bash
  git diff --name-status oldVersionCommitHash latestVersionCommitHash
  ```
  You can get the hashes from `git log`
  
  Extract only deleted files `D <file>` and paste it in `contents/update/removeFiles.php`
* Change Lobby server inside `includes/config.php`
* Zip the folder

## Blog

New page in `lobby/version-0-`

## Server

* Update version, release notes, blog post URL in App.php of lobby-server app
* Add download url in api.php in src/Page of lobby-server app
