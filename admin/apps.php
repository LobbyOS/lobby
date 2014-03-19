<?include("../includes/load.php");?>
<html>
 <head>
  <?$LC->head("App Manager");?>
 </head>
 <body>
  <?include("../includes/ps/top.php");?>
  <div class="workspace">
   <div class="contents">
    <h2>App Manager</h2>
    <p>You can remove or disable installed apps using this page. You can Install Great Apps from <a href="<?echo L_HOST;?>/admin/appCenter.php">App Center</a>.</p>
    <style>
    .contents table td{
     border:1px solid black;
     padding:4px 15px;
    }
    </style>
    <?
    if(isset($_GET['action']) && isset($_GET['app'])){
     $action=$_GET['action'];
     $app=$_GET['app'];
     $App=new App($app);
     if(!$App->exists){
      ser("Error", "I checked all over, but App Does Not Exist");
     }
     if($action=="disable"){
      if($App->disableApp()){
       sss("Disabled", "App has been disabled.");
      }else{
       ser("Error", "The App couldn't be disabled. Try again.", false);
      }
     }else if($action=="remove"){
      $App->removeApp();
     }else if($action=="enable"){
      if($App->enableApp()){
       sss("Enabled", "App has been enabled.");
      }else{
       ser("Error", "The App couldn't be enabled. Try again.", false);
      }
     }
    }
    ?>
    <h3>Enabled Apps</h3>
    <table style="width: 100%;margin-top:5px"><tbody>
     <?
     $apps=getOption("active_apps");
     $apps=json_decode($apps, true);
     if(count($apps)==0){
      ser("No Enabled Apps", "", false);
     }
     ?>
     <tr>
      <td>Name</td>
      <td>Short Description</td>
      <td>Actions</td>
     </tr>
     <?
     foreach($apps as $app){
      $App = new App($app);
      $data=$App->getInfo();
      $appImage=!isset($data['image']) ? L_HOST."/includes/img/blank_app.png":$data['image'];
     ?>
     <tr>
      <td><a href="<?echo L_HOST.'/app/'.$app;?>"><?echo$data['name'];?></a></td>
      <td><?echo$data['short_description'];?></td>
      <td>
       <a href="?action=disable&app=<?echo$app;?>">Disable</a> |
       <a href="?action=remove&app=<?echo$app;?>">Remove</a>
      </td>
     </tr>
     <?}?>
    </tbody></table>
    <h3>Disabled Apps</h3>
    <?
    $apps=getOption("active_apps");
    $apps=json_decode($apps, true);
    $App = new App();
    $Disapps=array();
    foreach($App->getApps() as $app){
     if(array_search($app, $apps)===false){
      $Disapps[]=$app;
     }
    }
    if(count($Disapps)==0){
     ser("No Disabled Apps", "You haven't disabled any apps.", false);
    }
    ?>
    <table style="width: 100%;margin-top:5px"><tbody>
     <tr>
      <td>Name</td>
      <td>Short Description</td>
      <td>Actions</td>
     </tr>
     <?
     foreach($Disapps as $app){
      $App = new App($app);
      $data=$App->getInfo();
      $appImage=!isset($data['image']) ? L_HOST."/includes/img/blank_app.png":$data['image'];
     ?>
     <tr>
      <td><a href="<?echo L_HOST.'/app/'.$app;?>"><?echo$data['name'];?></a></td>
      <td><?echo$data['short_description'];?></td>
      <td>
       <a href="?action=enable&app=<?echo$app;?>">Enable</a> |
       <a href="?action=remove&app=<?echo$app;?>">Remove</a>
      </td>
     </tr>
     <?}?>
    </tbody></table>
   </div>
  </div>
 </body>
</html>
