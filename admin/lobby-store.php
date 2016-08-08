<?php
require "../load.php";

use Lobby\Apps;
use Lobby\Need;
use Lobby\Time;

$page_title = "Lobby Store";
$appID = \Request::get('app');
if($appID !== null){
  $app = \Lobby\Server::store(array(
    "get" => "app",
    "id" => $appID
  ));
  if($app){
    $page_title = $app['name'] . " | Lobby Store";
  }
}
?>
<html>
  <head>
    <?php
    \Assets::css("apps-grid", "/admin/css/apps-grid.css");
    \Assets::css("lobby-store", "/admin/css/lobby-store.css");
    \Assets::js("lobby-store", "/admin/js/lobby-store.js");
    
    \Hooks::doAction("admin.head.begin");
    \Response::head($page_title);
    ?>
  </head>
  <body>
    <?php
    \Hooks::doAction("admin.body.begin");
    ?>
    <div id="workspace">
      <div class="contents">
        <?php
        if($appID !== null){
          if($app === false){
            echo ser("404 - App Not Found", "App was not found in Lobby Store.");
          }else{
            $appImage = $app['image'] != "" ? $app['image'] : L_URL . "/includes/lib/lobby/image/blank.png";
            $c = $app['category'];
            $sc = $app['sub_category'];
        ?>
            <h1>
              <?php
              echo Lobby::l("/admin/lobby-store.php?id={$app['id']}", $app['name']);
              echo Lobby::l(L_SERVER . "/apps/{$app['id']}?lobby_url=" . urlencode(L_URL), "<i id='open-in-new' class='small'></i>", "target='_blank'");
              ?>
            </h1>
            <div id="appNav">
              <?php echo "<div class='chip'><a href='". L_SERVER ."/apps?c={$c}' target='_blank'>" . ucfirst($c) . "</a> &gt; <a href='". L_SERVER ."/apps?sc={$sc}' target='_blank' >" . ucfirst($sc) . "</a></div>";?>
              <p class="chip"><?php echo $app['short_description'];?></p>
            </div>
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
                $App = new Apps($appID);
                $require = $app['require'];
                
                if(!$App->exists){
                  /**
                   * Check whether Lobby version is compatible
                   */
                  if(Need::checkRequirements($require, true)){
                    echo \Lobby::l("/admin/install-app.php?app={$appID}" . CSRF::getParam(), "Install", "class='btn red'");
                  }else{
                    echo "<a class='btn red disabled' title='The app requirements are not satisfied. See 'Info' tab.'>Install</a>";
                  }
                }else if(version_compare($app['version'], $App->info['version'], ">")){
                  /**
                   * New version of app is available
                   */
                  echo \Lobby::l("/admin/check-updates.php", "Update App", "class='btn red'");
                }else if($App->enabled){
                  echo \Lobby::l($App->info['url'], "Open App", "class='btn green'");
                }else{
                  /**
                   * App is Disabled. Show button to enable it
                   */
                  echo \Lobby::l("/admin/apps.php?action=enable&redirect=1&app=" . $appID . CSRF::getParam(), "Enable App", "class='btn green'");
                }
                ?>
                <div class="chip" clear>Developed By <a href="<?php echo $app['author_page'];?>" target="_blank"><?php echo $app['author'];?></a></div>
                <div class="chip" clear><a href="<?php echo $app['app_page'];?>" target="_blank">App's Webpage</a></div>
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
                  <div class="chip">Last updated <?php echo Time::getTimeago($app['updated']);?></div><cl/>
                  <div class="chip"><span>Requirements :</span></div>
                    <ul class="collection" style="margin-left: 20px;">
                      <?php
                      $requirementsInSystemInfo = Need::checkRequirements($require);
                      foreach($require as $k => $v){
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
                    echo ser("No Screenshots", "This app has no screenshots");
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
          $q = Request::get("q");
          $p = Request::get("p");
          $section = Request::get("section");
        ?>
          <h1>
            <a href="<?php echo Lobby::u("/admin/lobby-store.php");?>">Lobby Store</a>
            <a href="<?php echo L_SERVER . "/apps?lobby_url=" . urlencode(L_URL);?>'" target="_blank"><i id="open-in-new" class="small"></i></a>
          </h1>
          <div id="storeNav" class="card">
            <form method="GET" action="<?php echo \Lobby::u("/admin/lobby-store.php");?>">
              <input type="text" placeholder="Search for an app" name="q" value="<?php echo htmlspecialchars($q);?>" />
              <button class="hide"></button>
            </form>
            <?php
            echo Lobby::l("/admin/lobby-store.php", "New", "class='btn ". ($section === null ? "green" : "") ."'");
            echo Lobby::l("/admin/lobby-store.php?section=popular", "Popular", "class='btn ". ($section === "popular" ? "green" : "") ."'");
            ?>
          </div>
          <?php
          if($q !== null)
            $params = array(
              "q" => $_GET['q']
            );
          else
            $params = array(
              "get" => "newApps"
            );
          
          if($section !== null)
            $params["get"] = "popular";
          
          if($p !== null)
            $params["p"] = $p;
          
          $server_response = \Lobby\Server::store($params);
          if($server_response == false){
            echo ser("Nothing Found", "Nothing was found that matches your criteria. Sorry...");
          }else{
            echo "<div class='apps row'>";
              foreach($server_response['apps'] as $app){
                $appImage = $app['image'] != "" ? $app['image'] : L_URL."/includes/lib/lobby/image/blank.png";
                $url = \Lobby::u("/admin/lobby-store.php?app={$app['id']}");
            ?>
                <div class="app card col s12 m6 l6">
                  <div class="app-inner row">
                    <div class="lpane col s4 m5 l4">
                      <a href="<?php echo $url;?>">
                        <img src="<?php echo $appImage;?>" />
                      </a>
                    </div>
                    <div class="rpane col s8 m6 l8">
                      <a href="<?php echo $url;?>" class="name"><?php echo $app['name'];?></a>
                      <p class="description truncate" title="<?php echo $app['short_description'];?>"><?php echo $app['short_description'];?></p>
                      <div class="chip">Version : <?php echo $app['version'];?></div>
                      <div class="chip">By <a href="<?php echo $app['author_page'];?>"><?php echo $app['author'];?></a></div>
                    </div>
                  </div>
                  <div class="bpane row">
                    <div class="lside col s6 l6">
                      <?php
                      echo "<div>Rating: " . $app['rating'] . "</div>";
                      echo "<div class='downloads'>" . $app['downloads'] . " downloads</div>";
                      ?>
                    </div>
                    <div class="rside col s6 l6">
                      <div>Updated <?php echo Time::getTimeago($app['updated']);?></div>
                    </div>
                  </div>
                </div>
            <?php
              }
            echo '</div>';
            $apps_pages = (ceil($server_response['apps_count'] / 6)) + 1;
            $cur_page = \Request::get("p", "1");
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
