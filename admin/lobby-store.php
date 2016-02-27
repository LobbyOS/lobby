<?php
require "../load.php";
header('Content-type: text/html; charset=utf-8');
?>
<html>
  <head>
    <?php
    \Lobby::addStyle("lobby-store", "/admin/css/lobby-store.css");
    \Lobby::addScript("lobby-store", "/admin/js/lobby-store.js");
    \Lobby::doHook("admin.head.begin");
    \Lobby::head("Lobby Store");
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
        if(isset($_GET['id']) && $_GET['id']!=""){
          $AppID = $_GET['id'];
          $app = \Lobby\Server::Store(array(
            "get" => "app",
            "id" => $AppID
          ));

          if($app === "false"){
            ser("404 - App Not Found", "App was not found in Lobby Store.");
          }else{
            $appImage = $app['image'] != "" ? $app['image'] : L_URL . "/includes/lib/lobby/image/blank.png";
            $c = $app['category'];
            $sc = $app['sub_category'];
        ?>
            <h1><?php echo "<a href='". L_SERVER ."/../apps/{$app['id']}' target='_blank'>{$app['name']}</a>";?></h1>
            <p style="margin-bottom:15px;margin-top:-5px;"><?php echo $app['short_description'];?></p>
            <div id="leftpane" style="float:left;margin-right:10px;display:inline-block;width: 200px;text-align:center;">
              <img src="<?php echo $appImage;?>" height="200" width="200" />
              <a clear="" href="<?php echo $app['app_page'];?>" target="_blank" class="btn">App Page</a>
              <cl/>
              <?php
              $App = new \Lobby\Apps($AppID);
              if(!$App->exists){
                echo \Lobby::l("/admin/install-app.php?id={$_GET['id']}" . H::csrf("g"), "Install", "class='btn red'");
              }else if(version_compare($App->info['version'], $app['version'])){
                echo \Lobby::l("/admin/check-updates.php", "Update App", "class='btn red'");
              }else if($App->enabled){
                echo \Lobby::l($App->info['URL'], "Open App", "class='btn green'");
              }else{
                // App Diabled
                echo \Lobby::l("/admin/apps.php?action=enable&redirect=1&app=" . $AppID . H::csrf("g"), "Enable App", "class='btn green'");
              }
              ?>
              <style>#leftpane .btn{width:100%;margin: 5px 0px;}</style>
            </div>
            <div style="display:inline-block;width: 60%;">
              <table>
                <thead>
                  <tr>
                    <td style="width: 10%;">Version</td>
                    <td style="width: 25%;">Category</td>
                    <td style="width: 15%;">Author</td>
                    <td style="width: 5%;">Rating</td>
                    <td style="width: 5%;">Downloads</td>
                    <td style="width: 40%;">Last Updated</td>
                  </tr>
                </tbody>
                <tbody>
                  <tr>
                    <td><?php echo $app['version'];?></td>
                    <td><?php echo "<a href='". L_SERVER ."/../apps?c={$c}' target='_blank'>" . ucfirst($c) . "</a>";?> > <?php echo "<a href='". L_SERVER ."/../apps?sc={$sc}' target='_blank' >" . ucfirst($sc) . "</a>";?></td>
                    <td><a href="<?php echo $app['author_page'];?>" target="_blank"><?php echo $app['author'];?></a></td>
                    <td><?php echo $app['rating'];?></td>
                    <td><?php echo $app['downloads'];?></td>
                    <td><?php echo date( "l, jS \of F Y", strtotime($app['updated']) );?></td>
                  </tr>
                </tbody>
              </table>
              <p style="max-width: 500px;margin-top: 20px;">
                <?php echo $app['description'];?>
              </p>
            </div>
        <?php
          }
        }else{
        ?>
          <h1><a href='<?php echo L_SERVER . "/../apps";?>' target='_blank'>Lobby Store</a></h1>
          <div clear></div>
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
          
          $server_response = \Lobby\Server::Store($request_data);
          if($server_response == false){
            ser("Nothing Found", "Nothing was found that matches your criteria. Sorry...");
          }else{
            echo "<div class='apps'>";
              foreach($server_response['apps'] as $app){
                $appImage = $app['image'] != "" ? $app['image'] : L_URL."/includes/lib/lobby/image/blank.png";
                $url = \Lobby::u("/admin/lobby-store.php?id={$app['id']}");
            ?>
                <div class="app">
                  <div class="app-inner">
                    <div class="lpane">
                      <a href="<?php echo $url;?>">
                        <img src="<?php echo $appImage;?>" />
                      </a>
                    </div>
                    <div class="rpane">
                      <a href="<?php echo $url;?>" class="name"><?php echo $app['name'];?></a>
                      <p class="description"><?php echo $app['short_description'];?></p>
                      <p>By: <a href="<?php echo $app['author_page'];?>"><?php echo $app['author'];?></a></p>
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
