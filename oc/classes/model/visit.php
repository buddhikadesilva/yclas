<?php defined('SYSPATH') or die('No direct script access.');
/**
 * description...
 *
 * @author		Chema <chema@open-classifieds.com>
 * @package		OC
 * @copyright	(c) 2009-2013 Open Classifieds Team
 * @license		GPL v3
 * *
 */
class Model_Visit extends ORM {
	
    /**
     * Table name to use
     *
     * @access	protected
     * @var		string	$_table_name default [singular model name]
     */
    protected $_table_name = 'visits';

    /**
     * Column to use as primary key
     *
     * @access	protected
     * @var		string	$_primary_key default [id]
     */
    protected $_primary_key = 'id_visit';

    /**
     * Rule definitions for validation
     *
     * @return array
     */
    public function rules()
    {
    	return array(
			        'id_visit'	=> array(array('numeric')),
			        'id_ad'	=> array(array('numeric')),
			    );
    }

    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
    	return array(
			        'id_visit'		=> 'Id visit',
			        'id_ad'		    => 'Id ad',
			        'created'		=> 'Created',
			    );
    }

    /**
     * get popular ads
     * @param  integer $days number of days to calculate
     * @return array        id_ad and count
     */
    public static function popular_ads($days = 30)
    {
        $query = DB::select('id_ad',DB::expr('SUM(hits) count'))
                        ->from('visits')
                        ->where('created','between',array(date('Y-m-d',strtotime('-'.$days.' day')),date::unix2mysql()))
                        ->group_by(DB::expr('id_ad'))
                        ->order_by('count','asc')
                        ->cached()
                        ->execute();

        return $query->as_array('id_ad');
    }

    /**
     * get all the visits
     * @return integer
     */
    public static function count_all_visits($id_ad = NULL)
    {
        $query = DB::select(DB::expr('SUM(hits) total'))->from('visits');

        if ($id_ad!=NULL)
            $query = $query->where('id_ad','=',$id_ad);

        $query = $query->cached()->execute();

        $result = $query->as_array();
        return (isset($result[0]['total'])) ? $result[0]['total'] : 0;
    }


    /**
     * ads a hit to table visits to an ad
     * @param  integer $id_ad id of the ad
     * @return integger        
     */
    public static function hit_ad($id_ad)
    {
        if (core::config('advertisement.count_visits')==TRUE AND !self::is_bot())
        {
            //see if exists the visit
            $hit = new Model_Visit();
            $hit = $hit ->where('id_ad','=',$id_ad)
                        ->where('created','=',date('Y-m-d'))
                        ->limit(1)->cached()->find();

            //didnt...so create it!
            if (!$hit->loaded())
            {
                $hit->invalidate_cache();
                $hit = new Model_Visit();
                $hit->id_ad   = $id_ad;
                $hit->hits    = 1;
                $hit->created = date('Y-m-d');
            }
            //existed add 1
            else    
                $hit->hits++;
            
            try {
                $hit->save();
            } catch (Exception $e) {}
            
            return $hit->hits;
        }
        return 0;
    }

    /**
     * ads a contact to table visits to an ad
     * @param  integer $id_ad id of the ad
     * @return integger        
     */
    public static function contact_ad($id_ad)
    {
        if (core::config('advertisement.count_visits')==TRUE AND !self::is_bot())
        {
            //see if exists the visit
            $hit = new Model_Visit();
            $hit = $hit ->where('id_ad','=',$id_ad)
                        ->where('created','=',date('Y-m-d'))
                        ->limit(1)->cached()->find();

            //didnt...so create it!
            if (!$hit->loaded())
            {
                $hit->invalidate_cache();
                $hit = new Model_Visit();
                $hit->id_ad   = $id_ad;
                $hit->contacts= 1;
                $hit->created = date('Y-m-d');
            }
            //existed add 1
            else    
                $hit->contacts++;
            
            try {
                $hit->save();
            } catch (Exception $e) {}

            return $hit->contacts;
        }

        return 0;
    }


    /**
     * visitor is a bot?
     * @return boolean
     */
    public static function is_bot() 
    {
        $spiders = array(
            "abot","dbot","ebot","hbot","kbot","lbot","mbot","nbot","obot","pbot","rbot","sbot","tbot","vbot","ybot","zbot","bot.","bot/","_bot",".bot","/bot","-bot",":bot","(bot","crawl","slurp","spider","seek","accoona","acoon","adressendeutschland","ah-ha.com","ahoy","altavista","ananzi","anthill","appie","arachnophilia","arale","araneo","aranha","architext","aretha","arks","asterias","atlocal","atn","atomz","augurfind","backrub","bannana_bot","baypup","bdfetch","big brother","biglotron","bjaaland","blackwidow","blaiz","blog","blo.","bloodhound","boitho","booch","bradley","butterfly","calif","cassandra","ccubee","cfetch","charlotte","churl","cienciaficcion","cmc","collective","comagent","combine","computingsite","csci","curl","cusco","daumoa","deepindex","delorie","depspid","deweb","die blinde kuh","digger","ditto","dmoz","docomo","download express","dtaagent","dwcp","ebiness","ebingbong","e-collector","ejupiter","emacs-w3 search engine","esther","evliya celebi","ezresult","falcon","felix ide","ferret","fetchrover","fido","findlinks","fireball","fish search","fouineur","funnelweb","gazz","gcreep","genieknows","getterroboplus","geturl","glx","goforit","golem","grabber","grapnel","gralon","griffon","gromit","grub","gulliver","hamahakki","harvest","havindex","helix","heritrix","hku www octopus","homerweb","htdig","html index","html_analyzer","htmlgobble","hubater","hyper-decontextualizer","ia_archiver","ibm_planetwide","ichiro","iconsurf","iltrovatore","image.kapsi.net","imagelock","incywincy","indexer","infobee","informant","ingrid","inktomisearch.com","inspector web","intelliagent","internet shinchakubin","ip3000","iron33","israeli-search","ivia","jack","jakarta","javabee","jetbot","jumpstation","katipo","kdd-explorer","kilroy","knowledge","kototoi","kretrieve","labelgrabber","lachesis","larbin","legs","libwww","linkalarm","link validator","linkscan","lockon","lwp","lycos","magpie","mantraagent","mapoftheinternet","marvin/","mattie","mediafox","mediapartners","mercator","merzscope","microsoft url control","minirank","miva","mj12","mnogosearch","moget","monster","moose","motor","multitext","muncher","muscatferret","mwd.search","myweb","najdi","nameprotect","nationaldirectory","nazilla","ncsa beta","nec-meshexplorer","nederland.zoek","netcarta webmap engine","netmechanic","netresearchserver","netscoop","newscan-online","nhse","nokia6682/","nomad","noyona","nutch","nzexplorer","objectssearch","occam","omni","open text","openfind","openintelligencedata","orb search","osis-project","pack rat","pageboy","pagebull","page_verifier","panscient","parasite","partnersite","patric","pear.","pegasus","peregrinator","pgp key agent","phantom","phpdig","picosearch","piltdownman","pimptrain","pinpoint","pioneer","piranha","plumtreewebaccessor","pogodak","poirot","pompos","poppelsdorf","poppi","popular iconoclast","psycheclone","publisher","python","rambler","raven search","roach","road runner","roadhouse","robbie","robofox","robozilla","rules","salty","sbider","scooter","scoutjet","scrubby","search.","searchprocess","semanticdiscovery","senrigan","sg-scout","shai'hulud","shark","shopwiki","sidewinder","sift","silk","simmany","site searcher","site valet","sitetech-rover","skymob.com","sleek","smartwit","sna-","snappy","snooper","sohu","speedfind","sphere","sphider","spinner","spyder","steeler/","suke","suntek","supersnooper","surfnomore","sven","sygol","szukacz","tach black widow","tarantula","templeton","/teoma","t-h-u-n-d-e-r-s-t-o-n-e","theophrastus","titan","titin","tkwww","toutatis","t-rex","tutorgig","twiceler","twisted","ucsd","udmsearch","url check","updated","vagabondo","valkyrie","verticrawl","victoria","vision-search","volcano","voyager/","voyager-hc","w3c_validator","w3m2","w3mir","walker","wallpaper","wanderer","wauuu","wavefire","web core","web hopper","web wombat","webbandit","webcatcher","webcopy","webfoot","weblayers","weblinker","weblog monitor","webmirror","webmonkey","webquest","webreaper","websitepulse","websnarf","webstolperer","webvac","webwalk","webwatch","webwombat","webzinger","wget","whizbang","whowhere","wild ferret","worldlight","wwwc","wwwster","xenu","xget","xift","xirq","yandex","yanga","yeti","yodao","zao/","zippp","zyborg",
            
            "Teoma", "alexa", "froogle", "Gigabot", "inktomi","looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory","Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
            "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp","msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
            "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot","Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot","Butterfly","Twitturls","Me.dium","Twiceler"
        );
        
        //no user agent is bot almost sure...
        if (!isset($_SERVER['HTTP_USER_AGENT']))
            return TRUE;
        
        //If the spider text is found in the current user agent, then return true
        foreach($spiders as $spider) 
            if ( stripos($_SERVER['HTTP_USER_AGENT'], $spider) !== FALSE ) return TRUE;
        
        //If it gets this far then no bot was found!
        return FALSE;
    }

    protected $_table_columns =  
