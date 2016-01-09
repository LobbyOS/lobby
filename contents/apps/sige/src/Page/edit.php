<?php
$this->addScript("editor.js");
/**
 * $name is for the Site Name
 * $pname is for the Page Name
 */
?>
<script src="<?php echo $this->u("/src/lib/tinymce/tinymce.min.js");?>"></script>
<div class="contents">
  <h2>Create Page</h2>
  <?php
  $data = array(
    "name" => "",
    "title" => "",
    "slug" => "",
    "body" => ""
  );
  $valid = false;
  
  if(isset($_GET['id'])){
    $data = $this->getPages($name, $_GET['id']);
    $valid = $data['title'] == "" ? false : true;
  }
  
  if(isset($_POST['submit'])){
    $pname = $valid === true  ? $_GET['id'] : $_POST['name'];
    $title = $_POST['title'];
    $body = $_POST['content'];
    $slug = $_POST['slug'];
    
    if($pname == "" || $body == "" || $title == "" || $slug == ""){
      \Lobby::ser("Fill Up", "Please fill up all the fields");
    }else if( !ctype_alnum(str_replace(" ", "", $pname)) ){
      \Lobby::ser("Invalid Name", "The page name should only contain alphanumeric characters");
    }else{
      $gSite = new \Lobby\App\sige\Site($site, $this);
      $page = $gSite->page($slug, array(
        "{{page-title}}" => $title,
        "{{page-content}}" => $body
      ));
      if($page === true){
        $this->addPage($name, $pname, array(
          "title" => $title,
          "slug" => $slug,
          "body" => $body
        ));
        \Lobby::sss( "Page Updated", "The page was successfully " . ($valid ? "updated" : "created") );
      }else{
        \Lobby::ser("Error", "Some error was occured while creating the page. Try again.");
      }
      $data = $this->getPages($name, $pname);
    }
  }
  ?>
  <form method="POST" action="<?php echo \Lobby::u();?>" style="width: 700px;">
    <?php
    if( $data['title'] == ""){
    ?>
      <p>Create a new page in <strong><?php echo $name;?></strong></p>
      <label title="Should only contain alphanumeric + space characters">
        <div>Name</div>
        <input type="text" name="name" value="<?php echo $data['name'];?>" />
      </label>
    <?php
    }else{
      echo "<input type='hidden' name='update' value='true' />";
    }
    ?>
    <label title="The <title> of the page. Not applicable to Plain File type">
      <div>Title</div>
      <input type="text" name="title" value="<?php echo $data['title'];?>" />
    </label>
    <label title="Path of page. Example : 'myFolder/myFile' . The default extension is .html">
      <div>Location</div>
      <input type="text" name="slug" value="<?php echo $data['slug'];?>" />
    </label>
    <label>
      <div>Content</div>
      <textarea name="content" style="height: 200px;width: 700px;"><?php echo $data['body'];?></textarea>
    </label>
    <div clear style="text-align: right;">
      <button name="submit"><?php echo $data['title'] == "" ? "Create Page" : "Update Page" ?></button>
    </div>
  </form>
  <style>
    .workspace#sige label{
      display: block;
      margin-bottom: 20px;
    }
  </style>
</div>
