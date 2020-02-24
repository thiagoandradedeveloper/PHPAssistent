<?php 

//-------------------------------------------------------------
// THALIB - v1.0
// Licença: MIT
//-------------------------------------------------------------- 

//Função para tirar o acento e cedilha de textos
//Function to remove the accent and cedilla of texts
function removeAccent($texto_){

	//change that
	$trocarIsso_ = array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ü','ú','ÿ','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','O','Ù','Ü','Ú','Ÿ','ç','Ç');

	//that is why
	$porIsso_    = array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','O','U','U','U','Y','c','C');

	$titletext_  = str_replace($trocarIsso_, $porIsso_, $texto_);

	return $titletext_;
}
	
//Gera uma string aleatária com um determinado tamanho definido
//Generates a random string with a defined size
function randomString($limit_){

	//Caracteres que irão constituir a string randonica
	//Characters that will constitute the random string
	$characters_ = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	$result_ = "";

	while(strlen($result_) < $limit_)

		//Seleciona um valor aleatório no meio da string
		//Select a random value in the middle of the string
		$result_ .= $characters_[mt_rand(0, strlen($characters_) - 1)];

	return $result_;
}

function validateAddress($Address_){
	
	if( ($Address_ == null) || empty($Address_) || ($Address_ == "/") ){
	
		return getcwd()."/";
	
	} else {

		$Address_ = json_encode($Address_);

		//change that
		$trocarIsso_ = array('\/','\\\\','"');

		//that is why
		$porIsso_    = array('\\','\\','');

		$Address_  = str_replace($trocarIsso_, $porIsso_, $Address_);

		if(substr($Address_, -1) != "\\"){ $Address_ .= "\\"; }
		
		return $Address_;
	}
}

//compactar arquivos
function zipFile($_dir,$namearquivo_,$_dirzip,$_namezip){
	
	if(empty($_namezip))
		$_namezip = $namearquivo_;

	if(strrchr($_namezip, ".") != ".zip")
		$_namezip .= ".zip";

	$_dir = validateAddress($_dir);
	$_dirzip = validateAddress($_dirzip);
	
	$zip = new ZipArchive();
	$zip -> open($_dirzip.$_namezip, ZipArchive::CREATE);
	$zip -> addfile($_dir.$namearquivo_,$namearquivo_);
	$zip -> close();
	
	return validateAddress($_dirzip).$_namezip;

}

//Descompactar arquivos
function unzipFile($_dirzip,$_namezip,$_dirext){
	
	if(strrchr($_namezip, ".") != ".zip"){
		
		return "not enabled";
	
	} else {
		
		$_dirzip = validateAddress($_dirzip);
		$_dirext = validateAddress($_dirext);

		$zip = new ZipArchive();
		$zip -> open($_dirzip.$_namezip);
		$zip -> extractTo($_dirext);
		$zip -> close();
	}
	
	return $_dirext;
}

//Cria e grava um arquivo xml	
function thaXML($nomeArquivoXML,$tags,$arrayValue){

	$tagsXML = explode("|", $tags);
	
	$contador = count($arrayValue) / count($tagsXML);
	
	if(count($arrayValue) % count($tagsXML) == 0){
	
		//gravando arquivo xml

		$meus_links = array();
		
		$contador1 = 0;

		for($i = 0; $i < $contador; $i++){

			for($a = 0; $a < count($tagsXML); $a++){
			
				$meus_links[$i][$tagsXML[$a]] = $arrayValue[$contador1];
				$contador1++;
			}
		}

		// Receberá todos os dados do XML
		$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>';

		// A raiz do meu documento XML
		$xml .= '<links>';

		for ( $i = 0; $i < count( $meus_links ); $i++ ) {

			$xml .= '<link>';

			foreach($tagsXML as $value)
				$xml .= "<$value>".$meus_links[$i][$value]."</$value>";

			$xml .= '</link>';
		}

		$xml .= '</links>';

		// Escreve o arquivo
		$fp = fopen($nomeArquivoXML, 'w+');
		fwrite($fp, $xml);
		fclose($fp);
		
	} else {
	
		return 0;
	
	}

}

