<?php 		

namespace libs;

class Error {


		public static function userError($errno,$errstr,$errfile,$errline)
		{
			self::writeLog( $errno, $errstr, $errfile, $errline );
		}

		public static function exceptionError($e)
		{
			echo $e->getMessage();die;
		}

		public static function stopError()
		{
			$error = error_get_last();
			echo '<pre>';
			print_r($error);
			// if( error_get_last() )
			// {
			// 	switch ($error['type']) {
			// 		case 'value':
			// 			# code...
			// 			break;
					
			// 		default:
			// 			# code...
			// 			break;
			// 	}
			// }
		}
		public static function writeLog( $errno, $errstr, $errfile, $errline  )
		{
			
			$str ='错误级别'.$errno."\r\n";
			$str.='错误消息'.$errstr."\r\n";
			$str.='错误文件'.$errfile."\r\n";
			$str.='错误行号'.$errline."\r\n";
			$str.="\r\n";
		echo $str;
			if( !is_dir(APP_PATH.'/runtime/logs/') )
			{
				mkdir(APP_PATH.'/runtime/logs/',0777);
			}		

			error_log($str, 3, APP_PATH.'/runtime/logs/mvc.log');

		}


}




 ?>