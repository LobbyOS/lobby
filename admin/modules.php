<?php include "../load.php";?>
<html>
  <head>
    <?php \Lobby::head("Modules");?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    include "$docRoot/admin/sidebar.php";
    ?>
    <div class="workspace">
      <div class="content">
        <h1>Modules</h1>
        <p>Modules extend the functionality of Lobby. This page shows the modules that are installed in Lobby.<a clear target="_blank" href="<?php echo L_SERVER;?>/../mods">Read more about Modules</a></p>
        <?php
        $core_modules = \Lobby\Modules::get("core");
        $custom_modules = \Lobby\Modules::get("custom");
        $app_modules = \Lobby\Modules::get("app");

        echo "<h2>Custom Modules</h2>";
        if(count($custom_modules) == 0){
          ser("No Custom Modules", "No custom modules are enabled or installed", false);
        }else{
          echo "<ul>";
          foreach($custom_modules as $module => $loc){
            echo "<li data-loc='$loc'>$module</li>";
          }
          echo "</ul><p>To disable a <b>custom module</b>, create a 'disabled.txt' file in the module directory</p>";
        }
        
        echo "<h2>App Modules</h2>";
        if(count($app_modules) == 0){
          ser("No App Modules", "No app's modules are enabled or installed", false);
        }else{
          echo "<ul>";
          foreach($app_modules as $module => $loc){
            echo "<li data-loc='$loc'>$module</li>";
          }
          echo "</ul>";
        }
        
        echo "<h2>Core Modules</h2><ul>";
        foreach($core_modules as $module => $loc){
          echo "<li data-loc='$loc'>$module</li>";
        }
        echo "</ul>";
        ?>
        <div id="dialog-message"><p></p></div>
        <script>
        $(window).load(function(){
          $(".content li").live("click", function(){
            $("#dialog-message p").html($(this).data("loc"));
            $( "#dialog-message" ).dialog({
              modal: true,
              width: 500,
              buttons: {
                Ok: function() {
                  $( this ).dialog( "close" );
                }
              }
            });
          });
        });
        </script>
      </div>
    </div>
  </body>
</html>
