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
<body class="J_scroll_fixed">
<div class="wrap jj">
  <ul class="nav nav-tabs">
     <li class="active"><a href="<?php echo U('AdminCategorys/index');?>">分类管理</a></li>
     <li><a href="<?php echo U('AdminCategorys/add');?>">添加分类</a></li>
  </ul>
  <form class="well form-search" method="post" action="<?php echo u('AdminCategorys/index');?>">
    <div class="search_type cc mb10">
      <div class="mb10"> 
        <span class="mr20">分类：
        <select class="select_2" name="type">
          	<option value='0' <?php if($type == 0): ?>selected="selected"<?php endif; ?>>信息分类</option>
          	<option value='1' <?php if($type == 1): ?>selected="selected"<?php endif; ?>>圈子分类</option>
        </select>
        <!-- &nbsp;&nbsp;时间：
        <input type="text" name="start_time" class="input length_2 J_date" value="<?php echo ((isset($formget["start_time"]) && ($formget["start_time"] !== ""))?($formget["start_time"]):''); ?>" style="width:80px;" autocomplete="off">-<input type="text" class="input length_2 J_date" name="end_time" value="<?php echo ($formget["end_time"]); ?>" style="width:80px;" autocomplete="off">
         -->
        <!-- 
        <select class="select_2" name="searchtype" style="width:70px;">
          <option value='0' >标题</option>
        </select>
         -->
       <!--         &nbsp; &nbsp;关键字：
        <input type="text" class="input length_2" name="keyword" style="width:200px;" value="<?php echo ($formget["keyword"]); ?>" placeholder="请输入关键字..."> -->
        <input type="submit" class="btn" value="搜索"/>
        </span>
      </div>
    </div>
  </form>
  <div class="common-form">
    <form method="post" class="J_ajaxForm" action="<?php echo U('AdminCategorys/listorders');?>">
      <div class="table_list">
	    <table width="100%" class="table table-hover">
	        <thead>
	          <tr>
	            <th width="80">排序</th>
	            <th width="100">ID</th>
	            <th>分类名称</th>
	            <th>分类类型</th>
              <th>分类图标</th>
	            <th>操作</th>
	          </tr>
	        </thead>
	        <tbody>
	        	<?php echo ($taxonomys); ?>
			</tbody>
	      </table>
  </div>
  		<div class="form-actions">
  			<input name="type" value="<?php echo ($type); ?>" type="hidden"/>
            <button type="submit" class="btn btn-primary btn_submit J_ajax_submit_btn">排序</button>
      </div>
    </form>
  </div>
</div>
<script src="/statics/js/common.js?"></script>
</body>
</html>