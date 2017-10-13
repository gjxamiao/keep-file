<?php
namespace libs;
/*
* 修改Model类添加上预处理
* prepare
  $user->field('id')->where()->limit()->groupBy->()->orderBy()->select()->save()->delete()->insert()
  将Model类实现ArrayAccess的接口，实现
    $model->username = 1;
    $model->password = 2;
    $model->save();
*/
    //模板类
class Model{
	private $host='127.0.0.1';
	private $username='root';
	private $password='root';
	private $dbname;
	private $tablePrefix='';  //表前缀

	protected $tableName;
	private $connect ;
	private $where=''; //条件
	private $limit=''; // 条数限制
	private $order = ''; // 排序
	private $fields =''; // 查询字段
	private $field = '';
	private $fieldAll = [];
	private $primary = 'id';


	public function __construct(){
		
		$config = C('db'); //配置文件
		$db_config = $config['db1']; //获取配置文件
		foreach($db_config as $key=>$v){
			$this->$key = $v;
		}
		$dns = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
		try{ //任何错误都没有会执行
			$this->connect = new \PDO($dns,$this->username,$this->password);
		}catch (PDOException $e) {  //如果不正常的话就抛出一个错误
    		echo 'Connection failed: ' . $e->getMessage();
		}
		$this->tableName = $this->setTableName();
		$this->getFields();
	}

	public function getFields(){
		$filename = RUNTIME.'fields'.DS.$this->tableName.'.frm';
		is_dir(RUNTIME.'fields') || mkdir(RUNTIME.'fields',0777,true);
		if(file_exists($filename)){
			// 直接读取进来
			$this->fieldAll = unserialize(file_get_contents($filename));
		}else{
		// desc $this->tableName;
			$sql = "desc ".$this->tableName;
			$this->fieldAll = $this->exec($sql,[],'select');
			file_put_contents($filename, serialize($this->fieldAll));
		}
		foreach($this->fieldAll as $key=>$value){
			$this->fields .= $value['Field'].',';
			if($value['Key'] == "PRI"){
				$this->primary = $value['Field'];
			}
		}
		$this->fields = rtrim($this->fields,',');
	}

	public function getPrimary(){
		return $this->primary;
	}

	public function count(){

	}
	/*
	* getById getByUsername
	*/
	public function __call($method,$params){
		if(substr($method,0,5) == "getBy"){
			$field = strtolower(substr($method,5)); // 得到字段名
			// 当前字段是否在表中存在
			if(strpos($this->fields,$field)!== false){
				if(count($params)>0){
					$value = $params[0];
				}
				// 进行查询
				$this->where([$field=>$value]);
				return $this->select();
			}
		}
		throw(new \Exception("对不起,方法不存在")); 
	}

	public function insert($arr=[]){
		$sql = "insert into ".$this->tableName." set ";
		$values = [];
		if(is_array($arr) && count($arr)>0){
			foreach($arr as $key=>$value){
				$values[]=$key." = '".$value."'";
			}
			$sql.= implode(',',$values);
		}
		if(is_string($arr)){
			$sql = $arr;
		}
		return $this->exec($sql,[],'insert');

	}

	public function save($arr=[]){
		// update tableName set 字段=值 where 条件
		$sql = "update ".$this->tableName.' set ';
		$values = [];
		foreach($arr as $key=>$value){

			$values[]=$key." = '".$value."'";	
		}
		$sql.= implode(',',$values);
		if($this->where)$sql.=' where '.$this->where;
		return $this->exec($sql,[],'update');

	}

	public function delete(){
		// delete from tableName where 
		$sql = 'delete ';
		$sql.=$this->mergeSql('delete');
		var_dump($sql);
		return $this->exec($sql,[],'delete');
	}

	public function where($arr=''){
		if(is_array($arr)){
			$where = [];
			$complex= isset($arr['complex']) ? " ".$arr['complex']." " : ' and ';
			foreach($arr as $key=>$v){
				if($key!='complex'){

					$where[] = ($key==='?' || strpos($key,':')===0) ? $key." = ".$v : $key." = '".$v."'";
				}
			}
			$this->where = implode($complex,$where); 
		}
		if(is_string($arr)){
			$this->where = $arr;
		}
		return $this;
	}

	public function limit($num,$offset=0){
		$this->limit = $offset.','.$num;
		return $this;
	}

	public function field($arr=''){
		if(is_array($arr)){
			$this->field = implode(',', $arr);
		}
		if(is_string($arr)){
			$this->field = $arr;
		}
		return $this;
	}

	public function orderBy($arr=''){
		if(is_array($arr)){
			$this->order = implode(',',$arr);
		}
		if(is_string($arr)){
			$this->order = $arr;
		}
		return $this;
	}
	public function select($arr=[]){
		
		$sql = "select ";
		$sql.=$this->mergeSql();
		//var_dump($sql);
		return $this->exec($sql,$arr);
		

	}
	public function mergeSql($kw='select'){
		// field where limit order
		$sql = '';
		if($kw !== 'delete'){
			if($this->field){
				$sql.= $this->field;
			}else{
				$sql.= $this->fields;
			}
		}
		if($this->tableName){
			$sql.=' from '.$this->tableName;
		}
		if($this->where){
			$sql.=' where '.$this->where;
		}
		if($this->order){
			$sql.=' order by '.$this->order;
		}
		if($this->limit){
			$sql.=' limit '.$this->limit;
		}
		return $sql;
	}

	public function table($tableName=''){
		if($tableName){
			$this->tableName = $tableName;
		}
		return $this;
	}

	public function setTableName(){
		// 默认情况下 直接获取类名
		/*echo str_replace(__NAMESPACE__."\\","", __CLASS__);*/
		return strtolower(ltrim(strrchr(get_class($this),'\\'),'\\'));
	}

	public function exec($sql,$arr=[],$keyw='select'){
		if(is_array($arr) && count($arr)>0){
			$stm = $this->connect->prepare($sql);
			foreach($arr as $key=>$value){
				$stm->bindValue($key,$value);
			}
			$stm->execute();
			return $stm;
		}
		if($keyw=='select'){
			return $this->connect->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
		}
		return $this->connect->exec($sql);
	}

}