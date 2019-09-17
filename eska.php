<?php
/*
	c)2017, PiDev (License BSD)
	eskaGO Hack v1.0
	https://4programmers.net/Forum/PHP/297180-jak_sluchac_radia_eska_na_winampie_screamer_radio_radiosure_itd_po_zmianach_29_wrzesnia_2017
*/

$addr = "http://www.eskago.pl/radio/eska-warszawa"; //Link do stacji
$title = "Eska Warszawa"; //Nazwa stacji

$eskago = file_get_contents($addr, false, stream_context_create(array("http"=>array("method"=>"GET","header"=>"User-Agent: ".$_SERVER['HTTP_USER_AGENT']))));
list($ver,$code,$message) = explode(" ",$http_response_header[0],3);

if($code == 200) {
	if(preg_match("/var streamUrl = \'([A-Za-z0-9\?\-\.\=\/\:\&]+)\';/i",$eskago,$result)) {
		if(isset($_GET['format']) && strtolower($_GET['format']) == 'aac') { $eskago = str_replace('.mp3','.aac',$eskago); }
		if(isset($_GET['out']) && !empty($_GET['out'])) {
			$out = strtolower($_GET['out']);
			if($out == 'pls') {
				// Playlist PLS
				header("Content-Type: audio/x-scpls");
				header('Content-disposition: attachment; filename="'.$title.'.pls"');
				echo "[Playlist]\nNumberOfEntries=1\nFile1=".$eskago."\nTitle1=".$title."\nLength1=-1\nVersion=2";
			} else if($out == 'm3u') {
				// Playlist M3U 
				header("Content-Type: application/x-mpegurl");
				header('Content-disposition: attachment; filename="'.$title.'.m3u"');
				echo "#EXTM3U".PHP_EOL."#EXTINF:-1, ".$title."\n".$eskago;
			}
		} else {
			header("Location: ".$eskago);
		}
	} else {
		echo "Błąd z wyszukaniem linku lub stacja tymczasowo offline";
	}
} else {
	echo "Błąd(".$code."): ".$message;
}
?>
