<?php
namespace libs;
class Page{

	// 总条数 
	// 每一页显示的条数
	// 总页数
	// 显示的字符串格式
	// 当前页
	// 分页的条件   
	protected $totalRows; // 总条数
	public $pageSize = 10; // 每页显示的条数
	protected $totalPages; // 总的页数
	protected $parameter; // 分页需要携带的参数

	private $nowPage = 1; // 当前页的页码
	private $p = 'p'; // ?p=2 表示第2页
	private $url = ''; // 分页地址
	private $rollPage = 5; // 分页栏中每页显示的页码数

	public function __construct($total,$pageSize='',$parameter=array()){

		$this->totalRows = $total;
		if(intval($pageSize)>0)$this->pageSize = intval($pageSize);
		$this->totalPages = ceil($this->totalRows / $this->pageSize); // 总的页数
		$this->parameter = empty($parameter) ? $_GET : $parameter;

		$this->nowPage = isset($_GET[$this->p]) ? intval($_GET[$this->p]) : 1;

		$this->nowPage = $this->nowPage > 0 ? $this->nowPage : 1;
		if($this->totalPages && ($this->nowPage > $this->totalPages )) $this->nowPage = $this->totalPages;
		$this->rollPage = $this->rollPage > $this->totalPages  ? $this->totalPages : $this->rollPage;
		

	}

	// 获取查询条件
	public function limit(){
		return (($this->nowPage - 1) * $this->pageSize);
	}

	//  生成链接地址
	public function parseUrl(){

		// 获取当前的访问地址
		$urlInfo = parse_url($_SERVER['REQUEST_URI']);
		// 对参数进行处理
		$this->parameter[$this->p] = "[PAGE]";
		$separator = '=';
		if($_SERVER['PATH_INFO']){
			$separator = '/';
		}
		// 整合参数
		$where = [];
		foreach($this->parameter as $k => $v){

			$where[] = $k.$separator.urlencode($v);
		}
		return $separator == '/' ? __ROOT__._CONTROLLER_.$separator._ACTION_.$separator.implode('/', $where) : $urlInfo['path'].'?'.implode('&', $where);
	}

	// 替换成实际的分页码
	public function url($pageNum){
		return str_replace(urlencode("[PAGE]"), $pageNum, $this->url);
	}
	// 生成分页字符串
	public function show(){
		if($this->totalPages == 0 ) return '';
		$this->url = $this->parseUrl();
		$now_cool_page = $this->rollPage/2;
		$page_str = "<a href='%s'>%s</a>"; // 显示的模板

		// 上一页
		$up_row = $this->nowPage - 1;
		$up_page = $up_row > 0 ? sprintf($page_str,$this->url($up_row),'上一页') : '';

		// 下一页
		$down_row = $this->nowPage + 1;
		$down_page = $down_row <= $this->totalPages ? sprintf($page_str,$this->url($down_row),'下一页') : '';

		// 首页
		$first = '';
		if($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >=1){
			$first = sprintf($page_str,$this->url(1),'首页');
		}

		// 尾页 
		$end = '';
		if($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages){
			$end = sprintf($page_str,$this->url($this->totalPages),'尾页');
		}

		$pageStr = '';
		// 数字页码的生成 $this->nowPage - $this->now_cool_page + $i 
		for($i=1;$i<=$this->rollPage;$i++){
			if($this->nowPage - $now_cool_page <=0 ){
				$page = $i;
			}elseif($this->nowPage + $now_cool_page -1 > $this->totalPages){
				$page = $this->totalPages - $this->rollPage + $i;
			}else{
				$page = $this->nowPage - ceil($now_cool_page) + $i;
			}

			if($page!=$this->nowPage){
				$pageStr.= sprintf($page_str,$this->url($page),$page);
			}else{
				$pageStr.= "<span>{$page}</span>";
			}
		}

		$pageStr = $first.$up_page.$pageStr.$down_page.$end;
		return $pageStr;
	}
}