<?php
include "config.php";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="bootstrap/docs/assets/ico/favicon.ico">

    <title>YubiKeys</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/docs/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="bootstrap/docs/dist/css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="bootstrap/docs/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

<script src="js/jquery.min.js"></script>

    <script language="javascript">
        function ykEnable(nickname)
        {
            window.location.replace('?action=enable&nickname='+nickname);
        }

        function ykDisable(nickname)
        {
            window.location.replace('?action=disable&nickname='+nickname);
        }

        function ykDelete(nickname)
        {
            if(confirm("Are you sure you want to delete the Yubikey entry for "+nickname+" ?"))
                window.location.replace('?action=delete&nickname='+nickname);
        }
        function ykHistory(nickname, publicid)
        {
            var url="history.php?nickname="+nickname+"&publicid="+publicid;
            window.open(url, "history_popup", "menubar=0, status=no, scrollbars=yes, width=800, height=700");
        }
        function validateAddKey()
        {
            var nickname=$("#ak_nick").val();
            var publicid=$("#ak_pubid").val();
            var privateid=$("#ak_privid").val();
            var aeskey=$("#ak_aes").val();
            var serno=$("#ak_serno").val();

            jQuery.ajax({
              type: 'POST',
              url: 'ajax-addkey.php',
              data: {
                nickname: nickname,
                publicid: publicid,
                privateid: privateid,
                aeskey: aeskey,
                serno: serno
              }, 
              success: function(data, textStatus, jqXHR) {
                  //alert(data);
                if(data=='OK') {
                    $('#modalAddKey').modal('hide');
                    window.location = "index.php";
                }else{
                    alert(data);
                }
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  alert('Action failed');
              }
            });

            return true;
        }
    </script>
  </head>

<body role="document">

<CENTER>
<?php

$dbconn = pg_connect("host='$db_host' dbname='$db_name' user='$db_user' password='$db_pass'")
    or die('Could not connect : ' . pg_last_error());

// Check actions
if(isset($_GET['action'])) {
    $action=$_GET['action'];
    $nickname=pg_escape_string($_GET['nickname']);
    if($action == "enable") {
        pg_query("UPDATE yubikeys SET active='1' WHERE nickname='$nickname';");
    }elseif($action == "disable") {
        pg_query("UPDATE yubikeys SET active='0' WHERE nickname='$nickname';");
    }elseif($action == "delete") {
        pg_query("DELETE FROM yubikeys WHERE nickname='$nickname';");
    }
}

$query = 'SELECT nickname, publicname, created, internalname, aeskey, serno, active, counter, time FROM yubikeys ORDER BY active DESC, nickname ASC;';
$result = pg_query($query) or die('Query failed : ' . pg_last_error());

echo "<div class=\"table-responsive\">\n";
echo "<table class=\"table table\" border=1 cellpadding=2 cellspacing=2>\n";
echo "<tr><th>Nickname</th><th>Public ID</th><th>Created</th><th>Status</th><th>Counter / Time</th><th>Serial</th><th>Actions</th></tr>";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    $nickname=$line['nickname'];
    $publicid=$line['publicname'];
    $active=$line['active'];
    $counter=$line['counter'];
    $time=$line['time'];
    $serno=$line['serno'];
    $lineattr="CLASS='alert alert-success'";
    $button_enable="disabled";
    $button_disable="";
    $label_active="<span class='label label-success'>active</span>";
    $label_disabled="<span class='label label-danger'>disabled</span>";
    if($active=='0') {
        $lineattr="CLASS='alert alert-danger'";
        $button_enable="";
        $button_disable="disabled";
    }
    echo "\t<tr $lineattr>\n";
    printf("<td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
        $nickname, $publicid, $line['created'], ($active=='1')?$label_active:$label_disabled, "$counter / $time",
        $serno);
    echo "<td>";
    echo "<button type=\"button\" class=\"btn btn-xs btn-default\" ONCLICK=\"ykHistory('$nickname', '$publicid');\">View history</button>";
    echo "<button type=\"button\" $button_enable class=\"btn btn-xs btn-success\" ONCLICK=\"ykEnable('$nickname');\">Enable</button>";
    echo "<button type=\"button\" $button_disable class=\"btn btn-xs btn-primary\" ONCLICK=\"ykDisable('$nickname');\">Disable</button>";
    echo "<button type=\"button\" class=\"btn btn-xs btn-danger\" ONCLICK=\"ykDelete('$nickname');\">Delete</button>";
    echo "</td>\n";
    echo "\t</tr>\n";
}
echo "</table>\n";
echo "</div>\n";
pg_free_result($result);
pg_close($dbconn);
?>
</CENTER>



    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/yubiserve">YubiKeys</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Management <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#" data-toggle='modal' data-target='#modalAddKey'>Add a new key</a></li>
             </ul>
            </li>

          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>


    </div> <!-- /container -->


<!-- Modal -->
<div class="modal fade" id="modalAddKey" tabindex="-1" role="dialog" aria-labelledby="modalAddKeyLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add key</h4>
      </div>
      <div class="modal-body">
        <table class="table table-striped" border=1 cellpadding=2 cellspacing=2>
        <tr><td>Nickname</td><td><input id="ak_nick" type="text"></input></td></tr>
        <tr><td>Public ID</td><td><input id="ak_pubid" type="text"></input></td></tr>
        <tr><td>Private ID</td><td><input id="ak_privid" type="text"></input></td></tr>
        <tr><td>AES key</td><td><input id="ak_aes" type="text"></input></td></tr>
        <tr><td>YubiKey serial</td><td><input id="ak_serno" type="text"></input></td></tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="return validateAddKey();">Save changes</button>
      </div>
    </div>
  </div>
</div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="bootstrap/docs/dist/js/bootstrap.min.js"></script>
    <script src="bootstrap/docs/assets/js/docs.min.js"></script>
  </body>
</html>