//Lendo o arquivo xml
function redaerXML($nomeArquivoXML,$tags){

	// Faz o load do arquivo XML e retorna um objeto
	$arquivo_xml = simplexml_load_file($nomeArquivoXML);
	
	$strngReturn = "";

	$tagsXML = explode("|", $tags);

	// Loop para ler o objeto
	for ( $j = 0; $j < count( $arquivo_xml ); $j++ ) {
		
		foreach($tagsXML as $valor)
			// Imprime o valor o valor das tags
			$strngReturn .= $arquivo_xml->link[$j]-> $valor."|";

	}

	return explode("|",substr($strngReturn,0,strlen($strngReturn)-1));
}

//Apaga um diretorios e seu conteudo
function apagarDir($dir_){
	
	$dir_ = validateAddress($dir_);

	//abre o diretorio para manipulação
	$abreDir = opendir($dir_);

	while(false !== ($file = readdir($abreDir))){
		
		if($file==".." || $file==".")
			continue;
		
		if(is_dir($cFile=($dir_."/".$file)))
			apagarDir($cFile);
		
		else if(is_file($cFile))
			@unlink($cFile);
	
	}
	
	closedir($abreDir);
	
	rmdir($dir_);

}

//Copia diretorios e tudo dentro dela, ou todo conteudo de uma pasta
function copiadir($DirFont, $DirDest){
	
	$DirFont = validateAddress($DirFont);
	$DirDest = validateAddress($DirDest);
	
	//Cria uma pasta no local especificado se ela não existe
	@mkdir($DirDest, 0755);
	
	//abre o diretorio para manipulação
	if ($dirAberto = opendir($DirFont)) {
		
		//Enquanto ele for diferente de false  
		while (false !== ($Arq = readdir($dirAberto))) {

			if($Arq != "." && $Arq != ".."){

				$PathIn  = $DirFont.$Arq;
				$PathOut = $DirDest.$Arq;

				if(is_dir($PathIn))
					CopiaDir($PathIn, $PathOut);

				elseif(is_file($PathIn))
					copy($PathIn, $PathOut);

			}
		}

		closedir($dirAberto);
		
		if(is_dir($DirDest)) return true;
		else return false;
	}
}


/*
	cria uma nova pasta, padrao windows
	
	código de retorno:

	0 - pasta nao existia e nova pasta nao foi criada (erro)
	1 - pasta nao existia e nova pasta fio criada com sucesso
	2 - pasta existia e não foi criada outra
	3 - pasta existia e foi criada outra pasta com sucesso
	
*/
function mkdirphp($_nomedir){
	
	$_nomedir  = str_replace("//", "\/", $_nomedir);

	$i = 2;

	if(!file_exists($_nomedir)){

		@mkdir($_nomedir, 0755);

		//verifica se a pasta foi criada
		if(file_exists($_nomedir)) return 1;
		else return 0;

	} else {

		while(file_exists($_nomedir.$i)){
			$i++;
		}

		@mkdir($_nomedir.$i, 0755);

		//verifica se a pasta foi criada
		if(file_exists($_nomedir.$i)) return 3;
		else return 2;

	}
}

/*

REDIMENCIONA IMAGEM (obs: so funciona para bitmap, jpg, png e gif)

Exemplo de endereço usado nos parametros desta função:

	C:\Users\Jane\Desktop\meu FRAMEWORK\root\bibliotecas\pre-prontas\tha\teste

Paremetros:

	resizeImage( [1], [2], [3], [4], [5], [6]);

	[1] Endereço de origem da imagem que será redimencionada	
	[2] Nome da imagem, com a extenção, que sera redimencionada
	[3] Endereço de destino onde ficará a imagem redimencionada
	[4] Largura em pixels
	[5] Altura em pixels
	[6] Mantem a proporção em relação ao tamanho da imagem original
		exemplo 1: 0.3,  redimencionará a  30% da imagem original e irá ignorar ignorar os parametros [4] e [5]
		exemplo 2: 1.5,  redimencionará a 150% da imagem original e irá ignorar ignorar os parametros [4] e [5]
		exemplo 2: 0.75, redimencionará a  75% da imagem original e irá ignorar ignorar os parametros [4] e [5]

REDIMENSES IMAGE (note: only works for bitmap, jpg, png and gif)

Example of the address used in the parameters of this function:

	C:\Users\Jane\Desktop\my FRAMEWORK\root\libraries\pre-made\tha\test

Parameters:

	resizeImage ([1], [2], [3], [4], [5], [6]);

	[1] Source address of the image that will be resized
	[2] Image name, with the extension, which will be resized
	[3] Destination address where the resized image will be
	[4] Width in pixels
	[5] Height in pixels
	[6] Maintains the aspect ratio of the original image
		example 1: 0.3, will resize to 30% of the original image and will ignore ignore parameters [4] and [5]
		example 2: 1.5, will resize 150% of the original image and will ignore ignore parameters [4] and [5]
		example 2: 0.75, will resize 75% of the original image and ignore ignoring parameters [4] and [5]
*/

