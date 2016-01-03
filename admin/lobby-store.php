<?php
include "../load.php";
header( 'Content-type: text/html; charset=utf-8' );
?>
<html>
  <head>
    <?php
    \Lobby::addStyle("lobby-store", "/admin/CSS/lobby-store.css");
    \Lobby::doHook("admin.head.begin");
    \Lobby::head("Lobby Store");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    include "$docRoot/admin/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <?php
        if(isset($_GET['id']) && $_GET['id']!=""){
          $app = \Lobby\Server::Store(array(
            "get" => "app",
            "id" => $_GET['id']
          ));

          if($app == "false"){
            ser("404 - App Not Found", "App was not found in Lobby Store.");
          }else{
            $appImage = $app['image'] != "" ? $app['image'] : L_URL . "/includes/lib/core/Img/blank.png";
            $c = $app['category'];
            $sc = $app['sub_category'];
        ?>
            <h1><?php echo "<a href='". L_SERVER ."/../apps/{$app['id']}' target='_blank'>{$app['name']}</a>";?></h1>
            <p style="margin-bottom:15px;margin-top:-5px;"><?php echo $app['short_description'];?></p>
            <div id="leftpane" style="float:left;margin-right:10px;display:inline-block;width: 200px;text-align:center;">
              <img src="<?php echo $appImage;?>" height="200" width="200" />
              <a clear="" href="<?php echo $app['app_page'];?>" target="_blank" class="button">App Page</a>
              <cl/>
              <?php
              $App = new \Lobby\Apps($_GET['id']);
              if(!$App->exists){
                echo \Lobby::l("/admin/install-app.php?id={$_GET['id']}" . H::csrf("g"), "Install", "class='button'");
              }elseif(version_compare($App->info['version'], $app['version'])){
                echo \Lobby::l("/admin/check-updates.php", "Update App", "class='button red'");
              }else{
                echo \Lobby::l($App->info['URL'], "Open App", "class='button green'");
              }
              ?>
              <style>#leftpane .button{width:100%;margin: 5px 0px;}</style>
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
          <p>Find Great New Apps</p>
          <div clear></div>
          <form method="GET" action="<?php echo \Lobby::u("/admin/lobby-store.php");?>">
            <input type="text" placeholder="Type an app name" name="q" style="width:450px;"/>
            <button class="button red">Search</button>
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
            foreach($server_response['apps'] as $app){
              $appImage = $app['image'] != "" ? $app['image'] : L_URL."/includes/lib/core/Img/blank.png";
              $url = \Lobby::u("/admin/lobby-store.php?id={$app['id']}");
          ?>
            <div class="app">
              <a href="<?php echo $url;?>">
                <div class="outer">
                  <img class="image" src="<?php echo $appImage;?>" />
                  <div class="title"><?php echo $app['name'];?></div>
                </div>
                <div class="inner">
                  <a href="<?php echo $url;?>">
                    <div style="padding: 5px;height: 200px;"><?php echo $app['short_description'];?></div>
                  </a>
                  <div class="byline">
                    <?php echo "<a href='{$app['author_page']}' target='_blank'>{$app['author']}</a>";?>
                    <span style="float: right;">
                      <?php echo "<a href='{$app['app_page']}' target='_blank'>App Page</a>";?>
                    </span>
                  </div>
                </div>
              </a>
            </div>
          <?php
            }
            $apps_pages = (ceil($server_response['apps_count'] / 10)) + 1;
            echo "<div class='pages'>";
              for($i = 1;$i < $apps_pages;$i++){
                echo "<a href='?p=$i' class='button ". (isset($_GET['p']) && $_GET['p'] == $i ? "blue" : "green") ."'>$i</a>";
              }
            echo '</div>';
          }
        }
        ?>
      </div>
    </div>
  </body>
</html>
