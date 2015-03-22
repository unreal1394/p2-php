<?php
// {{{ dig2chsearch()
function dig2chsearch($query)
{
	global $_conf;

	parse_str($query, $query_arry);

	//$query_q = preg_replace('/(\s+)/' , '\+' ,$query_arry['q']);
	$query_arry['q'] = urlencode($query_arry['q']);

	$client = new HTTP_Client();
	$client->setDefaultHeader('User-Agent', 'Mozilla/5.0 (Windows NT 6.4; WOW64; Trident/7.0; .NET4.0E; .NET4.0C; rv:11.0) like Gecko');
	$code = $client->get($_conf['test.dig2ch_url'] . '?AndOr=' . $query_arry['AndOr'] . '&maxResult=' . $query_arry['maxResult'] . '&atLeast=1&Sort=' . $query_arry['Sort'] . '&Link=1&Bbs=all&924=' . $query_arry['924'] . '&json=1&keywords=' . $query_arry['q']);
	if (PEAR::isError($code)) {
		p2die($code->getMessage());
	} elseif ($code != 200) {
		p2die("HTTP Error - {$code}");
	}
	$response = $client->currentResponse();

    // æ•û‚ÌI‚Å‰½‚©áŠQ‚ª”­¶‚µ‚½‚çJSON‚ÉHTML‚ÌƒRƒƒ“ƒg‚ª¬‚´‚é‚Ì‚Å‚»‚Ì‘Îô
    $body = preg_replace("/<\!--.*-->/", "", $response['body']);

	$jsontest1 = json_decode($body, true);

	//mb_convert_variables('SHIFT-JIS','UTF-8',$jsontest1);
    $jsonerror = "";
	switch (json_last_error()) {
		case JSON_ERROR_NONE:
			$jsonerror = ' - No errors';
			break;
		case JSON_ERROR_DEPTH:
			$jsonerror = ' - Maximum stack depth exceeded';
			break;
		case JSON_ERROR_STATE_MISMATCH:
			$jsonerror = ' - Underflow or the modes mismatch';
			break;
		case JSON_ERROR_CTRL_CHAR:
			$jsonerror = ' - Unexpected control character found';
			break;
		case JSON_ERROR_SYNTAX:
			$jsonerror = ' - Syntax error, malformed JSON';
			break;
		case JSON_ERROR_UTF8:
			$jsonerror = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
		default:
			$jsonerror = ' - Unknown error';
			break;
	}
	if ($jsontest1 === NULL) {
		p2die("ŒŸõŒ‹‰Ê‚Ìæ“¾‚É¸”s‚µ‚Ü‚µ‚½".$jsonerror);
	}

	foreach ($jsontest1[result] as $jsontest2) {
        $result['threads'][$n1] = new stdClass;
		$result['threads'][$n1]->title = $jsontest2[subject];
		$result['threads'][$n1]->host = $jsontest2[server];
		$result['threads'][$n1]->bbs = $jsontest2[bbs];
		$result['threads'][$n1]->tkey = $jsontest2[key];
		$result['threads'][$n1]->resnum = $jsontest2[resno];
		$result['threads'][$n1]->ita = $jsontest2[ita];
		$result['threads'][$n1]->dayres = $jsontest2[ikioi];
		$n1++;
	}
	$result['modified'] = isset($response['body']['date'])? $response['body']['date'] : '';
	$result['profile']['regex'] = '/(' . $jsontest1[query] .')/i';
	$result['profile']['hits'] = $jsontest1[found];
	$result['profile']['cm0'] = str_replace("a href=" , "a target=\"_blank\" href=", $jsontest1[cm0]);
	if (strstr($result['profile']['cm0'] , "rounin")) { $result['profile']['cm0'] = str_replace("src=\"" , "src=\"http://dig.2ch.net", $result['profile']['cm0']);}
	$result['profile']['cm0'] = str_replace("<br></a>" , "</a>", $result['profile']['cm0']);

	$result['profile']['cm1'] = str_replace("a href=" , "a target=\"_blank\" href=", $jsontest1[cm1]);
	if (strstr($result['profile']['cm1'] , "rounin")) { $result['profile']['cm1'] = str_replace("src=\"" , "src=\"http://dig.2ch.net", $result['profile']['cm1']);}
	$result['profile']['cm1'] = str_replace("<br></a>" , "</a>", $result['profile']['cm1']);

	$result['profile']['cm2'] = str_replace("a href=" , "a target=\"_blank\" href=", $jsontest1[cm2]);
	if (strstr($result['profile']['cm2'] , "rounin")) { $result['profile']['cm2'] = str_replace("src=\"" , "src=\"http://dig.2ch.net", $result['profile']['cm2']);}
	$result['profile']['cm2'] = str_replace("<br></a>" , "</a>", $result['profile']['cm2']);

	return $result;
}

