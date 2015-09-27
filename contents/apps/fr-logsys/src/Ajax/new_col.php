<?php
$this->load();

if(isset($_POST['column'])){
  foreach($_POST['column'] as $column){
    if($column['length'] == ""){
      $sql = $this->dbh->prepare("ALTER TABLE `". $this->table ."` ADD {$column['name']} {$column['type']}");
    }else{
      $sql = $this->dbh->prepare("ALTER TABLE `". $this->table ."` ADD {$column['name']} {$column['type']}({$column['length']})");
    }
    if($sql->execute()){
      sss("Column Added", "The column <b>{{$column['name']}}</b> was successfully added");
    }else{
      $this->log($sql->errorInfo());
      ser("Failed", "Some error caused the column to be not created");
    }
  }
}

$field = str_replace(array("\r","\n"), "", ("<tr>
  <td><input type='text' name='column[0][name]' id='column_name' /></td>
  <td>
    <select name='column[0][type]' class='column_type'>
      <option title='A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295' value='INT'>INT</option><option title='A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size' value='VARCHAR'>VARCHAR</option><option title='A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes' value='TEXT'>TEXT</option><option title='A date, supported range is 1000-01-01 to 9999-12-31' value='DATE'>DATE</option><optgroup label='Numeric'><option title='A 1-byte integer, signed range is -128 to 127, unsigned range is 0 to 255' value='TINYINT'>TINYINT</option><option title='A 2-byte integer, signed range is -32,768 to 32,767, unsigned range is 0 to 65,535' value='SMALLINT'>SMALLINT</option><option title='A 3-byte integer, signed range is -8,388,608 to 8,388,607, unsigned range is 0 to 16,777,215' value='MEDIUMINT'>MEDIUMINT</option><option title='A 4-byte integer, signed range is -2,147,483,648 to 2,147,483,647, unsigned range is 0 to 4,294,967,295' value='INT'>INT</option><option title='An 8-byte integer, signed range is -9,223,372,036,854,775,808 to 9,223,372,036,854,775,807, unsigned range is 0 to 18,446,744,073,709,551,615' value='BIGINT'>BIGINT</option><option disabled='disabled' value='-'>-</option><option title='A fixed-point number (M, D) - the maximum number of digits (M) is 65 (default 10), the maximum number of decimals (D) is 30 (default 0)' value='DECIMAL'>DECIMAL</option><option title='A small floating-point number, allowable values are -3.402823466E+38 to -1.175494351E-38, 0, and 1.175494351E-38 to 3.402823466E+38' value='FLOAT'>FLOAT</option><option title='A double-precision floating-point number, allowable values are -1.7976931348623157E+308 to -2.2250738585072014E-308, 0, and 2.2250738585072014E-308 to 1.7976931348623157E+308' value='DOUBLE'>DOUBLE</option><option title='Synonym for DOUBLE (exception: in REAL_AS_FLOAT SQL mode it is a synonym for FLOAT)' value='REAL'>REAL</option><option disabled='disabled' value='-'>-</option><option title='A bit-field type (M), storing M of bits per value (default is 1, maximum is 64)' value='BIT'>BIT</option><option title='A synonym for TINYINT(1), a value of zero is considered false, nonzero values are considered true' value='BOOLEAN'>BOOLEAN</option><option title='An alias for BIGINT UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE' value='SERIAL'>SERIAL</option></optgroup><optgroup label='Date and time'><option title='A date, supported range is 1000-01-01 to 9999-12-31' value='DATE'>DATE</option><option title='A date and time combination, supported range is 1000-01-01 00:00:00 to 9999-12-31 23:59:59' value='DATETIME'>DATETIME</option><option title='A timestamp, range is 1970-01-01 00:00:01 UTC to 2038-01-09 03:14:07 UTC, stored as the number of seconds since the epoch (1970-01-01 00:00:00 UTC)' value='TIMESTAMP'>TIMESTAMP</option><option title='A time, range is -838:59:59 to 838:59:59' value='TIME'>TIME</option><option title='A year in four-digit (4, default) or two-digit (2) format, the allowable values are 70 (1970) to 69 (2069) or 1901 to 2155 and 0000' value='YEAR'>YEAR</option></optgroup><optgroup label='String'><option title='A fixed-length (0-255, default 1) string that is always right-padded with spaces to the specified length when stored' value='CHAR'>CHAR</option><option title='A variable-length (0-65,535) string, the effective maximum length is subject to the maximum row size' value='VARCHAR'>VARCHAR</option><option disabled='disabled' value='-'>-</option><option title='A TEXT column with a maximum length of 255 (2^8 - 1) characters, stored with a one-byte prefix indicating the length of the value in bytes' value='TINYTEXT'>TINYTEXT</option><option title='A TEXT column with a maximum length of 65,535 (2^16 - 1) characters, stored with a two-byte prefix indicating the length of the value in bytes' value='TEXT'>TEXT</option><option title='A TEXT column with a maximum length of 16,777,215 (2^24 - 1) characters, stored with a three-byte prefix indicating the length of the value in bytes' value='MEDIUMTEXT'>MEDIUMTEXT</option><option title='A TEXT column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) characters, stored with a four-byte prefix indicating the length of the value in bytes' value='LONGTEXT'>LONGTEXT</option><option disabled='disabled' value='-'>-</option><option title='Similar to the CHAR type, but stores binary byte strings rather than non-binary character strings' value='BINARY'>BINARY</option><option title='Similar to the VARCHAR type, but stores binary byte strings rather than non-binary character strings' value='VARBINARY'>VARBINARY</option><option disabled='disabled' value='-'>-</option><option title='A BLOB column with a maximum length of 255 (2^8 - 1) bytes, stored with a one-byte prefix indicating the length of the value' value='TINYBLOB'>TINYBLOB</option><option title='A BLOB column with a maximum length of 16,777,215 (2^24 - 1) bytes, stored with a three-byte prefix indicating the length of the value' value='MEDIUMBLOB'>MEDIUMBLOB</option><option title='A BLOB column with a maximum length of 65,535 (2^16 - 1) bytes, stored with a two-byte prefix indicating the length of the value' value='BLOB'>BLOB</option><option title='A BLOB column with a maximum length of 4,294,967,295 or 4GiB (2^32 - 1) bytes, stored with a four-byte prefix indicating the length of the value' value='LONGBLOB'>LONGBLOB</option><option disabled='disabled' value='-'>-</option><option title='An enumeration, chosen from the list of up to 65,535 values or the special '' error value' value='ENUM'>ENUM</option><option title='A single value chosen from a set of up to 64 members' value='SET'>SET</option></optgroup><optgroup label='Spatial'><option title='A type that can store a geometry of any type' value='GEOMETRY'>GEOMETRY</option><option title='A point in 2-dimensional space' value='POINT'>POINT</option><option title='A curve with linear interpolation between points' value='LINESTRING'>LINESTRING</option><option title='A polygon' value='POLYGON'>POLYGON</option><option title='A collection of points' value='MULTIPOINT'>MULTIPOINT</option><option title='A collection of curves with linear interpolation between points' value='MULTILINESTRING'>MULTILINESTRING</option><option title='A collection of polygons' value='MULTIPOLYGON'>MULTIPOLYGON</option><option title='A collection of geometry objects of any type' value='GEOMETRYCOLLECTION'>GEOMETRYCOLLECTION</option></optgroup>
    </select>
  </td>
  <td><input type='number' name='column[0][length]' id='column_length' placeholder='Leave empty for using default length' /></td>
</tr>"));
?>
<form id="newColumn">
  <table><thead>
    <th>Name</th>
    <th>Type</th>
    <th>Length</th>
  </thead><tbody>
    <?php echo $field;?>
  </tbody></table>
  <button>Add Column(s)</button>
</form>
<style>
  form label{
    display: block;
    margin-bottom: 10px;
  }
  .overflow{
    max-height: 200px;
  }
</style>
<script>
  $("#newColumn #newField").live("click", function(){
    i = typeof $("#newColumn table tr:last").data("no") == "undefined" ? 1 : parseFloat($("#newColumn table tr:last").data("no")) + 1;
    
    h = "<?php echo $field;?>".replace(/\[0\]/g, "["+ i +"]");
    clog(i);
    $("#newColumn table tr:last").after($(h).data("no", i));
    $(this).remove();
    selectMenu();
  });
  
  $("form#newColumn").die("submit").live("submit", function(){
    event.preventDefault();
    valid = {
      "VARCHAR": function(){
        if($("#column_length").val() != ""){
          return true;
        }else{
          alert("Please enter valid Length");
          return false;
        }
      },
      "CHAR": function(){
        if($("#column_length").val() != ""){
          return true;
        }else{
          alert("Please enter valid Length");
          return false;
        }
      }
    };
    
    if($("#column_name").val() == ""){
      alert("Type in a Column Name");
    }else if(typeof valid[$(this).find("option:selected").val()] == "undefined" || (typeof valid[$(this).find("option:selected").val()] != "undefined" && valid[$(this).find("option:selected").val()]() == true)){
      $("<a class='dialog'></a>").data({"params": $(this).serialize(), "dialog": "new_col.php"}).appendTo(".workspace").click();
    }
  });
  
  $(".column_type").selectmenu({
    open: function( event, ui ) {
      $(this).css("z-index", 999);
      $('li.ui-menu-item').tooltip({
          items:'li',
          content: function(){
            return $("#column_type").find("option[value="+ $(this).text() +"]").attr("title");
          }
      });
    }
  }).selectmenu("menuWidget").css("max-height", "200px");
</script>
