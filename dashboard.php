<?php 
error_reporting(0);
include("./core/functions.php");
session_start();

$isUserLogged = False;
if(isset($_SESSION['logged']) == True){
	$user_agent = $_SESSION['user_agent'];
	$user_ip = $_SESSION['user_ip'];
	$status = session_check($user_agent,$user_ip);
	if(($_SESSION['session_count']-=1) <= 0){
		session_regenerate_id();
		$_SESSION['session_count'] = 5;
	}
	if($status){
		$isUserLogged = True;
	}
	if($_GET['logout'] == 'true'){
		session_destroy();
		header('location: '.$_SERVER['PHP_SELF']);
	}
}
    
if($isUserLogged == False){
    header("Location: ./");
}

if(isset($_POST['generate'])){
    $url = secure_input($_POST['url']);
    $uid = $_SESSION['user_id'];
    addLink($url, $uid);
    header("Location: ".$_SERVER['PHP_SELF']);
}

if(isset($_GET['rm'])){
    $code = $_GET['rm'];
    $uid = $_SESSION['user_id'];
    if(check_link_id($code, $uid)){
        remove_link($code, $uid);
        header("Location: ".$_SERVER['PHP_SELF']);
    }else{
        header("Location: ".$_SERVER['PHP_SELF']);
    }
}

if(isset($_POST['edit'])){
    $code = $_POST['editCode'];
    $new_url = secure_input($_POST['editnv']);
    $uid = $_SESSION['user_id'];
    update_url($code, $new_url,$uid);
    header("Location: ".$_SERVER['PHP_SELF']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <!-- metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- css -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
	
    <!-- scripts -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/111c11b663.js" crossorigin="anonymous"></script>
    <title>Dashboard</title>
</head>
<body>
    <nav class="fixed-top navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand mx-auto" href="./">SMUS</a>
            <div class="">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            Profile
                            <i class="fa-solid fa-address-card"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?logout=confirm">
                            Logout
                            <i class="fa-solid fa-power-off" style="color: #a94432;"></i>
                        </a>
                    </li>
                    
                </ul>
            </div>
        </div>
    </nav>
    <article class="d-flex justify-content-center align-items-center">
        <div class="linkG-wrapper align-items-center text-center">
            <h3>Ninja Links</h3>
            <div class="link-gen">
                <form method="post">
                    <input name="url" type="text" onkeyup="cinp();" id="genInp" autocomplete="off" placeholder="Enter url to be shorten">
                    <input name="generate" type="submit" id="genBtn" disabled class="dizstyle btn mx-2 btn-sm btn-warning" value="Generate">
                </form>
                <!--<p class="text-muted m-2"><i>Short link url: <span><input style="background-color:rgb(0,0,0,0.1);color:green; text-align:center; padding:5px;border-radius:10px;" class="form-control" value="https://<?php echo $_SERVER['HTTP_HOST'];?>/z/short_link_id" readonly></span></i></p>-->
            </div><br>
            <div class="link-track">
                <table class="table table-bordered mt20">
                    <thead>
                        <th class="text-muted">ID</th>
                        <th class="text-muted">Views</th>
                        <!-- <th class="text-muted">Original Url</th> -->
                        <th class="text-muted">Link</th>
                        <th colspan="3" class="text-muted">Controls</th>
                    </thead>
                    <tbody>
<?php
$conn = new mysqli($servername, $username, $password, $dbname);
$uid = $_SESSION['user_id'];
$sql = "SELECT * FROM `links` WHERE uid=$uid";
$query = $conn->query($sql);

$current_dir = basename(__DIR__);
if($current_dir == 'htdocs'){
    $current_dir = '';
}else{
    $current_dir = '/'.$current_dir;
}

while($row = $query->fetch_assoc()){
    $link_id = $row['link_id'];
    $url = $row['url'];

    $shrt_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]!$_SERVER[REQUEST_URI]";
    $shrt_url = explode('!',$shrt_url);
    array_splice($shrt_url,-1);
    $shrt_url = implode("",$shrt_url);
    $shrt_url = $shrt_url.$current_dir.'/'.$link_id;
    #$shrt_url = 'https://'.$_SERVER["HTTP_HOST"].'/z/'.$row["link_id"];
    echo '
                        <tr>
                            <td>'.$row["link_id"].'</td>
                            <td>'.$row["views"].'</td>
                            <td><a href="'.$shrt_url.'">'.$shrt_url.'</td>
                            <td><a class="btn btn-sm btn-outline-primary" onclick="editz(\''.$link_id.'\',\''.$url.'\');">Edit</a></td>
                            <td><a class="btn btn-sm btn-outline-success" target="_blank" href="'.$url.'">View</a></td>
                            <td><a class="btn btn-sm btn-outline-danger" href="?rm='.$link_id.'">Delete</a></td>
                        </tr>';
}
?>
                    </tbody>
                </table>
            </div>
        </div>
    </article>
</body>
<style>

.dizstyle{
    color:rgb(255,255,255,0.6)!important;
    border-color: rgb(0, 0, 0,0.5)!important;
    background-color: rgb(0, 0, 0,0.3)!important;
}

.linkG-wrapper{
    overflow: auto;
    border-radius: 10px;
    padding:10px;
    /*background-color:rgb(0, 0, 0,0.8);*/
    box-shadow: 0px 2px 5px 0px grey;
}
.link-gen input[type=text]{
    padding:3px;
    border:1px solid skyblue;
    border-radius: 5px;
    outline:none;
    background-color:transparent;
}
.link-track{
    overflow: auto;
}
.nav-link{
    margin-left:2px;
    margin-right:2px;
    border-radius:10px;

}
.nav-link:hover{
    transition:.2s;
    box-shadow: 0px 3px 2px 1px rgb(0,0,0,0.6);
    background-color:rgb(0,0,0,0.3);
    transform: translateY(-3px);
}
.nav-link:active{
    box-shadow: none;
    transform: translateY(0px);    
}
article{
    height: 100vh;
}
body{
    margin:0;
    scroll-behavior: smooth;
    height: 100vh;
}


</style>
<script>

function s_msg(icon,title,btnTxt){
    Swal.fire({
        title: title,
        icon: icon,
        showCancelButton:true,
        confirmButtonText: btnTxt,
    }).then((result) =>{
        if(result.isConfirmed){
            window.location.href="?logout=true";
        }
    });
}

function isUrlValid(url){
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}

function cinp(){
    var btn = document.getElementById("genBtn");
    var inp = document.getElementById("genInp");
    if(isUrlValid(inp.value)){
        $(btn).removeAttr("disabled");
        btn.classList.remove("dizstyle");
    }else{
        $(btn).attr("disabled",'');
        btn.classList.add("dizstyle");
    }
}

<?php 
if($_GET['logout'] == 'confirm'){echo "s_msg('warning','Do you really want to logout?','Yes');";}
?>

function editz(link_id,url){
    Swal.fire({
        title: 'Edit Link Value',
        html: `
        <form method="post">
            <input hidden name="editCode" value="`+link_id+`">
            <input class="form-control" type="text" name="editnv" value="`+url+`" autocomplete="off">
            <input class="btn btn-success m-3" type="submit" name="edit" value="Save">
            <input class="btn btn-secondary m-3" type="button" onclick="swal.close();" value="Cancel">
        </form>
        `,
        showConfirmButton: false,
        
        });
}

</script>
</html>
