<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="http://47.93.226.132{CSSPATH}bootstrap.min.css" rel="stylesheet">
	<title>显示个人的信息</title>
</head>
<body>
	<nav class="navbar navbar-default">
	<div class="container-fluid">
	    <div class="navbar-header">
	      <a class="navbar-brand" href="#">内涵段子</a>
	    </div>
	    
	      <ul class="nav navbar-nav navbar-right">
	        <li><a href="#"><img src="{$avatar}" class="img-circle" style="height:30px;">{$nickname}</a></li>
	        <li class="dropdown">
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">个人菜单<span class="caret"></span></a>
	          <ul class="dropdown-menu">
	            <li><a href="#">个人信息</a></li>
	            <li role="separator" class="divider"></li>
	            <li><a href="#">我的收藏</a></li>
	            <li role="separator" class="divider"></li>
	            <li><a href="#">退出</a></li>
	          </ul>
	        </li>
	      </ul>
	    
  	</div>
	</nav>
	<div class="jumbotron">
		  <div class="container">
		    <h1>欢迎你，{$nickname}</h1>
			  <p>1505PHPC班欢迎你</p>
			  <p><a class="btn btn-primary" href="#" role="button">更多</a></p>
		  </div>
	</div>
	<script src="https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js"></script>
	<script src="{JSPATH}bootstrap.min.js"></script>
</body>
</html>