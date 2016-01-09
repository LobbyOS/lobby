<?php
$this->load();

if(isset($_POST['column'])){
  $sql = $this->dbh->prepare("ALTER TABLE {$this->table} DROP COLUMN {$_POST['column']}");
  if($sql->execute()){
    echo "1";
  }else{
    $this->log($sql->errorInfo());
  }
}