array (
  'id_visit' =>
  array (
    'type' => 'int', 
    'min' => '0', 
    'max' => '4294967295', 
    'column_name' => 'id_visit', 
    'column_default' => NULL, 
    'data_type' => 'int unsigned', 
    'is_nullable' => false, 
    'ordinal_position' => 1, 
    'display' => '10', 
    'comment' => '', 
    'extra' => 'auto_increment', 
    'key' => 'PRI', 
    'privileges' => 'select,insert,update,references', 
  ), 
  'id_ad' => 
  array (
    'type' => 'int',
    'min' => '0',
    'max' => '4294967295',
    'column_name' => 'id_ad', 
    'column_default' => NULL, 
    'data_type' => 'int unsigned', 
    'is_nullable' => true, 
    'ordinal_position' => 2, 
    'display' => '10',
    'comment' => '', 
    'extra' => '', 
    'key' => 'MUL', 
    'privileges' => 'select,insert,update,references', 
  ), 
  'hits' => 
  array (
    'type' => 'int', 
    'min' => '-2147483648', 
    'max' => '2147483647', 
    'column_name' => 'hits', 
    'column_default' => '0', 
    'data_type' => 'int', 
    'is_nullable' => false, 
    'ordinal_position' => 3, 
    'display' => '10', 
    'comment' => '', 
    'extra' => '', 
    'key' => '', 
    'privileges' => 'select,insert,update,references', 
  ),
  'contacts' => 
  array (
    'type' => 'int', 
    'min' => '-2147483648', 
    'max' => '2147483647', 
    'column_name' => 'contacts', 
    'column_default' => '0', 
    'data_type' => 'int', 
    'is_nullable' => false, 
    'ordinal_position' => 4, 
    'display' => '10', 
    'comment' => '', 
    'extra' => '', 
    'key' => '', 
    'privileges' => 'select,insert,update,references', 
  ), 
  'created' =>
  array (
    'type' => 'string', 
    'column_name' => 'created', 
    'column_default' => NULL, 
    'data_type' => 'date', 
    'is_nullable' => false, 
    'ordinal_position' => 5, 
    'comment' => '', 
    'extra' => '', 
    'key' => '', 
    'privileges' => 'select,insert,update,references', 
  ),
);

} // END Model_Visit
