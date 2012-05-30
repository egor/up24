<?php
$u=0;
mysql_connect('localhost', 'root', '');
mysql_select_db('deluxe_upline24-ru-new');
//mysql_query("SET character_set_client = utf-8");
//mysql_query("SET character_set_connection = utf-8");
//mysql_query("SET character_set_results = utf-8");
$res = mysql_query("SELECT * FROM `users` WHERE `id`>'5517'");
while ($row=  mysql_fetch_array($res)){
    $res_ua = mysql_query("SELECT * FROM `users_u` WHERE `email`='".$row['email']."'");
    if (mysql_num_rows($res_ua)!=0){
        $row_ua = mysql_fetch_array($res_ua);
        //echo $row['email'].' - '.$row['number'].' -> '.$row_ua['email'].' - '.$row_ua['number'].'<br>';
        
        mysql_query("UPDATE `users` SET `email`='".$row_ua['email']."',
            `password`='".$row_ua['password']."',
            `surname`='".$row_ua['surname']."',
            `name`='".$row_ua['name']."',
            `number`='".$row_ua['number']."',
            `phone`='".$row_ua['phone']."',
            `diamond`='".$row_ua['diamond']."',
            `emerald`='".$row_ua['emerald']."',
            `date`='".$row_ua['date']."',
            `status`='".$row_ua['status']."',
            `privilege`='".$row_ua['privilege']."',
            `type`='".$row_ua['type']."',
            `remind_date`='".$row_ua['remind_date']."',
            `remind_code`='".$row_ua['remind_code']."',
            `up`='".$row_ua['up']."',
            `upup`='".$row_ua['upup']."',
            `upupup`='".$row_ua['upupup']."',
            `top_user_no`='".$row_ua['top_user_no']."' WHERE `email`='".$row_ua['email']."'
            ");
        $u++;
    }
}
echo '<br><br><br>'.$u;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
