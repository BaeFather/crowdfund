<?
$con = mysqli_connect(
    $_ENV['MYDATA_DB_HOST'],
    $_ENV['MYDATA_DB_USER'],
    $_ENV['MYDATA_DB_PASSWORD'],
    $_ENV['MYDATA_DB_NAME'],
    $_ENV['MYDATA_DB_PORT']
);
mysqli_set_charset($con, "utf8");
?>