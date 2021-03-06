<?php
//创建server对象(创建了一个tcp服务器)，监听192.168.17.128：55152端口
$serv = new swoole_server("192.168.17.128", 55152);

//监听连接进入事件   ###$fd是客户端连接的唯一标识
$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});

//设置异步任务的工作进程数量
$serv->set(array('task_worker_num'=>4));

//监听数据发送事件   ###$fd是客户端连接的唯一标识
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    //$serv->send($fd, 'Swoole: '.$data);

    //投递异步任务
    $task_id = $serv->task($data); 
    echo "Dispath AsyncTask: id=$task_id\n"; 
});

//处理异步任务 
$serv->on('task', function ($serv, $task_id, $from_id, $data) { 
    echo "New AsyncTask[id=$task_id]".PHP_EOL; 
    //返回任务执行的结果 
    $serv->finish("$data -> OK"); 
}); 

//处理异步任务的结果 
$serv->on('finish', function ($serv, $task_id, $data) { 
    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL; 
}); 


//监听连接关闭事件  ###$fd是客户端连接的唯一标识
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();
?>