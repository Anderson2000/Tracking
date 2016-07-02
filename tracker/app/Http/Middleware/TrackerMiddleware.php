<?php

namespace App\Http\Middleware;

use Closure;
use \FastRedis;
use \Identify;
use \GeoIP;
use \Request;
class TrackerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $coocie = \Cookie::get('name');
        if(!$coocie){
            $swID = rand(1, 87);
            $swChar = json_decode(file_get_contents('https://swapi.co/api/people/'.$swID.'/'));
            $coocie = $swChar->name;
            
             \Cookie::queue(\Cookie::make('name', $coocie, 5));
        }
        $redis=FastRedis::connection();
        //total
        $totalIndex=$redis->hget('pages', 'total');
        if(!$totalIndex){
            $totalIndex=$redis->hlen('pages')+1;
            $redis->hset('pages', 'total', $totalIndex);
        }
        //url page
        $pageIndex=$redis->hget('pages', Request::url());
        if(!$pageIndex){
            $pageIndex=$redis->hlen('pages')+1;
            $redis->hset('pages', Request::url(), $pageIndex);
        }
        //name browser
        $browserIndex=$redis->hget('browsers', Identify::browser()->getName());
        if(!$browserIndex){
            $browserIndex=$redis->hlen('browsers')+1;
            $redis->hset('browsers', Identify::browser()->getName(), $browserIndex);
        }
        //name system
        $systemIndex=$redis->hget('systems', Identify::os()->getName());
        if(!$systemIndex){
            $systemIndex=$redis->hlen('systems')+1;
            $redis->hset('systems', Identify::os()->getName(), $systemIndex);
        }
        //geolocation
        $geoIndex=$redis->hget('geos', GeoIP::getLocation()['lat'].'_'.GeoIP::getLocation()['lon']);
        if(!$geoIndex){
            $geoIndex=$redis->hlen('geos')+1;
            $redis->hset('geos', GeoIP::getLocation()['lat'].'_'.GeoIP::getLocation()['lon'], $geoIndex);
        }
        //host of referer 
        $refererHost = (Request::server('HTTP_REFERER'))?parse_url(Request::server('HTTP_REFERER'))['host'] : 'noReferer';
        $refererIndex=$redis->hget('referers', $refererHost);
        if(!$refererIndex){
            $refererIndex=$redis->hlen('referers')+1;
            $redis->hset('referers', $refererHost, $refererIndex);
        }
        
        $keys = [$totalIndex, $pageIndex];
        foreach ($keys as $i => $key) {
            
            //hits of browser
            $redis->incrby(implode("_",['hits','browsers', $key, $browserIndex]),1);
            //unique ip of browser
            $redis->sadd(implode("_",['ip',  'browsers', $key, $browserIndex]), GeoIP::getLocation()['ip']);
            //unique cookie of browser
            $redis->sadd(implode("_",['cookie',  'browsers', $key, $browserIndex]), $coocie);

            //hits of os
            $redis->incrby(implode("_",['hits','systems', $key, $systemIndex]),1);
            //unique ip of os
            $redis->sadd(implode("_",['ip',  'systems', $key, $systemIndex]), GeoIP::getLocation()['ip']);
            //unique cookie of os
            $redis->sadd(implode("_",['cookie',  'systems', $key, $systemIndex]), $coocie);

            //hits of geo
            $redis->incrby(implode("_",['hits','geos', $key, $geoIndex]),1);
            //unique ip of os
            $redis->sadd(implode("_",['ip',  'geos', $key, $geoIndex]), GeoIP::getLocation()['ip']);
            //unique cookie of os
            $redis->sadd(implode("_",['cookie',  'geos', $key, $geoIndex]), $coocie);

            //hits of referer
            $redis->incrby(implode("_",['hits','referers', $key, $refererIndex]),1);
            //unique ip of os
            $redis->sadd(implode("_",['ip',  'referers', $key, $refererIndex]), GeoIP::getLocation()['ip']);
            //unique cookie of os
            $redis->sadd(implode("_",['cookie',  'referers', $key, $refererIndex]), $coocie);

        }        
        
        return $next($request);
    }
}
