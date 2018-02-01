<?php
//引入PDO文件
require_once 'pdo.php';
//实例化
$del = NewPDO::getInstance('localhost','cpdb','Cpdb@PwdGcAdmin','cpdb','utf-8');

echo "\n**********开启用户订单事务***********";
//删除用户订单
try{
$result = $del->beginTransaction();
//获取当前时间的前一个月的时间
$mtime= date("Y-m-d H:i:s", strtotime("-1 month"));
//设置中国时区
date_default_timezone_set("PRC");

//查询条件
$sql = "select id,order_code from tp_user_order where add_time < '$mtime'";
$data = $del->query($sql,'All');
$del->closeCursor();

//批量删除
foreach ($data as  $value) {
	$id = $value['id'];
	$order_code = $value['order_code'];
	$res = $del->delete('tp_user_order','id='.$id);
	

	//删除订单方案
	echo "删除订单方案".$id;
	$res = $del->delete('tp_user_order_codes','order_id='.$id);
	


	//删除用户认购表
	echo "删除用户认购表".$id;
	$res = $del->delete('tp_user_buy_info','order_id='.$id);
	
	

	//删除用户认购加单表
	echo "删除用户认购加单表".$id;
	$res = $del->delete('tp_user_buy_info_jiadan','order_id='.$id);
	
	

	//删除用户资金流水表
	echo "删除用户资金流水表".$id;
	$res = $del->delete('tp_sale_userpaylog',"order_id = '".$order_code."'");
	echo "$res";
	
	

	//删除用户跟单信息表
	echo "删除用户跟单信息表".$id;
	$res = $del->delete('tp_gendan_order_info','order_id='.$id);


	//删除竞彩订单赛事表
	echo "删除竞彩订单赛事表".$id;
	$res = $del->delete('tp_jc_order_matchs','order_id='.$id);
	
	

	//删除竞彩订单让分胜负表
	echo "删除竞彩订单让分胜负表".$id;
	$res = $del->delete('tp_jc_order_rfsf','order_id='.$id);
	

	//删除订单赛事表
	echo "删除订单赛事表".$id;
	$res = $del->delete('tp_order_match','order_id='.$id);
	
}
//提交
$del->commit();
}catch(Exception $e){
echo $e->getMessage();
//回滚
$del->rollback();
}

echo "\n**********用户订单事务结束**********";


echo "\n**********开启比赛事务***************";
//删除比赛数据
try{
$result = $del->beginTransaction();
//获取当前时间的前一个月的时间
$mtime= date("Y-m-d H:i:s", strtotime("-1 month"));
//设置中国时区
date_default_timezone_set("PRC");

//查询条件
$sql = "select id,match_key from tp_match_info where dt < '$mtime' and is_draw = 1 and lot_type in (42,43)";
$data = $del->query($sql,'All');
//批量删除
foreach ($data as  $value) {
	$id = $value['id'];
	$match_key = $value['match_key'];
	$res = $del->delete('tp_match_info','id='.$id);
	if(!$res){
		echo "删除比赛数据出错啦";
		$del->rollback();
	}

	//删除开奖表
	echo "删除开奖表".$id;
	$res = $del->delete('tp_draw_lottery','match_key='.$match_key);
	if(!$res){
		echo "删除开奖表出错啦";
		$del->rollback();
	}

}
//提交
$del->commit();
}catch(Exception $e){
echo $e->getMessage();
//回滚
$del->rollback();
}

echo "\n**********比赛事务结束***************";

echo "\n**********开启充值流水事务***********";
//删除充值流水数据
try{
$result = $del->beginTransaction();
//获取当前时间的前一个月的时间
$mtime= date("Y-m-d H:i:s", strtotime("-1 month"));
//设置中国时区
date_default_timezone_set("PRC");

//查询条件
$sql = "select id,order_no from tp_sale_banktmp where create_time < '$mtime'";
$data = $del->query($sql,'All');
//批量删除
foreach ($data as  $value) {
	$id = $value['id'];
	$order_no = $value['order_no'];
	$res = $del->delete('tp_sale_banktmp','id='.$id);
	if(!$res){
		echo "删除充值临时表出错啦";
		$del->rollback();
	}

	//删除收入支出信息表
	echo "删除收入支出信息表".$id;
	$res = $del->delete('tp_sale_userpaylog',"order_id = '".$order_no."'");
	// var_dump($res);die;
	if(!$res){
		echo "删除收入支出信息表出错啦";
		$del->rollback();
	}

}
//提交
$del->commit();
}catch(Exception $e){
echo $e->getMessage();
//回滚
$del->rollback();
}

echo "\n**********充值流水事务结束***********";


echo "\n**********开启提现流水事务***********";
//删除提现流水数据
try{
$result = $del->beginTransaction();
//获取当前时间的前一个月的时间
$mtime= date("Y-m-d H:i:s", strtotime("-1 month"));
//设置中国时区
date_default_timezone_set("PRC");

//查询条件
$sql = "select id,cash_no from tp_sale_getmoney where cash_time < '$mtime'";
$data = $del->query($sql,'All');
//批量删除
foreach ($data as  $value) {
	$id = $value['id'];
	$cash_no = $value['cash_no'];
	$res = $del->delete('tp_sale_getmoney','id='.$id);
	if(!$res){
		echo "删除提现临时表出错啦";
		$del->rollback();
	}

	//删除收入支出信息表
	echo "删除收入支出信息表".$id;
	$res = $del->delete('tp_sale_userpaylog',"order_id = '".$cash_no."'");
	// var_dump($res);die;
	if(!$res){
		echo "删除收入支出信息表出错啦";
		$del->rollback();
	}

}
//提交
$del->commit();
}catch(Exception $e){
echo $e->getMessage();
//回滚
$del->rollback();
}

echo "\n**********提现流水事务结束***********";

//销毁
$del->destruct();

?>