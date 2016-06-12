<?php require "../load.php";?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Response::head("Modules");
    ?>
  </head>
  <body>
    <?php
    \Lobby::doHook("admin.body.begin");
    require "$docRoot/admin/inc/sidebar.php";
    ?>
    <div id="workspace">
      <div class="content">
        <h1>Modules</h1>
        <p>Modules extend the functionality of Lobby. This page shows the modules that are installed in Lobby. <a target="_blank" href="<?php echo L_SERVER;?>/mods">Read more about Modules</a></p>
        <?php
        $core_modules = \Lobby\Modules::get("core");
        $custom_modules = \Lobby\Modules::get("custom");
        $app_modules = \Lobby\Modules::get("app");

        echo "<h3>Custom Modules</h3>";
        echo "Manually installed modules are 'custom modules'";
        if(count($custom_modules) == 0){
          echo ser("No Custom Modules", "No custom modules are enabled or installed", false);
        }else{
          echo "<ul>";
          foreach($custom_modules as $module){
            echo "<li data-loc='{$module['location']}'>{$module['id']}</li>";
          }
          echo "</ul><p>To disable a <b>custom module</b>, create a 'disabled.txt' file in the module directory</p>";
        }
        
        echo "<h3>App Modules</h3>";
        echo "<p>The modules loaded by Apps</p>";
        if(count($app_modules) == 0){
          echo ser("No App Modules", "No app's modules are enabled or installed", false);
        }else{
          echo "<ul>";
          foreach($app_modules as $module){
            echo "<li data-loc='{$module['location']}'>{$module['id']}</li>";
          }
          echo "</ul>";
        }
        
        echo "<h3>Core Modules</h3>";
        echo "<p>These modules can't be removed and is part of the Lobby Core.</p>";
        echo "<ul>";
        foreach($core_modules as $module){
          echo "<li data-loc='{$module['location']}'>{$module['id']}</li>";
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
