<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<!-- Set render engine for 360 browser -->
	<meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- HTML5 shim for IE8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <![endif]-->

	<link href="/public/simpleboot/themes/<?php echo C('SP_ADMIN_STYLE');?>/theme.min.css" rel="stylesheet">
    <link href="/public/simpleboot/css/simplebootadmin.css" rel="stylesheet">
    <link href="/public/js/artDialog/skins/default.css" rel="stylesheet" />
    <link href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome.min.css"  rel="stylesheet" type="text/css">
    <style>
		form .input-order{margin-bottom: 0px;padding:3px;width:40px;}
		.table-actions{margin-top: 5px; margin-bottom: 5px;padding:0px;}
		.table-list{margin-bottom: 0px;}
	</style>
	<!--[if IE 7]>
	<link rel="stylesheet" href="/public/simpleboot/font-awesome/4.4.0/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
	//全局变量
	var GV = {
	    ROOT: "/",
	    WEB_ROOT: "/",
	    JS_ROOT: "public/js/",
	    APP:'<?php echo (MODULE_NAME); ?>'/*当前应用名*/
	};
	</script>
    <script src="/public/js/jquery.js"></script>
    <script src="/public/js/wind.js"></script>
    <script src="/public/simpleboot/bootstrap/js/bootstrap.min.js"></script>
    <script>
    	$(function(){
    		$("[data-toggle='tooltip']").tooltip();
    	});
    </script>
<?php if(APP_DEBUG): ?><style>
		#think_page_trace_open{
			z-index:9999;
		}
	</style><?php endif; ?>
</head>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li class="active"><a href="javascript:;">会员管理</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('AdminMember/index');?>">
			昵称： 
			<input type="text" name="username" style="width: 200px;" value="<?php echo ((isset($formget["username"]) && ($formget["username"] !== ""))?($formget["username"]):''); ?>" placeholder="请输入昵称..."> &nbsp;&nbsp;
			手机号： 
			<input type="text" name="phone" style="width: 200px;" value="<?php echo ((isset($formget["phone"]) && ($formget["phone"] !== ""))?($formget["phone"]):''); ?>" placeholder="请输入手机号..."> &nbsp;&nbsp;
			<!-- 时间：
			<input type="text" name="start_time" class="js-datetime" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>" style="width: 120px;" autocomplete="off">-
			<input type="text" class="js-datetime" name="end_time" value="<?php echo ((isset($formget["end_time"]) && ($formget["end_time"] !== ""))?($formget["end_time"]):''); ?>" style="width: 120px;" autocomplete="off"> &nbsp; &nbsp; -->
			主营： 
			<input type="text" name="keyword" style="width: 200px;" value="<?php echo ((isset($formget["keyword"]) && ($formget["keyword"] !== ""))?($formget["keyword"]):''); ?>" placeholder="请输入主营...">
			<input type="submit" class="btn btn-primary" value="搜索" />
			<a class="btn btn-danger" href="<?php echo U('AdminMember/index');?>">清空</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<!-- <th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th> -->
						<!-- <th width="30">排序</th> -->
						<th width="35">ID</th>
						<th width="80">昵称</th>
						<th width="50">真实姓名</th>
						<th width="30">手机号</th>
						<th width="50">主营业务</th>
						<th width="35">邀请人</th>
						<th width="30">经验/等级</th>
						<th width="30">金币余额/总量</th>
						<th width="30">加入时间</th>
						<th width="40">状态</th>
						<th width="30">操作</th>
					</tr>
				</thead>
				<?php $top_status=array("1"=>"已置顶","0"=>"未置顶"); $post_status=array("1"=>"未违规","0"=>"违规"); ?>
				<?php if(is_array($members)): foreach($members as $key=>$vo): ?><tr>
					<!-- <td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td> -->
					<!-- <td><input name="listorders[<?php echo ($vo["infosid"]); ?>]" class="input input-order" type="text" size="5" value="<?php echo ($vo["listorder"]); ?>"></td> -->
					<td><?php echo ($vo["user_id"]); ?></td>
                    <td><?php echo ($vo["username"]); ?></td>
                    <td><?php echo ($vo["realname"]); ?></td>
                    <td><?php echo ($vo["phone"]); ?></td>
                    <td><?php echo ($vo["main_business"]); ?></td>
					<td><?php echo ($vo["invite_userid"]); ?></td>
					<td><?php echo ($vo["exp"]); ?>/<?php echo ($vo["level"]); ?></td>
					<td><?php echo ($vo["gold"]); ?></td>
					<td><?php echo ($vo["addtime"]); ?></td>
					<td><?php echo ($post_status[$vo['post_status']]); ?><br><?php echo ($top_status[$vo['istop']]); ?></td>
					<td>
						<a href="<?php echo U('AdminMember/edit',array('id'=>$vo['user_id']));?>">查看</a>
					</td>
				</tr><?php endforeach; endif; ?>
				
			</table>
			<!-- <div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminMember/listorders');?>">排序</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminMember/check',array('check'=>1));?>" data-subcheck="true">违规</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminMember/check',array('uncheck'=>1));?>" data-subcheck="true">取消违规</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminMember/top',array('top'=>1));?>" data-subcheck="true">置顶</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminMember/top',array('untop'=>1));?>" data-subcheck="true">取消置顶</button>
			</div> -->
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
<!-- 	<scriMember
	</script> -->
</body>
</html>