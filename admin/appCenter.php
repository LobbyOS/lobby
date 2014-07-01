<?include("../load.php");?>
<html>
 <head>
  <?
  $LC->addStyle("appC", L_HOST."/includes/source/css/appC.css");
  $LC->head("App Manager");
  ?>
 </head>
 <body>
  <?include("../includes/source/top.php");?>
  <div class="workspace">
   <div class="contents">
    <?
    if(isset($_GET['id']) && $_GET['id']!=""){
     $appsURI=load(L_SERVER."/core/appCenter.php", array(
      "get" => "app",
      "id" => $_GET['id']
     ), "POST");
     if($appsURI=="false"){
      ser("Error", "App With the give Id does not exist");
     }
     $apps=json_decode($appsURI, true);
     $apps=$apps[$_GET['id']];
     $appImage=isset($apps['image']) ? L_HOST."/includes/source/img/blank_app.png":$apps['image'];
    ?>
    <h2><?echo$apps['name'];?></h2>
    <div style="width:500px;"></div>
    <div id="leftpane" style="float:left;margin-right:45px;display:inline-block;width:87px;">
     <img src="<?echo$appImage;?>" height="120" width="120"/>
     <div clear></div>
     <a href="<?echo$apps['appURL'];?>" target="_blank" class="button">App Page</a>
     <div clear></div>
     <?
     $App = new App($_GET['id']);
     if($App->exists===false){
     ?>
      <a href="<?echo L_HOST;?>/admin/install-app.php?id=<?echo$_GET['id'];?>" class="button">Install</a>
     <?
     }else{
     ?>
      <a href="<?echo $App->getURL();?>" class="button">Open App</a>
     <?
     }
     ?>
     <style>#leftpane .button{width:100%;}</style>
    </div>
    <div style="display:inline-block;margin-top:-15px;">    
     <h3>Version</h3>
     <?echo$apps['version'];?>. Updated On <?echo$apps['updated'];?>
     <h3>Description</h3>
     <p style="max-width: 300px;">
     <?echo$apps['description'];?>
     </p>
     <h3>Author</h3>
     <a href="<?echo$apps['authorURL'];?>" target="_blank"><?echo$apps['authorName'];?></a>
    </div>
    <?
     exit;
    }
    ?>
    <h2>App Center</h2>
    <p>Find Great New Apps</p>
    <div clear></div>
    <form method="GET" action="<?echo L_HOST?>/admin/appCenter.php">
     <input type="text" placeholder="Type an app name" name="q" style="width:450px;"/>
     <button>Search</button>
    </form>
    <?
    $appsURI=load(L_SERVER."/core/appCenter.php", array(
     "get" => "newApps"
    ), "POST");
    if($appsURI=="false" || $appsURI==""){
     ser("Nothing Found", "Nothing was found that matches your criteria. Sorry");
    }
    $apps=json_decode($appsURI, true);
    if(!is_array($apps)){
     ser("Sorry", "The Lobby Server is experiencing some problems. Please Try again.");
    }
    foreach($apps as $appId=>$appArray){
     $appImage=isset($appArray['image']) ? L_HOST."/includes/source/img/blank_app.png":$appArray['image'];
    ?>
    <div class="app">
     <div class="left">
      <a href="?id=<?echo$appId;?>"><img src="<?echo $appImage;?>" height="120" width="120"/></a>
      <div clear></div>
      <?
      $App = new App($appId);
      if($App->exists===false){
      ?>
      <a href="<?echo L_HOST;?>/admin/install-app.php?id=<?echo$appId;?>" style="width:100%;" class="button">Install</a>
      <?}else{
      ?>
       <a href="<?echo $App->getURL();?>" class="button" style="text-align:center;width:100%;">Open App</a>
      <?
      }
      ?>
     </div>
     <div class="right">
      <a href="?id=<?echo$appId;?>"><div class="title"><?echo $appArray['name'];?></div></a>
      <div class="description"><?echo $appArray['description'];?></div>
      <div class="info">
       By <a href="<?echo $appArray['authorURL'];?>" target="_blank"><?echo $appArray['authorName'];?></a> |
       Version <?echo $appArray['version'];?> |
       <a href="<?echo$appArray['appURL'];?>" target="_blank">App Page</a> |
       Updated <?echo date("Y-m-d", strtotime($appArray['updated']));?>
      </div>
     </div>
    </div>
    <?
    }
    ?>
   </div>
  </div>
 </body>
</html>
