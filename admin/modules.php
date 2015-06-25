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
        <h2>Modules</h2>
        <p>Here are the active modules :</p>
        <?php
        $core_modules = \Lobby\Modules::get("core");
        $custom_modules = \Lobby\Modules::get("custom");
        ?>
        <ul>
          <?php
          echo "<h2>Custom Modules</h2>";
          if(count($custom_modules) == 0){
            ser("No Custom Modules", "No custom modules is enabled or installed", false);
          }else{
            foreach($custom_modules as $module => $loc){
              echo "<li data-loc='$loc'>$module</li>";
            }
            echo "</ul><p>To disable a <b>custom module</b>, create a 'disabled.txt' file in the module directory</p><ul>";
          }
          echo "<h2>Core Modules</h2>";
          foreach($core_modules as $module => $loc){
            echo "<li data-loc='$loc'>$module</li>";
          }
          ?>
        </ul>
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