function resizeImage($thatec_dir_src,$thatec_file,$thatec_dir_thumb,$thatec_resize_x,$thatec_resize_y,$thatec_prop){

	//faz o tratamento dos endereços 
	$thatec_dir_src   = validateAddress($thatec_dir_src);
	$thatec_dir_thumb = validateAddress($thatec_dir_thumb);

	if(is_file($thatec_dir_src.$thatec_file) && file_exists($thatec_dir_src.$thatec_file)){

		$array_ = array('.png', '.jpg', '.jpeg', '.gif');
		
		$extencao_ = strtolower(strrchr($thatec_file, "."));

		//verifica se a extenção é válida
		if( in_array($extencao_, $array_) ){

			$dir_thatec = $thatec_dir_src.$thatec_file;

			$tamanho = getimagesize($dir_thatec);

			//o condicional abaixo cria uma nova imagem a partir de um arquivo URL de acordo com a extenção da imagem
			if( strtolower(strrchr($thatec_file, ".")) == ".jpg" || strtolower(strrchr($thatec_file, ".")) == ".jpeg" )
				$img = imagecreatefromjpeg($dir_thatec);			

			else if( strtolower(strrchr($thatec_file, ".")) == ".png" )
				$img = imagecreatefrompng($dir_thatec);

			else if( strtolower(strrchr($thatec_file, ".")) == ".gif" )
				$img = imagecreatefromgif($dir_thatec);			

			else if( strtolower(strrchr($thatec_file, ".")) == ".bmp" )
				$img = imagecreatefromwbmp($dir_thatec);

			else
				exit;
			
			//redimencionamento da imagem
			if(!empty($thatec_prop)){

				$thatec_tamanho = getimagesize($thatec_dir_src.$thatec_file);

				$thatec_largura = $thatec_tamanho[0];
				$thatec_altura  = $thatec_tamanho[1];

				$thatec_resize_y = (int)($thatec_altura  * $thatec_prop);
				$thatec_resize_x = (int)($thatec_largura * $thatec_prop);

				$thumb = imagecreatetruecolor($thatec_resize_x,$thatec_resize_y);

			} else {

				$thumb = imagecreatetruecolor($thatec_resize_x,$thatec_resize_y);

			}

			//coloca transparencia nas imagens
			imagealphablending ( $thumb ,  false  );
			imagesavealpha ( $thumb ,  true  );

			//copia uma imagem e redimenciona
			imagecopyresampled($thumb,$img,0,0,0,0,$thatec_resize_x,$thatec_resize_y,$tamanho[0],$tamanho[1]);

			//envia um arquivo para pasta
			if( strtolower(strrchr($thatec_file, ".")) == ".jpg" || strtolower(strrchr($thatec_file, ".")) == ".jpg" )
				imageJPEG($thumb,$thatec_dir_thumb.$thatec_file);			

			else if( strtolower(strrchr($thatec_file, ".")) == ".png" )
				imagepng($thumb,$thatec_dir_thumb.$thatec_file);

			else if( strtolower(strrchr($thatec_file, ".")) == ".bmp" )
				imagewbmp($thumb,$thatec_dir_thumb.$thatec_file);

			else if( strtolower(strrchr($thatec_file, ".")) == ".gif" )
				imagegif($thumb,$thatec_dir_thumb.$thatec_file);
			
			//esvazia as imagens da memória
			imagedestroy($img);
			imagedestroy($thumb);

			return true;

		} else { return false; }
	}
}

