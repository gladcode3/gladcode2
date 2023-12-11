<?php
	function getTempName(){
		$tempname = "";
		for ($i = 0 ; $i < 10 ; $i++){
			$tempname .= rand(0,9);
		}
		return $tempname;
	}

	if ($_POST['code'] != ""){
		$output = ""; 
		$error = "";
		$sberror = "";
		
		$code = $_POST['code'];
		$input = $_POST['input'];
		$foldername = getTempName();
		$path = "/home/gladcode";
		$target_file = "$path/temp/$foldername/file.c";
		system("mkdir $path/temp/$foldername && cp $path/script/compilerun.sh $path/temp/$foldername/compilerun.sh && echo \"$input\" > $path/temp/$foldername/input.txt");
		file_put_contents($target_file, $code);
		system("$path/script/callscript.sh $foldername &>> $path/temp/$foldername/error.txt");
		
		if (file_exists("$path/temp/$foldername/output.txt"))
			$output = file_get_contents ("$path/temp/$foldername/output.txt");
		if (file_exists("$path/temp/$foldername/error.txt"))
			$error = file_get_contents ("$path/temp/$foldername/error.txt");

		system("rm -rf $path/temp/$foldername");
		
		echo "$error|$output";
	}
?>
