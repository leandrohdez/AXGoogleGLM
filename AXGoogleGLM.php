<?php		
	// AXGoogleGLM 
	// Get the latitude and longitude using the GLM service
	// 
	// by Leandro Hernandez, 2013
	
	// PARAMS
	//	mcc: mobile country code
	//	mnc: mobile network code
	//	lac: location area code
	//	cid: cell id
	
	function AXGoogleGLM($mcc, $mnc, $lac, $cid){
		
		$data =
                "\x00\x0e" . // Function Code
                "\x00\x00\x00\x00\x00\x00\x00\x00" . //Session ID
                "\x00\x00" . // Contry Code
                "\x00\x00" . // Client descriptor
                "\x00\x00" . // Version
                "\x1b" . // Op Code
                "\x00\x00\x00\x00" . // MNC
                "\x00\x00\x00\x00" . // MCC
                "\x00\x00\x00\x03" .
                "\x00\x00" .
                "\x00\x00\x00\x00" . //CID
                "\x00\x00\x00\x00" . //LAC
                "\x00\x00\x00\x00" . //MNC
                "\x00\x00\x00\x00" . //MCC
                "\xff\xff\xff\xff" . // ??
                "\x00\x00\x00\x00"  // Rx Level
        ;
	
		// Transform params to Hexa
		$mcc = substr("00000000" . $mcc, -8);
        $mnc = substr("00000000" . $mnc, -8);
        $lac = substr("00000000" . $lac, -8);
        $cid = substr("00000000" . $cid, -8);
	
		$init_pos = strlen($data);
        $data[$init_pos - 38] = pack("H*", substr($mnc, 0, 2));
        $data[$init_pos - 37] = pack("H*", substr($mnc, 2, 2));
        $data[$init_pos - 36] = pack("H*", substr($mnc, 4, 2));
        $data[$init_pos - 35] = pack("H*", substr($mnc, 6, 2));
        $data[$init_pos - 34] = pack("H*", substr($mcc, 0, 2));
        $data[$init_pos - 33] = pack("H*", substr($mcc, 2, 2));
        $data[$init_pos - 32] = pack("H*", substr($mcc, 4, 2));
        $data[$init_pos - 31] = pack("H*", substr($mcc, 6, 2));
        $data[$init_pos - 24] = pack("H*", substr($cid, 0, 2));
        $data[$init_pos - 23] = pack("H*", substr($cid, 2, 2));
        $data[$init_pos - 22] = pack("H*", substr($cid, 4, 2));
        $data[$init_pos - 21] = pack("H*", substr($cid, 6, 2));
        $data[$init_pos - 20] = pack("H*", substr($lac, 0, 2));
        $data[$init_pos - 19] = pack("H*", substr($lac, 2, 2));
        $data[$init_pos - 18] = pack("H*", substr($lac, 4, 2));
        $data[$init_pos - 17] = pack("H*", substr($lac, 6, 2));
        $data[$init_pos - 16] = pack("H*", substr($mnc, 0, 2));
        $data[$init_pos - 15] = pack("H*", substr($mnc, 2, 2));
        $data[$init_pos - 14] = pack("H*", substr($mnc, 4, 2));
        $data[$init_pos - 13] = pack("H*", substr($mnc, 6, 2));
        $data[$init_pos - 12] = pack("H*", substr($mcc, 0, 2));
        $data[$init_pos - 11] = pack("H*", substr($mcc, 2, 2));
        $data[$init_pos - 10] = pack("H*", substr($mcc, 4, 2));
        $data[$init_pos - 9] = pack("H*", substr($mcc, 6, 2));
	
		if ((hexdec($cid) > 0xffff) && ($mcc != "00000000") && ($mnc != "00000000")) {
            $data[$init_pos - 27] = chr(5);
        } else {
            $data[$init_pos - 24] = chr(0);
            $data[$init_pos - 23] = chr(0);
        }

        $context = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/binary\r\n"
                . "Content-Length: " . strlen($data) . "\r\n",
                'content' => $data
            )
        );
	
		$xcontext = stream_context_create($context);
	
		$str = file_get_contents("http://www.google.com/glm/mmap", FALSE, $xcontext);
        
        $latlong = array();
        if (strlen($str) > 10) {
            $lat_tmp = unpack("l", $str[10] . $str[9] . $str[8] . $str[7]);
            $lat = $lat_tmp[1] / 1000000;
            $lon_tmp = unpack("l", $str[14] . $str[13] . $str[12] . $str[11]);
            $lon = $lon_tmp[1] / 1000000;
            $latlong['lat'] = $lat; $latlong['long'] = $lon;
            return $latlong;
        } else {
            return "Not found!";
        }
		return false;
	}

        
    // Demostration:
	// *****************
	// $coordinates = AXGoogleGLM("214","07","0335","5A0C");
	// die(json_encode($coordinates));
        
		
?>