<?php
	if (isset($_POST['delete'])){
		//$file = $_POST['delete'];
		//unlink("/home/gladcode/public_html/temp/$file");
	}
	else{
		$name = $_FILES["file"]['name'];
		//$token = md5(uniqid(rand(), true) . $name);
		$code = file_get_contents ($_FILES["file"]['tmp_name']);
		//file_put_contents("/home/gladcode/public_html/temp/$token",$code);
		echo $code;
	}
?>
