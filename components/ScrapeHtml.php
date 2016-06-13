<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: nterry
 * Date: 6/6/16
 * Time: 3:39 PM
 */

namespace app\components;

use yii\base\NotSupportedException;
use yii\web\HttpException;


class ScrapeHtml {

	public function getPage(string $url, $params = [], string $method='GET') {
		switch($method) {
			case 'GET':
				$result = $this->httpGet($url, $params);
				break;
			case 'POST':
				$result = $this->httpPost($url, $params);
				break;
			default:
				if(in_array($method, ['PUT', 'DELETE'])){
					throw new NotSupportedException("Method $method not supported");
			} else {
				throw new NotSupportedException("Method $method not supported");
			}
		}
		return $result['result'];
	}

	public function getForms( string $page ) {
		preg_match_all( "/(<form.*?>)\s*(.*?)\s*(<\/[\s]*form>)/s", $page, $forms, PREG_SET_ORDER );
		return $forms;
}

	public function getInputs(string $form){
		preg_match_all( "/<input\s*((?:\s*[^\s\/>]+)*?)\s*\/*>/si", $form, $inputs_html, PREG_PATTERN_ORDER );
		return $inputs_html;
	}

	public function getAttributes($tag){     // single for now
		preg_match_all('/[^"]+"[^"]*"\s*/', $tag, $atts);
		$attributes = [];
		foreach($atts[0] as $att){
			preg_match('/\s*([^=]+)="([^"]*)"\s*/', $att, $parts);
			$attributes[$parts[1]] = $parts[2];
		}
		return $attributes;
	}

	public function getTables($page){
		preg_match_all( "/<table.*?>.*?<\/[\s]*table>/s", $page, $tables );
		return $tables;
	}
	
	public function parseTable( $table ) {
		preg_match_all( "/<tr.*?>(.*?)<\/[\s]*tr>/s", $table, $rows );
		$buildTable = [];
		foreach ( $rows[1] as $row ) {
			preg_match_all( "/<td.*?>(.*?)<\/[\s]*td>/", str_replace( "\r\n", "", $row ), $tds );
			$buildRow = [];
			foreach($tds[1] as $key => $td) {
				$buildRow[ $key ] = trim( strip_tags( html_entity_decode( $td ) ) );
			}
			if ( count( $row ) > 0 ) {
				$buildTable[] = $buildRow;
			}
		}

		return $buildTable;
	}

	private function httpGet( string $url, $params ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 );
		return $this->do_curl($ch);
	}

	private function httpPost( $url, $params ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_COOKIEJAR, '/tmp/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_COOKIEFILE, '/tmp/CookieJar.txt' );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HEADER, false );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_0 );
		return $this->do_curl($ch);
	}

	private function do_curl($ch){
		if(false === ($result = curl_exec( $ch ))){
			curl_close( $ch );
			$error = curl_error($ch);
			$errorDetail =curl_getinfo($ch);
			throw new HttpException($errorDetail['http_code'], $error);
		}

		curl_close( $ch );
		return ['result' => $result, 'error' => false];
	}


}