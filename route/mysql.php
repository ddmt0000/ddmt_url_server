<?php
$servername = "localhost";
$username = "ddmt_urlsrver";
$password = "000000";
$dbname = "ddmt_urlsrver";//链接创建
$conn = new mysqli($servername, $username, $password, $dbname);//检测链接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
//天界记录
function url_add($url_ip, $url_new, $url_orange)
{
    global $conn;
    $url_orange = urlencode($url_orange);
    $submission_date = date("Y-m-d H:i:s"); // 当前日期时间
    $sql = "INSERT INTO url_li (url_orange, url_new, url_ip, submission_date)
        VALUES ('$url_orange', '$url_new', '$url_ip', '$submission_date')";
    // 执行插入语句
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return $sql . ";" . $conn->error;
    }
}
//通过new寻找原始url
function url_find_orange($url_new)
{
    global $conn;
    // 创建查询语句
    $sql = "SELECT url_orange FROM url_li WHERE url_new = '$url_new'";    // 执行查询语句
    $result = $conn->query($sql);    if ($result->num_rows > 0) {
        // 输出查询结果
        while ($row = $result->fetch_assoc()) {
            return urldecode($row["url_orange"]);
        }
    } else {
        return null;
    }
}//通过ip寻找原始url
function url_find_ip($user_ip)
{
    global $conn;
    $sql = "SELECT url_orange FROM url_li WHERE url_ip = '$user_ip'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $urlOranges = array();
        while ($row = $result->fetch_assoc()) {
            $urlOranges[] = $row["url_orange"];
        }
        return $urlOranges;
    } else {
        return array();
    }
}//检查数据库是否存在该url
function url_examine_orange($url_orange)
{
    global $conn;
    // 创建查询语句
    $url_orange = urlencode($url_orange);
    $sql = "SELECT url_new FROM url_li WHERE url_orange = '$url_orange'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return urldecode($row["url_new"]);
        }
    } else {
        return null;
    }
}
//统计总数
function url_all()
{
    global $conn;
    $sql = "SELECT COUNT(*) AS total FROM url_li";
    // 执行查询语句
    $result = $conn->query($sql);    if ($result->num_rows > 0) {
        // 输出查询结果
        while ($row = $result->fetch_assoc()) {
            return $row["total"];
        }
    } else {
        return null;
    }
}
//获取一个随机code
function generateRandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
    }    return $randomString;
}
//检查doce
function find_RandomString($randomString)
{
    $pattern = '/^[a-zA-Z0-9]{6}$/';
    return preg_match($pattern, $randomString);
}
