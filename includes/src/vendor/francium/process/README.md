# Francium-Process

Run **Non Blocking Background Processes** in PHP. Works for Unix (Linux, Mac) and Windows Systems

## Install

Simply install it with Composer
```bash
composer require francium/process
```

## Usage

* Run a PHP file in background :
  ```php
  $Process = new \Fr\Process("/usr/bin/php", array(
    "arguments" => array(
      "myfile.php"
    )
  ));
  $Process->start(function(){
    echo "started";
  });
  ```
  The callback passed to the `start()` function will be executed when the process is started. Also, any `echo` output will be shown in browser and connection will be immediately closed.
  
  If there is no callback mentioned, the browser will still be in connection with the script waiting for further result.

* Run `ffmpeg` in background :
  ```php
  $Process = new \Fr\Process("ffmpeg", array(
    "arguments" => array(
      "-i" => "video.avi",
      "image%d.jpg"
    )
  ));
