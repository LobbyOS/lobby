<?php
require "../load.php";

$page_title = "Lobby Store";
$AppID = \H::i('id');
if($AppID !== null){
  $app = \Lobby\Server::store(array(
    "get" => "app",
    "id" => $AppID
  ));
  if($app){
    $page_title = $app['name'] . " | Lobby Store";
  }
}
?>
<html>
  <head>
    <?php
    \Assets::css("lobby-store", "/admin/css/lobby-store.css");
    \Assets::js("lobby-store", "/admin/js/lobby-store.js");
    \Lobby::doHook("admin.head.begin");
    \Lobby::head($page_title);
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <?php
        if($AppID !== null){
          if($app === false){
            ser("404 - App Not Found", "App was not found in Lobby Store.");
          }else{
            $appImage = $app['image'] != "" ? $app['image'] : L_URL . "/includes/lib/lobby/image/blank.png";
            $c = $app['category'];
            $sc = $app['sub_category'];
        ?>
            <h1><?php echo "<a href='". L_SERVER ."/../apps/{$app['id']}' target='_blank'>{$app['name']}</a>";?></h1>
            <?php echo "<div class='chip'><a href='". L_SERVER ."/../apps?c={$c}' target='_blank'>" . ucfirst($c) . "</a> &gt; <a href='". L_SERVER ."/../apps?sc={$sc}' target='_blank' >" . ucfirst($sc) . "</a></div>";?>
            <p class="chip" style="margin: -5px 0 20px;"><?php echo $app['short_description'];?></p>
            <div class="row">
              <div class="col m3" id="leftpane" style="text-align: center;">
                <img src='image/clear.gif' height="200" width="200" />
                <script>
                  $(window).load(function(){
                    var image = $("#leftpane img");
                    var downloadingImage = new Image();
                    downloadingImage.onload = function(){
                      image.attr("src", this.src);
                    };
                    downloadingImage.src = "<?php echo $appImage;?>";
                  });
                </script>
                <?php
                $App = new \Lobby\Apps($AppID);
                $requires = $app['requires'];
                if(!$App->exists){
                  /**
                   * Check whether Lobby version is compatible
                   */
                  if(\Lobby\Need::checkRequirements($requires, true)){
                    echo "<a class='btn red disabled' title='The app requirements are not satisfied. See `Info` tab.'>Install</a>";
                  }else{
                    echo \Lobby::l("/admin/install-app.php?id={$_GET['id']}" . H::csrf("g"), "Install", "class='btn red'");
                  }
                }else if(version_compare($app['version'], $App->info['version'], ">")){
                  /**
                   * New version of app is available
                   */
                  echo \Lobby::l("/admin/check-updates.php", "Update App", "class='btn red'");
                }else if($App->enabled){
                  echo \Lobby::l($App->info['URL'], "Open App", "class='btn green'");
                }else{
                  /**
                   * App is Disabled. Show button to enable it
                   */
                  echo \Lobby::l("/admin/apps.php?action=enable&redirect=1&app=" . $AppID . H::csrf("g"), "Enable App", "class='btn green'");
                }
                ?>
                <div class="chip" clear>Developed By <a href="<?php echo $app['author_page'];?>" target="_blank"><?php echo $app['author'];?></a></div>
                <div class="chip" clear><a href="<?php echo $app['app_page'];?>" target="_blank">App's Webpage</a></div>
                <style>#leftpane .btn{width:100%;margin: 5px 0px;}</style>
              </div>
              <div class="col m9">
                <ul class="tabs">
                  <li class="tab"><a href="#app-info">Info</a></li>
                  <li class="tab"><a href="#app-description">Description</a></li>
                  <li class="tab"><a href="#app-screenshots">Screenshots</a></li>
                  <li class="tab"><a href="#app-stats">Stats</a></li>
                </ul>
                <div id="app-info" class="tab-contents">
                  <div class="chip">Version : <?php echo $app['version'];?></div>
                  <div class="chip">Last updated <?php echo $app['updated'];?></div><cl/>
                  <div class="chip"><span>Requirements :</span></div>
                    <ul class="collection" style="margin-left: 20px;">
                      <?php
                      $requirementsInSystemInfo = \Lobby\Need::checkRequirements($requires);
                      foreach($requires as $k => $v){
                        if($requirementsInSystemInfo[$k]){
                          echo "<li class='collection-item'>$k $v</li>";
                        }else{
                          echo "<li class='collection-item red' title=''>$k $v</li>";
                        }
                      }
                      ?>
                    </ul>
                </div>
                <div id="app-description" class="tab-contents">
                  <div class="card-panel light-green">
                    <span class="white-text"><?php echo $app['description'];?></span>
                  </div>
                </div>
                <div id="app-screenshots" class="tab-contents">
                  <?php
                  $screenshots = explode("\n", $app['screenshots']);
                  if(count($screenshots) > 1){
                    foreach($screenshots as $screenshot){
                      if($screenshot != ""){
                        echo "<a href='$screenshot' target='_blank' clear><img src='image/clear.gif' data-none='' width='100%' /></a>";
                      }
                    }
                    ?>
                    <script>
                      $(window).load(function(){
                        var screenshots = <?php echo json_encode($screenshots);?>;
                        $.each(screenshots, function(i, elem){
                          var image = $("#app-screenshots img[data-none]:first");
                          var downloadingImage = new Image();
                          downloadingImage.onload = function(){
                            image.attr("src", this.src);
                          };
                          downloadingImage.src = elem;
                          image.removeAttr("data-none");
                        });
                      });
                    </script>
                    <?php
                  }else{
                    ser("No Screenshots", "This app has no screenshots");
                  }
                  ?>
                </div>
                <div id="app-stats" class="tab-contents">
                  <div class="chip">Downloads : <?php echo $app['downloads'];?></div><cl/>
                  <div class="chip">Rating : <?php echo $app['rating'];?></div>
                </div>
              </div>
            </div>
            <style>
            .tab-contents{
              padding: 10px 0;
            }
            </style>
        <?php
          }
        }else{
        ?>
          <h1><a href='<?php echo L_SERVER . "/../apps?lobby_url=" . urlencode(L_URL);?>' target='_blank'>Lobby Store</a></h1>
          <cl/>
          <form method="GET" action="<?php echo \Lobby::u("/admin/lobby-store.php");?>">
            <input type="text" placeholder="Type an app name" name="q" style="width:450px;"/>
            <button class="btn red">Search</button>
          </form>
          <?php
          if(isset($_GET['q'])){
            $request_data = array(
              "q" => $_GET['q']
            );
          }else{
            $request_data = array(
              "get" => "newApps"
            );
          }
          if(isset($_GET['p'])){
            $request_data['p'] = $_GET['p'];
          }
          
          $server_response = \Lobby\Server::store($request_data);
          if($server_response == false){
            ser("Nothing Found", "Nothing was found that matches your criteria. Sorry...");
          }else{
            echo "<div class='apps'>";
              foreach($server_response['apps'] as $app){
                $appImage = $app['image'] != "" ? $app['image'] : L_URL."/includes/lib/lobby/image/blank.png";
                $url = \Lobby::u("/admin/lobby-store.php?id={$app['id']}");
            ?>
                <div class="app card">
                  <div class="app-inner">
                    <div class="lpane">
                      <a href="<?php echo $url;?>">
                        <img src="<?php echo $appImage;?>" />
                      </a>
                    </div>
                    <div class="rpane">
                      <a href="<?php echo $url;?>" class="name"><?php echo $app['name'];?></a>
                      <p class="description"><?php echo $app['short_description'];?></p>
                      <p style='font-style: italic;'>By <a href="<?php echo $app['author_page'];?>"><?php echo $app['author'];?></a></p>
                    </div>
                  </div>
                  <div class="bpane">
                    <div class="lside">
                      <?php
                      echo "<div>Rating: " . $app['rating'] . "</div>";
                      echo "<div class='downloads'>" . $app['downloads'] . " downloads</div>";
                      ?>
                    </div>
                    <div class="rside">
                      <div>Updated <?php echo $app['updated'];?></div>
                      <div>Version : <?php echo $app['version'];?></div>
                    </div>
                  </div>
                </div>
            <?php
              }
            echo '</div>';
            $apps_pages = (ceil($server_response['apps_count'] / 6)) + 1;
            $cur_page = \H::i("p", "1");
            echo "<ul class='pagination'>";
              for($i = 1;$i < $apps_pages;$i++){
                echo "<li class='waves-effect ". ($cur_page == $i ? "active" : "") ."'>";
                  echo "<a href='?p=$i'>$i</a>";
                echo "</li>";
              }
            echo '</div>';
          }
        }
        ?>
      </div>
    </div>
  </body>
</html>
