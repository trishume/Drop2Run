<?php
	ini_set('display_errors','On');
	$langs = array(
		'.bf' => 12,
		'.c' => 11,
		'.cs' => 27,
		'.cpp' => 1,
		'.d' => 102,
		'.erl' => 36,
		'.go' => 114,
		'.hs' => 21,
		'.java' => 10,
		'.js' => 112,
		'.lua' => 26,
		'.m' => 43,
		'.pl' => 54,
		'.php' => 29,
		'.py' => 116,
		'.r' => 117,
		'.rb' => 17,
		'.ss' => 33,
		'.sql' => 40,
		'.txt' => 62,
		'.vb' => 101,
		'.ws' => 6
	);
	function get_type_name($fname) {
		global $langs;
		$ext = pathinfo($fname, PATHINFO_EXTENSION);
		$type = $langs['.' . $ext];
		if(!$type) {
			$type = 62; //text file
		}
		return $langs['.' . $ext];
	}
	function run_program($client,$program,$type = 62,$input = "") {
		if($type != 62 && $client && strlen($program)){
			$result = $client->createSubmission("thume", "drop2run",$program,$type,$input,true,true);
			$finished = false;
			while(!$finished) {
				sleep(2);
				$ready = $client->getSubmissionStatus("thume", "drop2run",$result["link"]);
				if($ready["status"] == "0") {
					if($ready["result"] == "15") {
						$prog_res = $client->
							getSubmissionDetails("thume", "drop2run",$result["link"],false,false,true,true,false);
						return $prog_res["output"];
					} else {
						return "Program failed to run with error code: " . $ready["status"];
					}
					$finished = true;
				}
				
			}
		} else {
			return $program;
		}
	}
	function handle_request() {
		$client = new SoapClient("http://ideone.com/api/1/service.wsdl");
		$program = file_get_contents($_FILES['Filedata']['tmp_name']);
		print(
			json_encode(
				array(
					'testval' => "hi!!",
					'file' => $_FILES['Filedata']['name'],
					'program' => $program,
					'output' => run_program($client,$program,get_type_name($_FILES['Filedata']['name'])),
					'error' => 0
				)
			)
		);
	}
	function test() {
		$client = new SoapClient("http://ideone.com/api/1/service.wsdl");
		echo "hi ";
		$program = "puts 'hi'";
		echo get_type_name("test.rb");
		echo run_program($client,$program,get_type_name("test.rb"));
		echo "done.";
	}
	//test();
	handle_request();
	
?>

