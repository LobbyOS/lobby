<?php
if(isset($_GET['logout'])){
  \Fr\LS::logout();
  \Lobby::redirect("/#");
}else{
  /**
   * User is logged in, so redirect to Admin main page
   */
  if(\Fr\LS::$loggedIn){
    \Lobby::redirect("/admin");
  }
}
if(isset($_POST["username"]) && isset($_POST["password"])){
  $user = $_POST["username"];
  $pass = $_POST["password"];
  if($user == "" || $pass == ""){
    $error = array("Username / Password Wrong", "The username or password you submitted was wrong.");
  }else{
    $login = \Fr\LS::login($user, $pass, isset($_POST['remember_me']));
    if($login === false){
      $error = array("Username / Password Wrong", "The username or password you submitted was wrong.");
    }else if(is_array($login) && $login['status'] == "blocked"){
      $error = array("Account Blocked", "Too many login attempts. You can attempt login again after ". $login['minutes'] ." minutes (". $login['seconds'] ." seconds)");
    }else{
      \Lobby::redirect("/admin");
    }
  }
}
?>
<html>
  <head>
    <?php
    \Lobby::doHook("admin.head.begin");
    \Lobby::head("Admin Login");
    ?>
  </head>
  <body>
    <?php \Lobby::doHook("admin.body.begin");?>
    <div class="workspace">
      <div class="contents">
        <h2>Log In</h2>
        <form method="POST" action="<?php echo \Lobby::u("/admin/login");?>">
          <label clear>
            <span clear>Username</span>
            <input clear type="text" name="username" value="<?php if(isset($_POST['username'])){echo $_POST['username'];}?>" />
          </label>
          <label clear>
            <span clear>Password</span>
            <input clear type="password" name="password" id="password" />
            <?php if(isset($_POST['username'])){echo "<script>$('#password').focus()</script>";}?>
          </label>
          <label clear>
            <input type="checkbox" name="remember_me" checked="checked" />
            <span>Remember Me</span>
          </label>
          <button class="btn" clear>Log In</button>
        </form>
        <?php
        if(isset($error)){
          \Lobby::echo ser($error[0], $error[1], false);
        }
        ?>
        <div>
          &copy; <a target="_blank" href="http://lobby.subinsb.com">Lobby</a> <?php echo date("Y");?>
        </div>
      </div>
    </div>
  </body>
</html>
