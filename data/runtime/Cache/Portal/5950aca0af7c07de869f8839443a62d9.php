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
<style type="text/css">
.pic-list li {
	margin-bottom: 5px;
}
</style>
<script type="text/html" id="photos-item-wrapper">
	<li id="savedimage{id}">
		<input id="photo-{id}" type="hidden" name="photos_url[]" value="{filepath}"> 
		<input id="photo-{id}-name" type="text" name="photos_alt[]" value="{name}" style="width: 160px;" title="图片名称">
		<img id="photo-{id}-preview" src="{url}" style="height:36px;width: 36px;" onclick="parent.image_preview_dialog(this.src);">
		<a href="javascript:upload_one_image('图片上传','#photo-{id}');">替换</a>
		<a href="javascript:(function(){$('#savedimage{id}').remove();})();">移除</a>
	</li>
</script>
</head>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('AdminMember/index');?>">会员管理</a></li>
			<li class="active"><a href="#">会员信息</a></li>
		</ul>
		<!-- <form action="" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data"> -->
			<div class="row-fluid">
				<div class="span9">
					<table class="table table-bordered">
						
						<tr>
							<th>昵称</th>
							<td>
								<input type="text" style="width: 400px;" name="member[username]" required value="<?php echo ($member["username"]); ?>" placeholder=""/>
							</td>
							<th>头像</th>
							<td>
								<input type="text" style="width: 400px;" name="member[userphoto]" required value="<?php echo ($member["userphoto"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>手机</th>
							<td>
								<input type="text" style="width: 400px;" name="member[phone]" required value="<?php echo ($member["phone"]); ?>" placeholder=""/>
							</td>
							<th>邮箱</th>
							<td>
								<input type="text" style="width: 400px;" name="member[user_email]" required value="<?php echo ($member["user_email"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>真实姓名</th>
							<td>
								<input type="text" style="width: 400px;" name="member[realname]" required value="<?php echo ($member["realname"]); ?>" placeholder=""/>
							</td>
							<th>公司名</th>
							<td>
								<input type="text" style="width: 400px;" name="member[firmname]" required value="<?php echo ($member["firmname"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>职务</th>
							<td>
								<input type="text" style="width: 400px;" name="member[job]" required value="<?php echo ($member["job"]); ?>" placeholder=""/>
							</td>
							<th>主营业务</th>
							<td>
								<input type="text" style="width: 400px;" name="member[main_business]" required value="<?php echo ($member["main_business"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>经验</th>
							<td>
								<input type="text" style="width: 400px;" name="member[exp]" required value="<?php echo ($member["exp"]); ?>" placeholder=""/>
							</td>
							<th>等级</th>
							<td>
								<input type="text" style="width: 400px;" name="member[level]" required value="<?php echo ($member["level"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>当前金币</th>
							<td>
								<input type="text" style="width: 400px;" name="member[gold]" required value="<?php echo ($member["gold"]); ?>" placeholder=""/>
							</td>
							<th>金币总量</th>
							<td>
								<input type="text" style="width: 400px;" name="member[gold_total]" required value="<?php echo ($member["gold_total"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>加入圈子</th>
							<td>
								<ul>
									<?php if(is_array($cicles)): foreach($cicles as $key=>$vo): ?><li>圈子：<?php echo ($vo["cg_name"]); ?>&nbsp;&nbsp;&nbsp;加入时间：<?php echo ($vo["addtime"]); ?></li><?php endforeach; endif; ?>
								</ul>
							</td>
							
						</tr>
						<tr>
							<th>关注列表</th>
							<td>
								<ul>
									<?php if(is_array($follows)): foreach($follows as $key=>$vo): ?><li>昵称：<a href="<?php echo U('AdminMember/edit',array('id'=>$vo['follow_id']));?>"><?php echo ($vo["follow_name"]); ?></a>&nbsp;&nbsp;&nbsp;关注时间：<?php echo ($vo["addtime"]); ?></li><?php endforeach; endif; ?>
								</ul>
							</td>
							
						</tr>
						<tr>
							<th>粉丝列表</th>
							<td>
								<ul>
									<?php if(is_array($fans)): foreach($fans as $key=>$vo): ?><li>昵称：<a href="<?php echo U('AdminMember/edit',array('id'=>$vo['fan_id']));?>"><?php echo ($vo["fan_name"]); ?></a>&nbsp;&nbsp;&nbsp;关注时间：<?php echo ($vo["addtime"]); ?></li><?php endforeach; endif; ?>
								</ul>
							</td>
							
						</tr>
						<tr>
							<th>我的收藏</th>
							<td>
								<a href="<?php echo U('AdminInfo/index',array('stars'=>1,'m_id'=>$member['user_id']));?>" target="_blank">我的收藏</a>
							</td>
							
						</tr>
						<tr>
							<th>我的发布</th>
							<td>
								<a href="<?php echo U('AdminInfo/index',array('post'=>1,'m_id'=>$member['user_id']));?>" target="_blank">我的发布</a>
							</td>
							
						</tr>
					</table>
				</div>
			</div>
			<div class="form-actions">
				<a class="btn" href="<?php echo U('AdminMember/index');?>">返回</a>
			</div>
		<!-- </form> -->
	</div>
	<script type="text/javascript" src="/public/js/common.js"></script>
	<script type="text/javascript">
		$(".table-bordered input").attr("readonly","readonly");
	</script>
</body>
</html>