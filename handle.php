<?php
 

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$pre = "huafang_";

$count_threshold = 5;
 
$oss_arr = [
            'ercode_1' => 'aaaaaaaa',
            'ercode_2' => 'bbbbbbb',
            'ercode_3' => 'ccccccccc',
            'ercode_4' => 'ddddddddd',
        ];

//1:自动切换模式 2:指定模式，不自动切换
$targets_type = 1;
 

if ($redis->exists($pre."choose_ercode")) {
    
    $targets_type = 2;
    $targets_ercode = $redis->get($pre."choose_ercode");
    
}

if($targets_type == 1){
    foreach ($oss_arr as $key => $val) {
        if ($redis->get($pre.$key . date("Y-m-d")) < $count_threshold) {
            $targets_ercode = $key;break;
        }
    }
    
}

if(!$targets_ercode){
    
    $targets_ercode = 'ercode_1';
}
 
 
$redis->incr($pre.$targets_ercode . date("Y-m-d"));
$redis->incr($pre.$targets_ercode . "_all");

$targets_type_msg = $targets_type==1?"自动模式":"指定模式";
echo "当前模式:  【 " . $targets_type_msg . "】";
echo "<br>";

echo "现在展示二维码:  【 " . $targets_ercode . "】";
echo "<br>";

echo "今天访问IP次数" . date("Y-m-d") . ": 【 " . $redis->get($pre.$targets_ercode . date("Y-m-d")) . "次】";
echo "<br>";

echo "此二维码近期所有IP" .   ": 【 " . $redis->get($pre.$targets_ercode ."_all") . "次】";
echo "<br>";


die;
