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
    <link href="tables.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="bootstrap/docs/assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  </head>

  <body role="document">

<CENTER>
<?php
include "config.php";

$dbconn = pg_connect("host='$db_host' dbname='$db_name' user='$db_user' password='$db_pass'")
    or die('Could not connect : ' . pg_last_error());

// Check actions
$publicid="";
$nickname="";
if(!isset($_GET['publicid'])) {
    die("No ID specified");
}
if(!isset($_GET['nickname'])) {
    die("No nickname specified");
}
$publicid=pg_escape_string($_GET['publicid']);
$nickname=pg_escape_string($_GET['nickname']);
$query = "SELECT actiontimestamp, result FROM yubikeys_audit WHERE publicname='" . $publicid . "' ORDER BY actiontimestamp DESC;";
$result = pg_query($query) or die('Query failed : ' . pg_last_error());

echo "<div class=\"table-responsive\">\n";
echo "<table class=\"table table\" border=1 cellpadding=2 cellspacing=2>\n";
echo "<tr><th>Timestamp</th><th>Result</th></tr>";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    $timestamp=$line['actiontimestamp'];
    $lresult=$line['result'];
    $lineattr="CLASS='alert alert-success'";
    if($lresult!='OK') {
        $lineattr="CLASS='alert alert-danger'";
    }
    echo "\t<tr $lineattr>\n";
    printf("<td>%s</td><td>%s</td>",
        $timestamp, $lresult);
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
          <a class="navbar-brand" href="#">YubiKeys - History for <?php echo "$nickname ($publicid)"; ?></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="javascript:window.close();"><span class="glyphicon glyphicon-remove"></span> Close window</a></li>
         </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="bootstrap/docs/dist/js/bootstrap.min.js"></script>
    <script src="bootstrap/docs/assets/js/docs.min.js"></script>
  </body>
</html>
