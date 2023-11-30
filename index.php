<?php
// 设置 cookie 的有效时间为 1 天
session_start([
    'cookie_lifetime' => 259200,
]);
include("route/mysql.php");
$get_mod = $_GET['mod'];
$user_ip = $_SERVER["REMOTE_ADDR"];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_SERVER['QUERY_STRING'];
//action请求全部参数
//method请求方式

//退出登录
function loginOut(){
    unset($_SESSION['username']);
    // header('Location : /index.php');
}

//维持登录状态
if(isset($_SESSION['username'])){
    $user_name=$_SESSION['username'];
}

//添加记录值模式
function page_active($pagename){
    if ($_SERVER['QUERY_STRING'] == $pagename) {
        return "active";
    } elseif ($pagename == '') {
        return $_SERVER['QUERY_STRING'];
    }
}
function page_mian(){
    if (file_exists("view/" . $_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != "") {
        include_once("view/" . $_SERVER['QUERY_STRING']);
    } else {
        include_once("view/home.htm");
    }
}
function home_list(){
    global $user_ip;
    $url = array_reverse(url_find_ip($user_ip));
    if (count($url) < 20) {
        $list_s = count($url);
    } else {
        $list_s = 20;
    }
    for ($i = 0; $i < $list_s; $i++) {
        echo "<mdui-list-item rounded><mdui-icon name='link' slot='icon'></mdui-icon><mdui-icon name='content_copy' id='end_icon_$i' slot='end-icon'></mdui-icon>" . urldecode($url[$i]) . "</mdui-list-item>\n";
    }
}
if ($get_mod == "add") {
    header('Content-Type:application/json; charset=utf-8');
    if (!isset($_GET['url_orange']) || empty($_GET['url_orange']) || filter_var($_GET['url_orange'], FILTER_VALIDATE_URL)) {
        $randomcode = generateRandomString(6);
        //排除出现相同的code
        while (url_examine_orange($randomcode) != null) {
            $randomcode = generateRandomString(6);
        }
        if ($_GET['url_orange'] == "") {
            $rejson = array('code' => 0, 'msg' => "不能为空");
            exit(json_encode($rejson));
        }
        $or_url = url_examine_orange($_GET['url_orange']);
        if ($or_url == null) {
            url_add($user_ip, $randomcode, $_GET['url_orange']);
            $rejson = array('code' => 1, 'newurl' => "http://" . $_SERVER['HTTP_HOST'] . "/?" . $randomcode, 'msg' => "成功");
            exit(json_encode($rejson));
        } else {
            $rejson = array('code' => 1, 'newurl' => "http://" . $_SERVER['HTTP_HOST'] . "/?" . $or_url, 'msg' => "已经存在你可以继续使用");
            exit(json_encode($rejson));
        }
    } else {
        $rejson = array('code' => 0, 'msg' => "错误的格式");
        exit(json_encode($rejson));
    }
} elseif (find_RandomString($action) == 1) { //查询模式
    $url_orange = url_find_orange($action);
    if ($url_orange == null) {
        include_once('index.htm');
    } else {
        include_once('Jump.htm');
    }
} elseif ($get_mod == "del") {
    header('Content-Type:application/json; charset=utf-8');
    if (!isset($_GET['url_orange']) || empty($_GET['url_orange']) || filter_var($_GET['url_orange'], FILTER_VALIDATE_URL)) {
        if ($_GET['url_orange'] == "") {
            $rejson = array('code' => 0, 'msg' => "不能为空");
            exit(json_encode($rejson));
        }
        $or_url = url_examine_orange($_GET['url_orange']);
        if ($or_url == null) {
            $rejson = array('code' => 0, 'msg' => "不存在该url");
            exit(json_encode($rejson));
        } else {
            $or_url_del = url_delete_orange($_GET['url_orange']);
            if ($or_url_del){
                $rejson = array('code' => 1, 'msg' => "删除已成功");
            }else{
                $rejson = array('code' => 0, 'msg' => "删除是失败的");
            }
            exit(json_encode($rejson));
        }
    } else {
        $rejson = array('code' => 0, 'msg' => "错误的格式");
        exit(json_encode($rejson));
    }

//注册
}elseif($action == "user_create"){
    header('Content-Type:application/json; charset=utf-8');
    if($method == "GET"){
        include_once('index.htm');
    }elseif($method == "POST"){
        $user_name = $_POST["username"];
        $user_password = $_POST["password"];
        if(!empty($user_name) && !empty($user_password)){//不能为空
            //preg_match('/^[\w_-]{6,16}$/', $user_password)===1
            if(preg_match('/^[\u4e00-\u9fa5a-zA-Z0-9]{6,12}$/', $user_name)===1 && preg_match('/^(?=.{32}$)(?![0-9]+$)(?![a-f]+$)[a-f0-9]+$/', $user_password)===1){//省时省心省力，直接正则
                $recreate = user_add($user_name,$user_password,$user_ip,1);
                if($recreate===true){
                    $_SESSION['username'] = $user_name;
                    //isset($_SESSION['username'])
                    $rejson = array('code' => 1, 'msg' => "注册成功，已自动登录");
                    exit(json_encode($rejson));
                }elseif($recreate===null){
                    $rejson = array('code' => 0, 'msg' => "用户名已占用");
                    exit(json_encode($rejson));
                }
            }else{
                $rejson = array('code' => 0, 'msg' => "参数格式错误");
                exit(json_encode($rejson));
            }
        }else{
            $rejson = array('code' => 0, 'msg' => "用户名密码不能为空");
            exit(json_encode($rejson));
        }
    }else{
        include_once('index.htm');//不支持的请求方式
    }

//登录
}elseif($action == "user_login"){
    header('Content-Type:application/json; charset=utf-8');
    if($method == "GET"){
        include_once('index.htm');
    }elseif($method == "POST"){
        $user_name = $_POST["username"];
        $user_password = $_POST["password"];
        if(!empty($user_name) && !empty($user_password)){//不能为空
            //preg_match('/^[\w_-]{6,16}$/', $user_password)===1
            if(preg_match('/^[\u4e00-\u9fa5a-zA-Z0-9]{6,12}$/', $user_name)===1 && preg_match('/^(?=.{32}$)(?![0-9]+$)(?![a-f]+$)[a-f0-9]+$/', $user_password)===1){//省时省心省力，直接正则
                $relogin = user_login($user_name,$user_password,$user_ip);
                
                if($relogin===1){
                    $_SESSION['username'] = $user_name;
                    //isset($_SESSION['username'])
                    $rejson = array('code' => 1, 'msg' => "登陆成功");
                    exit(json_encode($rejson));
                }elseif($relogin===-1){
                    $rejson = array('code' => 0, 'msg' => "账号密码错误");
                    exit(json_encode($rejson));
                }elseif($relogin===-2){
                    $rejson = array('code' => 0, 'msg' => "用户被禁止");
                    exit(json_encode($rejson));
                }elseif($relogin===-3){
                    $rejson = array('code' => 0, 'msg' => '找不到用户');
                    exit(json_encode($rejson));
                }else{
                    $rejson = array('code' => 0, 'msg' => 'error:'.$relogin);
                    exit(json_encode($rejson));
                }
            }else{
                $rejson = array('code' => 0, 'msg' => "参数格式错误");
                exit(json_encode($rejson));
            }
        }else{
            $rejson = array('code' => 0, 'msg' => "用户名密码不能为空");
            exit(json_encode($rejson));
        }
    }else{
        include_once('index.htm');//不支持的请求方式
    }

}elseif($action == "user_loginout"){
    include_once('loginout.htm');
} else {
    include_once('index.htm');
}
mysqli_close($conn);