//LER TODOS OS ARQUIVOS DENTRO DE UMA PASTA 
function listFile($diretorio_thatec,$tipo_thatec){
	
	if( ($tipo_thatec == "*") || empty($tipo_thatec) ) $tipo_thatec = "all";

	$diretorio_thatec = validateAddress($diretorio_thatec);

	//define o caminho do diretório

	$dir_thatec = $diretorio_thatec;

	$scan = scandir($dir_thatec);

	if(count($scan) < 3) { 

		$msg[] = "Empyt";
		return $msg; 

	} else {

		//listar arquivos

		if(strripos($tipo_thatec, ".") !== false){

			$confimacao_ = false;

			foreach($scan as $valor ){

				if(strripos($valor, $tipo_thatec) !== false){
				
					$confimacao_ = true;
					break;
				
				}			
			}

			if($confimacao_)
				$files_thatec = glob($dir_thatec."/*".$tipo_thatec) or die ("ERRO AO ACESSAR A PASTA");
			else
				$files_thatec = array(null);
		
		} else {
			
			$files_thatec = glob($dir_thatec."/*") or die ("ERRO AO ACESSAR A PASTA");
		}

		//permorre a lista

		foreach($files_thatec as $file_thatec) {

			$file2_thatec = str_replace($diretorio_thatec."/","",$file_thatec);
			$file2_thatec = str_replace($dir_thatec."/","",$file_thatec);

			if(is_dir($file_thatec))
				$pastas_thatec[]    = utf8_decode($file2_thatec);
			
			else
				$arquivos_thatec[]  = utf8_decode($file2_thatec);

			$arquivo_final_thetec[] = utf8_decode($file2_thatec);

		}

		//retorna a lista pelo tipo de arquivo

		if($tipo_thatec == "dir") { return $pastas_thatec; }
		else if($tipo_thatec == "file"){ return $arquivos_thatec; }
		else if($tipo_thatec == "all") { return $arquivo_final_thetec; }
		else { return $arquivo_final_thetec; }

	}
}


