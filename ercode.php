 <html>
  <head>
  
<style>

table {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-bottom: 20px; 
}

th, td {
  text-align: left; 
  padding: 12px; 
}

th {
  font-size: 16px; 
  background-color: #f2f2f2;
  color: #444; 
}

td {
  border-bottom: 1px solid #ddd; 
}

tr:hover {
  background-color: #f5f5f5; 
}

button {
  background-color: #ff6600;
  color: white; 
  border: none; 
  padding: 8px 16px; 
  font-size: 14px;
  border-radius: 4px; 
  cursor: pointer;
  transition: 0.3s; 
}

button:hover {
  background-color: #cc5500;
}

button:active {
  background-color: #ff5500;
}

.enable {
  background-color: #4CAF50; 
}

.disable {
  background-color: red; 
}
a{
    color:white;
}

</style> 
  </head>

 
  <body>
<?php

    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $pre = "huafang_";
    
    
    
    if($_GET['single']){
        $redis->set($pre . "choose_ercode",$_GET['single']);
    
        header("Location:/ercode.php");
    }
    
    if($_GET['auto']){
        $redis->del($pre . "choose_ercode");
        
        header("Location:/ercode.php");
    }
    
    $oss_arr = [
                'ercode_1' => ["url"=>'aaaaaaaa',"remark"=>"张三的码"],
                'ercode_2' => ["url"=>'bbbbbb',"remark"=>"李四的码"],
                'ercode_3' => ["url"=>'ccccc',"remark"=>"王五的码"],
                'ercode_4' => ["url"=>'dddddd',"remark"=>"赵六的码"],
            ];
     
     $data = [];
     $index = 0;
     foreach ($oss_arr as $key => $val) {
        
        $data[$index]['oss_flag'] = $key;
        $data[$index]['oss_url'] = $val['url'];
        $data[$index]['today_ip'] = $redis->get($pre.$key . date("Y-m-d"));
        $data[$index]['all_ip'] = $redis->get($pre.$key . "_all");
        $data[$index]['status'] = 0;
        $data[$index]['remark'] = $val['remark'];
        
        if($redis->get($pre."choose_ercode")){
            if($redis->get($pre."choose_ercode") == $key)$data[$index]['status'] = 1;

        }

        $index++;
     }
     
     $page_url = $_SERVER['SERVER_NAME'] . "/ercode.php";
     $auto_or_single = $redis->get($pre . "choose_ercode");
     
    ?>

    <h1 style="text-align: center;">二维码池子</h1>
    <table>
        <h2 style="text-align: center;"><button type="submit" style="background:#ca2d2d"><a href="?auto=1" >自动模式</a></button></h2>
        
        <h3 style="text-align: center;">说明：当前是<?php if(!$auto_or_single){echo "自动模式，达到500访问量后会依照顺序展示下一张二维码";}else{echo "手动模式:".$auto_or_single."，会一直展示此二维码。 ";  } ?></h3>
        <h3 style="color:#5858a1">提示：拿到两个文件代码后，只需要更改的地方如下：</h3>
        <h3 style="color:#5858a1">1.handle.php文件，更改第6行$pre，第8行$count_threshold和第10行$oss_arr($pre代表当前网站或虚拟主机，如果一个服务器仅操作一个网站的二维码展示，则不需要更改。如果有其他网站，请填写不同redis标识。如"huafang","huafang2"等等，ercode.php也需要同步更改；$count_threshold表示流量达到多少会自动更换；$oss_arr的key表示OSS的url链接的标识，value表示url链接)</h3>  
        <h3 style="color:#5858a1">2.ercode.php文件，表示二维码池子的管理页面。默认是自动模式，达到500更换下一张。点击最右侧手动执行，说明不管这个二维码是多少，即使大于500，也会一直执行，直到达到自己的预期效果后，再点击执行其他，或者点击自动模式，或者修改handle.php的$count_threshold的值 </h3>
  <thead>
    <tr>
      <th>标识</th>
      <th>OSS链接</th>
      <th>备注</th>
      <th>今日IP</th>
      <th>近期IP</th>
       
      <th>状态</th>
      <th>操作</th>
    </tr>
  </thead>
  <tbody>
    
    <!-- <tr>
      <td>https://oss.example.com/file2.pdf</td>
      <td>文档2</td>
      <td>512</td>
      <td>1024</td>
      <td>1小时</td>
      <td><button style="background: #957f70;">停用</button></td>
    </tr>
   -->
  
   <?php foreach ($data as $key => $val) { ?>
    
      <tr>
        <td><?php echo $val['oss_flag'];?></td>
        <td><?php echo $val['oss_url'];?></td>
        <td><?php echo $val['remark'];?></td>
        <td><?php echo $val['today_ip'];?></td>
        <td><?php echo $val['all_ip'];?></td>
      
        <?php if($val['today_ip']>500){ ?>
            <td><button style="background: #957f70;">已结束</button></td>
        <?php }else{?>
            <td><button>进行中</button></td>
        <?php }?>
        <td><button style="background:#ca2d2d"> <a href="?single=<?php echo $val['oss_flag'];?>" >手动执行</a> </button></td>
      </tr>
    
   <?php }?>

  </tbody>
</table>

  </body>
 </html>
 