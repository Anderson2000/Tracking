<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \FastRedis;

class Tracker extends Model 
{	

	public static function getAllStatistic(){
		$data = false;
		$redis=FastRedis::connection();

		$pages = $redis->hGetAll('pages');
		$browsers = $redis->hGetAll('browsers');
		$systems = $redis->hGetAll('systems');
		$geos = $redis->hGetAll('geos');
		$referers = $redis->hGetAll('referers');

		foreach ($pages as $page=> $pageIndex){
		 	
		 	foreach ($browsers as $browser=> $browserIndex){
		 		if($hits=$redis->get(implode("_",['hits','browsers', $pageIndex, $browserIndex]))){
		 			$data[$page]['browsers'][$browser]['hits']=$hits;
		 			
		 			$data[$page]['browsers'][$browser]['ip']=$redis->sCard(
		 				implode("_",['ip','browsers', $pageIndex, $browserIndex]));
		 			
		 			$data[$page]['browsers'][$browser]['cookie']=$redis->sCard(
		 				implode("_",['cookie','browsers', $pageIndex, $browserIndex]));
		 			}
		 		}

		 	foreach ($systems as $system=> $systemIndex){
		 		if($hits=$redis->get(implode("_",['hits','systems', $pageIndex, $systemIndex]))){
		 			$data[$page]['systems'][$system]['hits']=$hits;
		 			
		 			$data[$page]['systems'][$system]['ip']=$redis->sCard(
		 				implode("_",['ip','systems', $pageIndex, $systemIndex]));
		 			
		 			$data[$page]['systems'][$system]['cookie']=$redis->sCard(
		 				implode("_",['cookie','systems', $pageIndex, $systemIndex]));
		 			}
		 		}

		 	foreach ($geos as $geo=> $geoIndex){
		 		if($hits=$redis->get(implode("_",['hits','geos', $pageIndex, $geoIndex]))){
		 			$data[$page]['geos'][$geo]['hits']=$hits;
		 			
		 			$data[$page]['geos'][$geo]['ip']=$redis->sCard(
		 				implode("_",['ip','geos', $pageIndex, $geoIndex]));
		 			
		 			$data[$page]['geos'][$geo]['cookie']=$redis->sCard(
		 				implode("_",['cookie','geos', $pageIndex, $geoIndex]));
		 			}
		 		}

		 	foreach ($referers as $referer=> $refererIndex){
		 		if($hits=$redis->get(implode("_",['hits','referers', $pageIndex, $refererIndex]))){
		 			$data[$page]['referers'][$referer]['hits']=$hits;
		 			
		 			$data[$page]['referers'][$referer]['ip']=$redis->sCard(
		 				implode("_",['ip','referers', $pageIndex, $refererIndex]));
		 			
		 			$data[$page]['referers'][$referer]['cookie']=$redis->sCard(
		 				implode("_",['cookie','referers', $pageIndex, $refererIndex]));
		 			}
		 		}


			}
		return $data;


	}
}
