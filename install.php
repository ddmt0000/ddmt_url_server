<?php
    include ("route/mysql.php");
    $sql = "CREATE TABLE url_li( ".
    "url_id INT NOT NULL AUTO_INCREMENT, ".
    "url_orange VARCHAR(1000) NOT NULL, ".
    "url_new VARCHAR(40) NOT NULL, ".
    "url_ip VARCHAR(40) NOT NULL, ".
    "submission_date DATETIME, ".
    "PRIMARY KEY ( url_id ))ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
    mysqli_select_db($conn, 'RUNOOB' );
    $retval = mysqli_query($conn, $sql );
    if(! $retval ){
        die('数据表创建失败: ' . mysqli_error($conn));
    }
    echo "数据表创建成功\n";

    $sql = "CREATE TABLE url_user( ".
    "user_id INT NOT NULL AUTO_INCREMENT, ".
    "user_name VARCHAR(1000) NOT NULL, ".
    "user_password VARCHAR(40) NOT NULL, ".
    "user_ip VARCHAR(40) NOT NULL, ".
    "user_role VARCHAR(40) NOT NULL, ".
    "login_date DATETIME, ".
    "PRIMARY KEY ( user_id ))ENGINE=InnoDB DEFAULT CHARSET=utf8; ";

    mysqli_select_db($conn, 'RUNOOB' );
    $retval = mysqli_query($conn, $sql );
    if(! $retval ){
        die('数据表创建失败: ' . mysqli_error($conn));
    }
    echo "数据表创建成功\n";
    mysqli_close($conn);
?>