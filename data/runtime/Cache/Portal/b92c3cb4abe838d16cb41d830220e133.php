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
			<li class="active"><a href="javascript:;">店铺管理</a></li>
		</ul>
		<form class="well form-search" method="post" action="<?php echo U('AdminShop/index');?>">
			分类
			<select name="cg_id" style="width: 65px;">
				<option value='0' <?php if($formget["cg_id"] == 0): ?>selected="selected"<?php endif; ?>>全部</option>
				<?php if(is_array($cg_id)): foreach($cg_id as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($formget["cg_id"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
			</select> &nbsp;&nbsp;
			豪华版
			<select name="level" style="width: 65px;">
				<option value='0' <?php if($formget["level"] == 0): ?>selected="selected"<?php endif; ?>>全部</option>
				<?php if(is_array($level)): foreach($level as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($formget["level"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
			</select> &nbsp;&nbsp;
			活动
			<select name="is_sale" style="width: 65px;">
				<option value='0' <?php if($formget["is_sale"] == 0): ?>selected="selected"<?php endif; ?>>全部</option>
				<?php if(is_array($is_sale)): foreach($is_sale as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($formget["is_sale"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
			</select> &nbsp;&nbsp;
			招聘
			<select name="is_recruit" style="width: 65px;">
				<option value='0' <?php if($formget["is_recruit"] == 0): ?>selected="selected"<?php endif; ?>>全部</option>
				<?php if(is_array($is_recruit)): foreach($is_recruit as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($formget["is_recruit"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
			</select> &nbsp;&nbsp;
			押金
			<select name="deposit" style="width: 65px;">
				<option value='0' <?php if($formget["deposit"] == 0): ?>selected="selected"<?php endif; ?>>全部</option>
				<?php if(is_array($deposit)): foreach($deposit as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($formget["deposit"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
			</select> &nbsp;&nbsp;
			审核
			<select name="check" style="width: 65px;">
				<option value='0' <?php if($formget["check"] == 0): ?>selected="selected"<?php endif; ?>>全部</option>
				<?php if(is_array($check)): foreach($check as $k=>$vo): ?><option value="<?php echo ($k); ?>" <?php if($formget["check"] == $k): ?>selected="selected"<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; ?>
			</select> &nbsp;&nbsp;
			截止时间
			<input type="text" name="vip_time" class="js-datetime" value="<?php echo ((isset($formget["vip_time"]) && ($formget["vip_time"] !== ""))?($formget["vip_time"]):''); ?>" style="width: 120px;" autocomplete="off"> &nbsp; &nbsp;
			关键字:
			<input type="text" name="keyword" style="width: 100px;" value="<?php echo ((isset($formget["keyword"]) && ($formget["keyword"] !== ""))?($formget["keyword"]):''); ?>" placeholder="请输入关键字...">
			<input type="submit" class="btn btn-primary" value="搜索" />
			<a class="btn btn-danger" href="<?php echo U('AdminShop/index');?>">清空</a>
		</form>
		<form class="js-ajax-form" action="" method="post">
			
			<table class="table table-hover table-bordered table-list">
				<thead>
					<tr>
						<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>
						<th width="20">排序</th>
						<th width="20">ID</th>
						<th width="70">店铺名</th>
						<th width="80">主营业务</th>
						<th width="30">联系人</th>
						<th width="60">商家联系电话</th>
						<th width="40">店铺性质</th>
						<th width="40">是否连锁</th>
						<th width="40">是否新店</th>
						<th width="20">押金</th>
						<th width="20">等级</th>
						<th width="80">豪华版期限</th>
						<th width="40">是否推荐</th>
						<th width="40">是否展示</th>
						<th width="40">是否审核</th>
						<th width="20">操作</th>
					</tr>
				</thead>
				<?php $status=array("1"=>"展示","0"=>"不展示"); $is_shiti=array("1"=>"实体","0"=>"非实体"); $shop_property=array("1"=>"自营","0"=>"实体"); $is_brand=array("1"=>"连锁","0"=>"非连锁"); $is_new=array("1"=>"新店","0"=>"非新店"); $istop=array("1"=>"置顶","0"=>"不置顶"); $deposit=array("1"=>"已缴纳","0"=>"未缴纳","2"=>"已退款"); $level=array("0"=>"初级","1"=>"豪华"); $check=array("0"=>"未审核","1"=>"已审核"); $recomended=array("0"=>"未推荐","1"=>"推荐"); ?>
				<?php if(is_array($shops)): foreach($shops as $key=>$vo): ?><tr>
					<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
					<td><input name="listorders[<?php echo ($vo["id"]); ?>]" class="input input-order" type="text" size="5" value="<?php echo ($vo["listorder"]); ?>"></td>
                    <td><b><?php echo ($vo["id"]); ?></b></td>
					<td><?php echo ($vo["shop_name"]); ?></td>
					<td><?php echo ($vo["shop_major"]); ?></td>
					<td><a href="<?php echo U('AdminMember/edit',array('id'=>$vo['member_id']));?>"  target="_blank"><?php echo ($vo["member_id"]); ?></a></td>
					<td><?php echo ($vo["shop_phone"]); ?></td>
					<td><?php echo ($shop_property[$vo['shop_property']]); ?></td>					
					<td><?php echo ($is_brand[$vo['is_brand']]); ?></td>
					<td><?php echo ($is_new[$vo['is_new']]); ?></td>
					<td><?php echo ($deposit[$vo['deposit']]); ?></td>
					<td><?php echo ($level[$vo['level']]); ?></td>
					<td><?php echo ($vo["vip_time"]); ?></td>
					<td><?php echo ($recomended[$vo['recommended']]); ?></td>
					<td><?php echo ($status[$vo['status']]); ?></td>
					<td><?php echo ($check[$vo['check']]); ?></td>
					<td>
						<a href="<?php echo U('AdminShop/edit',array('id'=>$vo['id']));?>">查看</a>
					</td>
				</tr><?php endforeach; endif; ?>
				
			</table>
			<div class="table-actions">
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/listorders');?>">排序</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/check',array('check'=>1));?>" data-subcheck="true">审核</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/check',array('uncheck'=>1));?>" data-subcheck="true">取消审核</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/top',array('top'=>1));?>" data-subcheck="true">置顶</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/top',array('untop'=>1));?>" data-subcheck="true">取消置顶</button>
 				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/recommend',array('recommend'=>1));?>" data-subcheck="true">推荐</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/recommend',array('unrecommend'=>1));?>" data-subcheck="true">取消推荐</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/setstatus',array('status'=>1));?>" data-subcheck="true">上架</button>
				<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/setstatus',array('unstatus'=>1));?>" data-subcheck="true">下架</button>
				<!-- <button class="btn btn-danger btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminShop/delete');?>" data-subcheck="true" data-msg="你确定删除吗？">删除</button> -->
			</div>
			<div class="pagination"><?php echo ($page); ?></div>
		</form>
	</div>
	<script src="/public/js/common.js"></script>
	<script>
		function refersh_window() {
			var refersh_time = getCookie('refersh_time');
			if (refersh_time == 1) {
				window.location = "<?php echo U('AdminShop/index',$formget);?>";
			}
		}
		setInterval(function() {
			refersh_window();
		}, 2000);
		$(function() {
			setCookie("refersh_time", 0);
			Wind.use('ajaxForm', 'artDialog', 'iframeTools', function() {
				//批量复制
				$('.js-articles-copy').click(function(e) {
					var ids=[];
					$("input[name='ids[]']").each(function() {
						if ($(this).is(':checked')) {
							ids.push($(this).val());
						}
					});
					
					if (ids.length == 0) {
						art.dialog.through({
							id : 'error',
							icon : 'error',
							content : '您没有勾选信息，无法进行操作！',
							cancelVal : '关闭',
							cancel : true
						});
						return false;
					}
					
					ids= ids.join(',');
					art.dialog.open("/index.php?g=portal&m=AdminShop&a=copy&ids="+ ids, {
						title : "批量复制",
						width : "300px"
					});
				});
				//批量移动
				$('.js-articles-move').click(function(e) {
					var ids=[];
					$("input[name='ids[]']").each(function() {
						if ($(this).is(':checked')) {
							ids.push($(this).val());
						}
					});
					
					if (ids.length == 0) {
						art.dialog.through({
							id : 'error',
							icon : 'error',
							content : '您没有勾选信息，无法进行操作！',
							cancelVal : '关闭',
							cancel : true
						});
						return false;
					}
					
					ids= ids.join(',');
					art.dialog.open("/index.php?g=portal&m=AdminShop&a=move&old_term_id=<?php echo ((isset($term["term_id"]) && ($term["term_id"] !== ""))?($term["term_id"]):0); ?>&ids="+ ids, {
						title : "批量移动",
						width : "300px"
					});
				});
			});
		});
	</script>
</body>
</html>