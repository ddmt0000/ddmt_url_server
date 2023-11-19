<?php
include("route/mysql.php");
$get_mod = $_GET['mod'];
$user_ip = $_SERVER["REMOTE_ADDR"];
//url_add($conn,"127.0.0.1","http://127.0.0.1/krESMA","http://127.0.0.1/86484866489479+4649849");
//echo url_find_orange($conn,"http://127.0.0.1/54354")."\n";
// echo "当前网站共记录短链接" . url_all($conn) . "条<br>\n";
// echo "是否符合短链接：".find_RandomString($nount)."<br>\n";
// echo "该链接是否已经存在于数据库：".url_examine_orange($conn,$_GET['url_orange'])."<br>";
// echo "来源ip：".$user_ip." url加密预览：".urlencode($_GET['url_orange'])."<br>";
//添加记录值模式
function page_active($pagename)
{
    if ($_SERVER['QUERY_STRING'] == $pagename) {
        return "active";
    } elseif ($pagename == '') {
        return $_SERVER['QUERY_STRING'];
    }
}
function page_mian()
{
    if (file_exists("view/" . $_SERVER['QUERY_STRING'])) {
        include_once("view/" . $_SERVER['QUERY_STRING']);
    } else {
        include_once("home.htm");
    }
}
function home_list()
{
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
} elseif (find_RandomString($_SERVER['QUERY_STRING']) == 1) { //查询模式
    $url_orange = url_find_orange($_SERVER['QUERY_STRING']);
    if ($url_orange == null) {
        include_once('home.htm');
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
            $rejson = array('code' => 0, 'msg' => "管理员没有允许这个权限");
            exit(json_encode($rejson));
        }
    } else {
        $rejson = array('code' => 0, 'msg' => "错误的格式");
        exit(json_encode($rejson));
    }
} else {
    include_once('home.htm');
}
mysqli_close($conn);
