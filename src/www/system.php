<?php
ini_set("error_reporting", E_ALL);
ini_set("html_errors", true);
ini_set("display_errors", true);
date_default_timezone_set("America/Sao_Paulo");
session_start();

GithubImport("FabioCarpi", "BitcoinPhpClass", "src/functions.php");
GithubImport("FabioCarpi", "BitcoinPhpClass", "src/keys.php");
GithubImport("FabioCarpi", "BitcoinPhpClass", "src/script.php");
GithubImport("FabioCarpi", "BitcoinPhpClass", "src/transactions.php");

function GithubImport($User, $Repo, $File, $Trunk = "master"){
	$file = basename($File);
	$server = file_get_contents("https://raw.githubusercontent.com/".$User."/".$Repo."/".$Trunk."/".$File);
	if(file_exists($file) == false){
		if($server === false){
			if(ini_get("display_errors") == true){
				die("Error: Could not possible to import dependents files from Github!");
			}else{
				return false;
			}
		}else{
			file_put_contents($file, $server);
			require_once($file);
		}
	}else{
		if($server !== false){
			if(hash_file("sha256", $file) != hash("sha256", $server)){
				file_put_contents($file, $server);
			}
		}
		require_once($file);
	}
}