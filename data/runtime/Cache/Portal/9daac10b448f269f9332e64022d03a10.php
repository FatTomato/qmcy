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
			<li><a href="<?php echo U('AdminInfo/index');?>">信息管理</a></li>
			<li class="active"><a href="#">查看信息</a></li>
		</ul>
		<!-- <form action="" method="post" class="form-horizontal js-ajax-forms" enctype="multipart/form-data"> -->
			<div class="row-fluid">
				<div class="span9">
					<table class="table table-bordered">
						<tr>
							<th width="80">分类</th>
							<td>
								<select class="infos" style="max-height: 100px;" name="cg_id"><?php echo ($taxonomys); ?></select>
							</td>
						</tr>
						<tr>
							<th>内容</th>
							<td>
								<!-- <input type="hidden" name="post[id]" value="<?php echo ($post["id"]); ?>"> -->
								<input class="infos" type="text" style="width: 400px;" name="post[post_content]" required value="<?php echo ($post["post_content"]); ?>" placeholder=""/>
							</td>
						</tr>
						<tr>
							<th>发布时间</th>
							<td>
								<input class="infos" type="text" name="post[post_date]" style="width: 400px" value="<?php echo ($post['post_date']); ?>" placeholder="">
							</td>
						</tr>
	
						<tr>
							<th>发布地址</th>
							<td>
								<input class="infos" type="text" name="post[post_addr]" value="<?php echo ($post['post_addr']); ?>" style="width: 400px" placeholder="">
							</td>
						</tr>
						<tr>
							<th>相册图集</th>
							<td>
								<ul id="photos" class="pic-list unstyled">
									<?php if(!empty($smeta)): if(is_array($smeta)): foreach($smeta as $key=>$vo): $img_url=sp_get_image_preview_url($vo['url']); ?>
										<li id="savedimage<?php echo ($key); ?>">
											<input id="photo-<?php echo ($key); ?>" type="hidden" name="photos_url[]" value="<?php echo ($img_url); ?>"> 
											<input id="photo-<?php echo ($key); ?>-name" type="text" name="photos_alt[]" value="<?php echo ($vo["alt"]); ?>" style="width: 200px;" title="图片名称">
											<img id="photo-<?php echo ($key); ?>-preview" src="<?php echo sp_get_image_preview_url($vo['url']);?>" style="height:36px;width: 36px;" onclick="parent.image_preview_dialog(this.src);">
											<a href="javascript:upload_one_image('图片上传','#photo-<?php echo ($key); ?>');">替换</a>
											<a href="javascript:(function(){ $('#savedimage<?php echo ($key); ?>').remove();})();">移除</a>
										</li><?php endforeach; endif; endif; ?>
								</ul>
							</td>
							
						</tr>
						<tr>
							<th>评论与回复</th>
							<td>
							<?php if(!empty($comments)): ?><form action="" method="post" class="js-ajax-form" enctype="multipart/form-data">
									<table>
										<thead>
											<tr>
												<th width="15"><label><input type="checkbox" class="js-check-all" data-direction="x" data-checklist="js-check-x"></label></th>
												<th width="35">ID</th>
												<th width="80">评论人</th>
												<th width="50">被评论人</th>
												<th width="50">评论内容</th>
												<th width="250">评论时间</th>
												<th width="40">状态</th>
											</tr>
										</thead>
										<?php if(is_array($comments)): foreach($comments as $key=>$vo): ?><tr>
										<?php $post_status=array("1"=>"未违规","0"=>"违规"); ?>
											<td><input type="checkbox" class="js-check" data-yid="js-check-y" data-xid="js-check-x" name="ids[]" value="<?php echo ($vo["id"]); ?>" title="ID:<?php echo ($vo["id"]); ?>"></td>
											<td><?php echo ($vo["id"]); ?></td>
						                    <td><?php echo ($vo["from_name"]); ?></td>
						                    <td><?php echo ($vo["to_name"]); ?></td>
						                    <td><?php echo ($vo["content"]); ?></td>
											<td><?php echo ($vo["createtime"]); ?></td>
											<td><?php echo ($post_status[$vo['status']]); ?></td>
										</tr><?php endforeach; endif; ?>
									</table>
									<div class="table-actions">
										<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminInfo/check_comments',array('check'=>1));?>" data-subcheck="true">违规</button>
										<button class="btn btn-primary btn-small js-ajax-submit" type="submit" data-action="<?php echo U('AdminInfo/check_comments',array('uncheck'=>1));?>" data-subcheck="true">取消违规</button>
									</div>
								</form><?php endif; ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="form-actions">
				<a class="btn" href="<?php echo U('AdminInfo/index');?>">返回</a>
			</div>
		<!-- </form> -->
	</div>
	<script type="text/javascript" src="/public/js/common.js"></script>
	<script type="text/javascript">
		$(".infos").attr("disabled","disabled");
		// $('.infos').attr("readonly","readonly");
	</script>
	<!-- <script type="text/javascript" src="/public/js/ueditor/ueditor.config.js"></script> -->
	<!-- <script type="text/javascript" src="/public/js/ueditor/ueditor.all.min.js"></script> -->
	<!-- <script type="text/javascript">
		$(function() {
			
			//setInterval(function(){public_lock_renewal();}, 10000);
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
			/////---------------------
			Wind.use('validate', 'ajaxForm', 'artDialog', function() {
				//javascript

				//编辑器
				editorcontent = new baidu.editor.ui.Editor();
				editorcontent.render('content');
				try {
					editorcontent.sync();
				} catch (err) {
				}
				//增加编辑器验证规则
				jQuery.validator.addMethod('editorcontent', function() {
					try {
						editorcontent.sync();
					} catch (err) {
					}
					;
					return editorcontent.hasContents();
				});
				var form = $('form.js-ajax-forms');
				//ie处理placeholder提交问题
				if ($.browser && $.browser.msie) {
					form.find('[placeholder]').each(function() {
						var input = $(this);
						if (input.val() == input.attr('placeholder')) {
							input.val('');
						}
					});
				}
				//表单验证开始
				form.validate({
					//是否在获取焦点时验证
					onfocusout : false,
					//是否在敲击键盘时验证
					onkeyup : false,
					//当鼠标掉级时验证
					onclick : false,
					//验证错误
					showErrors : function(errorMap, errorArr) {
						//errorMap {'name':'错误信息'}
						//errorArr [{'message':'错误信息',element:({})}]
						try {
							$(errorArr[0].element).focus();
							art.dialog({
								id : 'error',
								icon : 'error',
								lock : true,
								fixed : true,
								background : "#CCCCCC",
								opacity : 0,
								content : errorArr[0].message,
								cancelVal : '确定',
								cancel : function() {
									$(errorArr[0].element).focus();
								}
							});
						} catch (err) {
						}
					},
					//验证规则
					rules : {
						'post[post_title]' : {
							required : 1
						},
						'post[post_content]' : {
							editorcontent : true
						}
					},
					//验证未通过提示消息
					messages : {
						'post[post_title]' : {
							required : '请输入标题'
						},
						'post[post_content]' : {
							editorcontent : '内容不能为空'
						}
					},
					//给未通过验证的元素加效果,闪烁等
					highlight : false,
					//是否在获取焦点时验证
					onfocusout : false,
					//验证通过，提交表单
					submitHandler : function(forms) {
						$(forms).ajaxSubmit({
							url : form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
							dataType : 'json',
							beforeSubmit : function(arr, $form, options) {

							},
							success : function(data, statusText, xhr, $form) {
								if (data.status) {
									setCookie("refersh_time", 1);
									//添加成功
									Wind.use("artDialog", function() {
										art.dialog({
											id : "succeed",
											icon : "succeed",
											fixed : true,
											lock : true,
											background : "#CCCCCC",
											opacity : 0,
											content : data.info,
											button : [ {
												name : '继续编辑？',
												callback : function() {
													//reloadPage(window);
													return true;
												},
												focus : true
											}, {
												name : '返回列表页',
												callback : function() {
													location = "<?php echo U('AdminInfo/index');?>";
													return true;
												}
											} ]
										});
									});
								} else {
									artdialog_alert(data.info);
								}
							}
						});
					}
				});
			});
			////-------------------------
		});
	</script> -->
</body>
</html>