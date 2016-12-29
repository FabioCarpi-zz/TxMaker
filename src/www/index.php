<?php
require_once("system.php");
require_once("transactions.php");

if(isset($_GET["action"])){
	if($_GET["action"] == "update"){
		$tx = new Transaction();
		for($i = 0; $i < $_GET["inputs"]; $i++){
			if(isset($_GET["txid".$i])){
				$tx->InputAdd(
					trim($_GET["txid".$i]),
					trim($_GET["vout".$i]),
					trim($_GET["scriptsighex".$i]),
					trim($_GET["scriptpubkey".$i]),
					trim($_GET["sequence".$i])
				);
			}
		}
		for($i = 0; $i < $_GET["outputs"]; $i++){
			if(isset($_GET["value".$i])){
				if(empty($_GET["address".$i]) == false or empty($_GET["scriptpubkeyhex".$i]) == false){
					$tx->OutputAdd(
						str_replace(".", "", trim($_GET["value".$i])),
						trim($_GET["address".$i]),
						trim($_GET["scriptpubkeyhex".$i])
					);
				}
			}
		}
		$tx->LockSet(trim($_GET["lock"]));

		if(!empty($_GET["priv"])){
			$key = new Keys();
			$key->WifSet($_GET["priv"]);
			$tx->Sign($key, $i);
		}

		$back = "inputs=".$_GET["inputs"]."&outputs=".$_GET["outputs"];
		for($i = 0; $i < $_GET["inputs"]; $i++){
			$temp = $tx->InputGet($i);
			$back .= "&txid".$i."=".$temp["txid"];
			$back .= "&vout".$i."=".$temp["vout"];
			$back .= "&scriptsighex".$i."=".$temp["scriptsighex"];
			$back .= "&scriptsigasm".$i."=".$temp["scriptsigasm"];
			$back .= "&scriptpubkey".$i."=".$temp["scriptpubkey"];
			$back .= "&sequence".$i."=".$temp["sequence"];
			
		}
		for($i = 0; $i < $_GET["outputs"]; $i++){
			$temp = $tx->OutputGet($i);
			$back .= "&value".$i."=".$temp["value"];
			$back .= "&scriptpubkeyhex".$i."=".$temp["scriptpubkeyhex"];
			$back .= "&scriptpubkeyasm".$i."=".$temp["scriptpubkeyasm"];
		}
		$back .= "&lock=".$tx->LockGet();
		$back .= "&hash=".$tx->HashGet();
		$back .= "&size=".$tx->SizeGet();
		$back .= "&raw=".$tx->RawGet();
		header("Location: index.php?".$back);
	}
}else{
	if(!isset($_GET["inputs"])){
		$_GET["inputs"] = 0;
	}
	if(!isset($_GET["outputs"])){
		$_GET["outputs"] = 0;
	}
	require_once("head.php");?>
	<form name="tx" method="get" action="index.php">
	<input type="hidden" name="action" value="update">
	<table>
		<tr>
			<td>Version:</td>
			<td>1</td>
		</tr>
		<tr>
			<td>Inputs:</td>
			<td><input type="text" name="inputs" size="1" value="<?php echo isset($_GET["inputs"])? $_GET["inputs"]: 0;?>"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><?php
				for($i = 0; $i < $_GET["inputs"]; $i++){?>
					<table style="border: solid 1px #000">
						<tr>
							<td>Tx id:</td>
							<td><input type="text" name="txid<?php echo $i;?>" size="75" value="<?php
								if(isset($_GET["txid".$i])){
									echo $_GET["txid".$i];
								}
							?>"></td>
						</tr>
						<tr>
							<td>Vout:</td>
							<td><input type="text" name="vout<?php echo $i;?>" size="1" value="<?php
							if(isset($_GET["vout".$i])){
								echo $_GET["vout".$i];
							}?>"></td>
						</tr>
						<tr>
							<td>ScriptSig<br>Hex:</td>
							<td>
								<textarea name="scriptsighex<?php echo $i;?>" cols="60" rows="3"><?php
									if(isset($_GET["scriptsighex".$i])){
										echo $_GET["scriptsighex".$i];
									}
								?></textarea>
							</td>
						</tr>
						<tr>
							<td>ScriptSig<br>Asm:</td>
							<td><textarea cols="60" rows="3"><?php
								if(isset($_GET["scriptsigasm".$i])){
									echo $_GET["scriptsigasm".$i];
								}?></textarea>
							</td>
						</tr>
						<tr>
							<td>ScriptPubkey:</td>
							<td>
								<textarea name="scriptpubkey<?php echo $i;?>" cols="60" rows="3"><?php
									if(isset($_GET["scriptpubkey".$i])){
										echo $_GET["scriptpubkey".$i];
									}
								?></textarea>
							</td>
						</tr>
						<tr>
							<td>Sequence:</td>
							<td>
								<input type="text" name="sequence<?php echo $i;?>" size="6" value="<?php
								if(isset($_GET["sequence".$i])){
									echo $_GET["sequence".$i];
								}else{
									echo "FFFFFFFF";
								}?>">
								<a href="#" onclick="document.tx.sequence<?php echo $i;?>.value='FFFFFFFF'">&lt;&lt; Final</a>
							</td>
						</tr>
					</table><?php
				}?>
			</td>
		</tr>
		<tr>
			<td>Outputs:</td>
			<td><input type="text" name="outputs" size="1" value="<?php echo isset($_GET["outputs"])? $_GET["outputs"]: 0;?>"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<table style="border: solid 1px #000"><?php
					for($i = 0; $i < $_GET["outputs"]; $i++){?>
						<tr>
							<td>Address:</td>
							<td><input type="text" name="address<?php echo $i;?>" size="45" value="<?php
								if(isset($_GET["address".$i])){
									echo $_GET["address".$i];
								}
							?>"></td>
						</tr>
						<tr>
							<td>Value:</td>
							<td><input type="text" name="value<?php echo $i;?>" size="10" value="<?php
							if(isset($_GET["value".$i])){
								echo $_GET["value".$i];
							}?>"></td>
						</tr>
						<tr>
							<td>ScriptPubkey<br>Hex:</td>
							<td>
								<textarea name="scriptpubkeyhex<?php echo $i;?>" cols="60" rows="3"><?php
									if(isset($_GET["scriptpubkeyhex".$i])){
										echo $_GET["scriptpubkeyhex".$i];
									}
								?></textarea>
							</td>
						</tr>
						<tr>
							<td>ScriptPubkey<br>Asm:</td>
							<td><textarea cols="60" rows="3"><?php
								if(isset($_GET["scriptpubkeyasm".$i])){
									echo $_GET["scriptpubkeyasm".$i];
								}?></textarea>
							</td>
						</tr><?php
					}?>
				</table>
			</td>
		</tr>
		<tr>
			<td>Locktime:</td>
			<td><input type="text" name="lock" size="6" value="<?php echo isset($_GET["lock"])? $_GET["lock"]: 0;?>"></td>
		</tr>
	</table><br>
	Privkey to sign: <input type="text" name="priv" size="60"><br>
	<br>
	<input type="submit" value="Update">
	</form><br>
	Hash: <?php echo isset($_GET["hash"])? $_GET["hash"]: null;?><br>
	<br>
	Raw: (<?php echo isset($_GET["size"])? $_GET["size"]: 0;?> bytes)<br>
	<textarea cols="80" rows="5"><?php echo isset($_GET["raw"])? $_GET["raw"]: null;?></textarea><?php
}