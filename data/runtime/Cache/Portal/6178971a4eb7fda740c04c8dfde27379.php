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
</head>
<body>
	<div class="wrap js-check-wrap">
		<ul class="nav nav-tabs">
			<li><a href="<?php echo U('AdminShop/index');?>">店铺管理</a></li>
			<li class="active"><a href="#">店铺编辑</a></li>
		</ul>
		<form action="<?php echo U('AdminShop/edit_post');?>" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data">
			<div class="row-fluid">
				<div class="span9">
					<table class="table table-bordered">
						<tr>
							<th width="100">店铺分类</th>
							<td>
								<select style="max-height: 100px;" name="post[cg_id]">
									<?php if(is_array($cg_id)): foreach($cg_id as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($shop["cg_id"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">店铺性质</th>
							<td>
								<select style="max-height: 100px;" name="post[shop_property]">
									<option value="1" <?php if($shop["shop_property"] == 1): ?>selected<?php endif; ?>>自营</option>
									<option value="0" <?php if($shop["shop_property"] == 0): ?>selected<?php endif; ?>>实体店铺</option>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">是否展示</th>
							<td>
								<select style="max-height: 100px;" name="post[status]">
									<option value="1" <?php if($shop["status"] == 1): ?>selected<?php endif; ?>>展示</option>
									<option value="0" <?php if($shop["status"] == 0): ?>selected<?php endif; ?>>不展示</option>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">是否连锁</th>
							<td>
								<select style="max-height: 100px;" name="post[is_brand]">
									<option value="1" <?php if($shop["is_brand"] == 1): ?>selected<?php endif; ?>>是</option>
									<option value="0" <?php if($shop["is_brand"] == 0): ?>selected<?php endif; ?>>否</option>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">是否推荐</th>
							<td>
								<select style="max-height: 100px;" name="post[recommended]">
									<option value="1" <?php if($shop["recommended"] == 1): ?>selected<?php endif; ?>>是</option>
									<option value="0" <?php if($shop["recommended"] == 0): ?>selected<?php endif; ?>>否</option>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">是否审核</th>
							<td>
								<select style="max-height: 100px;" name="post[check]">
									<option value="1" <?php if($shop["check"] == 1): ?>selected<?php endif; ?>>是</option>
									<option value="0" <?php if($shop["check"] == 0): ?>selected<?php endif; ?>>否</option>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">押金</th>
							<td>
								<select style="max-height: 100px;" name="post[deposit]">
									<option value="1" <?php if($shop["deposit"] == 1): ?>selected<?php endif; ?>>是</option>
									<option value="0" <?php if($shop["deposit"] == 0): ?>selected<?php endif; ?>>否</option>
								</select>
							</td>
						</tr>
						<tr>
							<th width="100">等级</th>
							<td>
								<select style="max-height: 100px;" name="post[level]">
									<option value="1" <?php if($shop["level"] == 1): ?>selected<?php endif; ?>>豪华版</option>
									<option value="0" <?php if($shop["level"] == 0): ?>selected<?php endif; ?>>初级版</option>
								</select>
							</td>
						</tr>
						<?php if($shop["level"] == 1): ?><tr>
							<th>有效期</th>
							<td>
								<input type="text" name="post[vip_time]" class="js-datetime" value="<?php echo ($shop['vip_time']); ?>" style="width: 120px;" autocomplete="off">
							</td>
						</tr><?php endif; ?>
						<tr>
							<th width="100">是否新店</th>
							<td>
								<select style="max-height: 100px;" name="post[is_new]">
									<option value="1" <?php if($shop["is_new"] == 0): ?>selected<?php endif; ?>>是</option>
									<option value="0" <?php if($shop["is_new"] == 0): ?>selected<?php endif; ?>>否</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>商店名称</th>
							<td>
								<input type="text" style="width: 400px;" name="post[shop_name]" required value="<?php echo ($shop["shop_name"]); ?>" placeholder="请输入标题"/>
								<span class="form-required">*</span>
							</td>
						</tr>
						<tr>
							<th>商店地址</th>
							<td>
								<input type="text" name="post[shop_addr]" style="width: 400px" value="<?php echo ($shop['shop_addr']); ?>" placeholder="请输入关键字">
							</td>
						</tr>
						<tr>
							<th>主营业务</th>
							<td>
								<input type="text" name="post[shop_major]" style="width: 400px" value="<?php echo ($shop['shop_major']); ?>" placeholder="请输入关键字">
							</td>
						</tr>
						<tr>
							<th>营业时间</th>
							<td>
								<input type="text" name="post[start_time]" class="js-datetime" value="<?php echo ($shop['start_time']); ?>" style="width: 120px;" autocomplete="off">-
								<input type="text" class="js-datetime" name="post[end_time]" value="<?php echo ($shop['end_time']); ?>" style="width: 120px;" autocomplete="off">
							</td>
						</tr>
						<tr>
							<th>关联用户手机号</th>
							<td><input type="text" name="post[shop_phone]" id="phone" value="<?php echo ($shop['shop_phone']); ?>" style="width: 400px" placeholder="请输入关联用户手机号"></td>
						</tr>
						<tr>
							<th>商家联系人</th>
							<td><input type="text" name="post[shop_contact]" value="<?php echo ($shop['shop_contact']); ?>" style="width: 400px" placeholder="请输入商家名称"></td>
						</tr>
						<tr>
							<th>商家联系电话</th>
							<td><input type="text" name="post[shop_phone]" value="<?php echo ($shop['shop_phone']); ?>" style="width: 400px" placeholder="请输入商家联系人"></td>
						</tr>
						<tr>
							<th>商家详情</th>
							<td>
								<input type="text" name="post[shop_detail]" value="<?php echo ($shop['shop_detail']); ?>" style="width: 400px" placeholder="请输入商家联系人">
							</td>
						</tr>
						<tr>
							<th>点击量</th>
							<td>
								<input type="text" name="post[hits]" style="width: 400px" value="<?php echo ($shop['hits']); ?>" placeholder="请输入关键字">
							</td>
						</tr>
						<tr>
							<th>相册图集</th>
							<td>
								<ul id="photos" class="pic-list unstyled">
									<?php if(!empty($shop['shop_pic'])): if(is_array($shop['shop_pic'])): foreach($shop['shop_pic'] as $key=>$vo): ?><li id="savedimage<?php echo ($key); ?>">
											<a href="<?php echo sp_get_image_preview_url($vo);?>" target='_blank'><img id="photo-<?php echo ($key); ?>-preview" src="<?php echo sp_get_image_preview_url($vo);?>" style="height:36px;width: 36px;"></a>
										</li><?php endforeach; endif; endif; ?>
								</ul>
							</td>
						</tr>
					</table>
				</div>
				<div class="span3">
					<table class="table table-bordered">
						<tr>
							<td><b>logo</b></td>
						</tr>
						<tr>
							<td>
								<div style="text-align: center;">
									<img src="<?php echo sp_get_image_preview_url($shop['shop_logo']);?>" id="thumb-preview" width="135" style="cursor: hand"/>
								</div>
							</td>
						</tr>
						<tr>
							<th>发布时间</th>
						</tr>
						<tr>
							<td><input type="text" name="post[add_time]" value="<?php echo ($shop["add_time"]); ?>" class="js-datetime" style="width: 160px;"></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="form-actions">
				<a class="btn" href="<?php echo U('AdminShop/index');?>">返回</a>
			</div>
		</form>
	</div>
	<script type="text/javascript" src="/public/js/common.js"></script>
	<script type="text/javascript">
		$("input").attr("disabled","disabled");
		$("select").attr("disabled","disabled");
		$(function() {
			$(".js-ajax-close-btn").on('click', function(e) {
				e.preventDefault();
				Wind.use("artDialog", function() {
					art.dialog({
						id : "question",
						icon : "question",
						fixed : true,
						lock : true,
						background : "#CCCCCC",
						opacity : 0,
						content : "您确定需要关闭当前页面嘛？",
						ok : function() {
							setCookie("refersh_time", 1);
							window.close();
							return true;
						}
					});
				});
			});
		});
	</script>
</body>
</html>