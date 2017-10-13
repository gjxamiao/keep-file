<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>后台管理</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="{CSSPATH}bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{CSSPATH}backend.css?v={time()}">

</head>
<body>
	
	<!-- 导航条 -->
<nav class="navbar navbar-inverse navbar-fixed-top">
  	<div class="container-fluid">
	    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="#">后台管理系统</a>
    	</div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
       
       <li><a href="{__ROOT__}Index/add">发布笑话</a></li>
       
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">聆听<span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="#">个人资料</a></li>
            <li><a href="#">设置</a></li>
            <li><a href="#">退出</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
	
	<div class="container-fluid margin100 minheight">		
		{foreach $data}
		<div class="panel panel-default">
		  <div class="panel-heading">{$v['nickname']}发表于{date('Y-m-d H:i:s',$v['ctime'])}</div>
		  <div class="panel-body">
		   		{$v['content']}
		  </div>	
		</div>
		{/foreach}
		<div id="page">{$pagestr}</div>
	</div>
	

	<!-- 尾部 -->
	<footer>
		<div class="container ">

			<p class="center">php1505c微信开发小组</p>
		</div>
	</footer>

	<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="{JSPATH}bootstrap.min.js"></script>
	<script type="text/javascript" src="{JSPATH}backend.js"></script>
</body>
</html>