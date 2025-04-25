<?php
include_once('./_common.php');

$sql = " SELECT * FROM schools_w WHERE (1) order by id desc ";
$result = sql_query($sql);

$rand = rand();

header("Content-Type: application/vnd.ms-excel"); 
header("Content-Type: application/x-msexcel"); 
header("Content-Disposition: attachment; filename=w{$rand}.xls");
header("Content-Description: PHP4 Generated Data" ); 
//header("Content-charset=utf-8");
?>

<html>
<head>
<style>
.sty { font-family:굴림; font-size:12px;}
.sty0 { font-family:굴림; font-size:12px; text-align:center;}
.sty1 {mso-number-format:"\@";font-family:굴림; font-size:12px}
.sty2 {font-family:굴림; font-size:12px}
.sty3 {font-family:굴림; font-size:12px; color: #ff0000}
.title {font-family:굴림; font-size:12px; font-weight:600}
.title_s {font-family:굴림; font-size:16px}
</style>
</head>
<body bgcolor="#FFFFFF">
<table border="1" style="table-layout:fixed">
<tr align="center">    
    <td bgcolor="#CCFFCC">school_name</td>
    <td bgcolor="#CCFFCC">school_level</td>
    <td bgcolor="#CCFFCC">road_address</td>   
    <td bgcolor="#CCFFCC">zip</td>   
</tr>

<?php for ($i=0; $row=sql_fetch_array($result); $i++) { ?>
    <tr>
        <td class="sty1"><?php echo $row['school_name'];?></td>
    	<td class="sty1"><?php echo $row['school_level'];?></td>
        <td class="sty1"><?php echo $row['road_address'];?></td>
        <td class="sty1"><?php echo $row['zip'];?></td>
    </tr>
<?php }
sql_free_result($result);
?>
    </table>
</body>
</html>