//UPLOAD DE ARQUIVOS
function uploadFile($name_thatec,$dir0_thatec,$tipo_thatec,$thatec_size,$thatec_rename){

	//Seleciona os tipos de extençoes permitidas

	if(($tipo_thatec == "image")or($tipo_thatec == "img")){ $ext_thatec = array('.gif','.bmp','.jpg','jpeg','.png','.JPG','.PNG','.GIF','JPEG','.BMP'); }
	if($tipo_thatec  == "doc"){ $ext_thatec = array('.doc','.pdf','.txt','.odt','docx','.sxw','.DOC','.PDF','.TXT','.ODT','DOCX','.SXW'); }
	if($tipo_thatec  == "compression"){ $ext_thatec = array('.zip','.rar','rar5','.ZIP','.RAR','.RAR5'); }
	if($tipo_thatec  == "imageDisc"){ $ext_thatec = array('.iso','.ISO'); }
	if($tipo_thatec  == "exec"){ $ext_thatec = array('.exe','.rum','run','.EXE','.RUM','.RUN'); }
	if(is_array($tipo_thatec)){ $ext_thatec = $tipo_thatec; }

	//verifica o tamanho do arquivo

	if($dir0_thatec == getcwd()){ $dir0_thatec = getcwd()."/"; }
	if($dir0_thatec == "/"){ $dir0_thatec = getcwd()."/"; }
	if($dir0_thatec == null){ $dir0_thatec = getcwd()."/"; }

	//condicao final do upload

	$condicao = false;

	//faz o upload do arquivo independente do tamanho

	$thatec_nome_upload = $name_thatec["name"];

	if(is_array($thatec_nome_upload)){ 

		for($i = 0; $i < count($name_thatec["name"]); $i++){ echo "ok";

			if($thatec_size == "all"){

				//Faz upload do arquivo

				if($tipo_thatec != "all"){

					if ( ($name_thatec["name"][$i] != "") and (in_array(substr($name_thatec["name"][$i], -4), $ext_thatec)) ){

						$dir_thatec = $dir0_thatec.$name_thatec["name"][$i];
						$condicao = move_uploaded_file($name_thatec["tmp_name"][$i], $dir_thatec);
						if(!$condicao){break;}
					
					}

				} else {

					$dir_thatec = $dir0_thatec.$name_thatec["name"][$i];
					$condicao = move_uploaded_file($name_thatec["tmp_name"][$i], $dir_thatec);
					if(!$condicao){break;}

				}
			}

			//limita o upload pelo tamanho

			if($name_thatec["size"][$i] <= $thatec_size){

				//Faz upload do arquivo

				if($tipo_thatec != "all"){

					if ( ($name_thatec["name"][$i] != "") and (in_array(substr($name_thatec["name"][$i], -4), $ext_thatec)) ){

						$dir_thatec = $dir0_thatec.$name_thatec["name"][$i];
						$condicao = move_uploaded_file($name_thatec["tmp_name"][$i], $dir_thatec);
						if(!$condicao){break;}

					}

				} else {

					$dir_thatec = $dir0_thatec.$name_thatec["name"][$i];
					$condicao = move_uploaded_file($name_thatec["tmp_name"][$i], $dir_thatec);
					if(!$condicao){break;}

				}
			}
		}

	} else {

			if($thatec_size == "all"){

				//Faz upload do arquivo

				if($tipo_thatec != "all"){

					if ( ($name_thatec["name"] != "") and (in_array(substr($name_thatec["name"], -4), $ext_thatec)) ){

						$dir_thatec = $dir0_thatec.$name_thatec["name"];
						$condicao = move_uploaded_file($name_thatec["tmp_name"], $dir_thatec);

					}

				} else {

					$dir_thatec = $dir0_thatec.$name_thatec["name"];
					$condicao = move_uploaded_file($name_thatec["tmp_name"], $dir_thatec);

				}
			}

			//limita o upload pelo tamanho

			if($name_thatec["size"] <= $thatec_size){

				//Faz upload do arquivo

				if($tipo_thatec != "all"){

					if ( ($name_thatec["name"] != "") and (in_array(substr($name_thatec["name"], -4), $ext_thatec)) ){

						$dir_thatec = $dir0_thatec.$name_thatec["name"];
						$condicao = move_uploaded_file($name_thatec["tmp_name"], $dir_thatec);

					}

				} else {

					$dir_thatec = $dir0_thatec.$name_thatec["name"];
					$condicao = move_uploaded_file($name_thatec["tmp_name"], $dir_thatec);

				}
			}
		}
		
		//renomea um arquivo ao fazer upload
		if( (!empty($thatec_rename)) && file_exists($dir_thatec) && $dir_thatec != null ){
			
			$thatec_repeticao = count($name_thatec["name"]);
			
			if(is_array($name_thatec["name"]) ){
				
				$thatec_cont = 0;
				
				for($thatec_c = 0; $thatec_c < $thatec_repeticao;$thatec_c++){
				
					$thatec_variable = list_file_thatec($dir0_thatec,"file");
					
					$extencao = substr($name_thatec["name"][$thatec_c], -4);

					if($extencao == "jpeg"){ $extencao = ".jpeg"; }
					if($extencao == "JPEG"){ $extencao = ".JPEG"; }

					while(in_array($thatec_rename.$thatec_cont.$extencao,$thatec_variable)){

						$thatec_cont++;
						if(!in_array($thatec_rename.$thatec_cont.$extencao,$thatec_variable)){

							$condicao = rename($dir0_thatec.$name_thatec["name"][$thatec_c],$dir0_thatec.$thatec_rename.$thatec_cont.$extencao);
							$thatec_variable = list_file_thatec($dir0_thatec,"file");
							break;

						}
					}

					if(!in_array($thatec_rename.$thatec_cont.$extencao,$thatec_variable)){

						$condicao = rename($dir0_thatec.$name_thatec["name"][$thatec_c],$dir0_thatec.$thatec_rename.$thatec_cont.$extencao);
						$thatec_variable = list_file_thatec($dir0_thatec,"file");

					}

					$thatec_cont++;

				}
			
			} else {
			
				$thatec_cont = 0;

				$thatec_variable = list_file_thatec($dir0_thatec,"file");
			
				$extencao = substr($name_thatec["name"], -4);
				
				if($extencao == "jpeg") $extencao = ".jpeg";
				if($extencao == "JPEG") $extencao = ".JPEG";
				
				if(in_array($thatec_rename.$extencao,$thatec_variable)){
				
					if(!in_array($thatec_rename.$thatec_cont.$extencao,$thatec_variable)){

						$condicao = rename($dir0_thatec.$name_thatec["name"],$dir0_thatec.$thatec_rename.$thatec_cont.$extencao);

					} else {
					
						$thatec_cont++;

						while(in_array($thatec_rename.$thatec_cont.$extencao,$thatec_variable))	$thatec_cont++;
						
						$condicao = rename($dir0_thatec.$name_thatec["name"],$dir0_thatec.$thatec_rename.$thatec_cont.$extencao);
					}

				} else {
				
					$condicao = rename($dir0_thatec.$name_thatec["name"],$dir0_thatec.$thatec_rename.$extencao);
				
				}
			}
		}

	return $condicao;

}



?>













