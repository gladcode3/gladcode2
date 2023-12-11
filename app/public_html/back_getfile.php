<?php
	if (isset($_POST['action'])){
		$action = $_POST['action'];
		$path = "/home/gladcode/user";
		
		if ($action == "FILE"){
			$href = $_POST['href'];

			$file = "NULL";
			if (file_exists($href))
				$file = file_get_contents ($href);
			
			echo $file;
		}
		elseif ($action == "DIR"){
			$dir = $_POST['dir'];
			$path .= "/$dir";

			$files = scandir($path);
			$files_only = Array();
			$folders_only = Array();
			foreach ($files as $file){
				if (is_dir("$path/$file")){
					if ($file != "." && $file != "..")
						array_push($folders_only, $file);
				}
				else
					array_push($files_only, $file);
			}
			echo implode("|",$folders_only) . "||" . implode("|",$files_only);
		}
		elseif ($action == "NEWDIR"){
			$name = $_POST['name'];
			$dir = $_POST['dir'];

			system("mkdir \"$path/$dir/$name\"");
		}
		elseif ($action == "REMOVE"){
			$name = $_POST['name'];
			$dir = $_POST['dir'];
			
			system("rm -rf \"$path/$dir/$name\"");
		}
		elseif ($action == "SAVE"){
			$name = $_POST['name'];
			$dir = $_POST['dir'];
			$text = $_POST['text'];
			
			file_put_contents("$path/$dir/$name","$text");
		}
		elseif ($action == "SYNC"){
			$path = $_POST['path'];
			$text = $_POST['text'];
			
			file_put_contents("$path","$text");
		}
	}
	
?>