<?php
namespace models;
use libs\Model;

class Joke extends Model{
	
	public function setTableName(){
		return 'joke';
	}
}