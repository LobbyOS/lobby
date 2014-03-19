<?include("../includes/load.php");require L_ROOT."includes/class-app.php";?>
<html>
 <head>
  <?$LC->head("Lobby Info");?>
 </head>
 <body>
  <?
  include("../includes/ps/top.php");
  ?>
  <div class="workspace">
   <div class="contents">
    <?
    if(isset($_GET['upgraded'])){
     sss("Upgraded", "Lobby was successfully upgraded to Version <b>".getOption("lobby_version")."</b> from the old ".$_GET['oldver']." version.");
    }
    ?>
    <h2>About</h2>
    <p>You can see the information about your Lobby install.</a></p>
    <table border="1" style="width: 100%;margin-top:5px"><tbody>
     <tr>
      <td>Version</td>
      <td><?echo getOption("lobby_version");?></td>
     </tr>
     <tr>
      <td>Release Date</td>
      <td><?echo getOption("lobby_version_release");?></td>
     </tr>
     <tr>
      <td>Latest Version</td>
      <td><?echo getOption("lobby_latest_version");?></td>
     </tr>
     <tr>
      <td>Latest Version Release Date</td>
      <td><?echo getOption("lobby_latest_version_release");?></td>
     </tr>
    </tbody></table>
    <div clear></div>
    <?
    if(getOption("lobby_version")!=getOption("lobby_latest_version")){
    ?>
    <a class="button" href="upgrade.php">Upgrade To Version <?echo getOption("lobby_latest_version");?></a>
    <?
    }
    ?>
   </div>
  </div>
  <style>
   .contents table td{
    border-top:1px solid black;
    border:1px solid black;
    padding:4px 15px;
   }
  </style>
 </body>
</html>
