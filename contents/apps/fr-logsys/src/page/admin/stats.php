<?php
require_once APP_DIR . "/src/inc/partial/layout.php";
?>
<div class='contentLoader'>
  <h1>Statistics</h1>
  <?php
  if($this->set){
    $this->load();
    
    $inMonth = $this->registeredInAMonth();
    $perMonthRaw = $this->dbh->query("SELECT MONTH(`created`), COUNT(`created`) FROM `{$this->table}` WHERE `created` >= NOW() - INTERVAL 1 YEAR GROUP BY MONTH(`created`)")->fetchAll();

    $perMonth = array();
    for($i=1;$i < 13;$i++){
      $perMonth[$i] = 0;
    }
    foreach($perMonthRaw as $k => $v){
      $perMonth[$v[0]] = $v[1];
    }
    
    $perDayRaw = $this->dbh->query("SELECT DAY(`created`), COUNT(`created`) FROM `{$this->table}` WHERE `created` >= NOW() - INTERVAL 1 MONTH GROUP BY DAY(`created`)")->fetchAll();

    $perDay = array();
    for($i=1;$i <= date("d");$i++){
      $perDay[$i] = 0;
    }
    foreach($perDayRaw as $k => $v){
      $perDay[$v[0]] = $v[1];
    }
  ?>
    <p>Users Registered In This Month (<?php echo date("M");?>) : <b><?php echo $inMonth;?></b></p>
    <div clear class='chartbox'>
      <canvas id="monthChart" width="400" height="400"></canvas>
      <p>Users Registered Per Month</p>
    </div>
    <div clear class='chartbox'>
      <canvas id="dayChart" width="600" height="400"></canvas>
      <p>Users Registered Per Day In This Month</p>
    </div>
    <style>
      .chartbox{
        display: inline-block;
        text-align:center;
        width: 400px;
        height: 400px;
      }
    </style>
  <?php
    \Lobby::hook("head.end", function() use($perMonth, $perDay){
      echo '<script>lobby.load(function(){lobby.app.stats('. json_encode(array_values($perMonth)) .', '. json_encode(array_values($perDay)) .', '. json_encode(array_keys($perDay)) .');});</script>';
    });
  }else{
  ?>
    <a href='<?php echo APP_URL;?>/admin/config' class='button red'>Setup logSys Admin</a>
  <?php
  }
  ?>
</div>
<?php require_once APP_DIR . "/src/inc/partial/layout_footer.php";?>
