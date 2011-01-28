<?php
require_once 'CalEvans/Klout/Exception.php';
/**
 * CalEvans_Klout
 *
 * Wrapper for the Klout.com API
 * http://developer.klout.com/page
 *
 * 
 * @package CalEvans_Klout
 * @copyright 2011 Cal Evans
 * @author Cal Evans <cal@calevans.com>
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 * @version 1.0
 * @todo Move Curl code to it's own object implementing a Transport interface. Create Transport implementations for file_get_contents and using a stream.
 * @todo create namespace and implement
 * 
 * Usage:
 * $o = new CalEvans_Klout($key,'CalEvans');
 * $o->setFormat('json');
 * $topics = $o->topics();
 * foreach ($topics->users[0]->topics as $topic) {
 *   echo $topic . "\n";
 * }
 */
class CalEvans_Klout {
    
    /**
     * @var $_apiVersion the version of the API this wrapper supports.
     */
    protected $_apiVersion   = 1;

    /**
     * @var $_key the klout.com API key
     */
    protected $_key          = '';

    /**
     * @var $_handle The twitter handle of this user.
     */
    protected $_handle       = '';


    /**
     * @var $_baseUrl the base url for the API. MUST END IN /
     */
    protected $_baseUrl      = 'http://api.klout.com/';

    /**
     * @var $_topics list of topics this user tweets on as definined by klout
     */
    protected $_topics       = null;

    /**
     * @var $_show The output of a call to /show for this user.
     */
    protected $_show         = null;

    /**
     * @var $_show The output of a call to /influencedBy for this user.
     */
    protected $_influencedBy = null;

    /**
     * @var $_show The output of a call to /influencerOf for this user.
     */
    protected $_influencerOf = null;
    
    
    /**
     * __construct
     *
     * Constructor
     *
     * @param string $key the klout.com API key
     * @param string $handle the twitter handle of the user to fetch.
     */
    public function __construct($key=null, $handle=null)
    {
        if (!is_null($key)) {
            $this->setKey($key);
        } // if (!is_null($key))

        if (!is_null($handle)) {
            $this->setHandle($handle);
        } // if (!is_null($handle))
        
    } // public function __construct($key=null)
    
    
    /**
     * setKey
     *
     * Sets the API key
     *
     * @param string $value the value to set in the API key
     */
    public function setKey($value=null)
    {
        if (!is_null($value)) {
            $this->_key = $value;
        } // if (!is_null($value))
        
    } // public function setKey($key=null)
    
    
    /**
     * setHandle
     *
     * Sets the twitter user handle
     *
     * @param string $value the value to set as the twitter handle
     */
    public function setHandle($value=null)
    {
        if (!is_null($value)) {
            $this->_handle = $value;
        } // if (!is_null($value))
        
    } // public function setHandle($value=null)
    
    
    /**
     * getHandle
     *
     * returns the value of the twitter handle
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->_handle;
    }
    
    
    /**
     * setFormat
     *
     * Sets the format for the API to return
     *
     * @param string $value the value to set as the return format, json and XML are supported.
     */
    public function setFormat($value=null)
    {
        if (!is_null($value)) {
            
            switch($value) {
                case 'json':
                case 'xml':
                    $this->_format = $value;
                    break;
                default:
            } // switch($value)
            
        } // if (!is_null($value))
        return;        
    } // public function setFormat($value=null)
    
    
    /**
     * influencedBy
     *
     * returns the list of twitter handles this user is influenced by, in the format specified. If the data does not exist, it makes a call to the API first.
     *
     * @return multi
     */
    public function influencedBy()
    {
        if (is_null($this->_influencedBy)) {
            $this->_influencedBy = $this->_fetch('influenced_by')->influencers;
        } // if (is_null($this->_influencedBy))
        return $this->_influencedBy;
    } // public function fetchInfluencedBy($handle=null)
    
    
    /**
     * influencerOf
     *
     * returns the list of twitter handles this user is an influencer of, in the format specified. If the data does not exist, it makes a call to the API first.
     *
     * @return multi
     */
    public function influencerOf()
    {
        if (is_null($this->_influencerOf)) {
            $this->_influencerOf = $this->_fetch('influencer_of')->influencees;
        } // if (is_null($this->_influencerOf)) 
        return $this->_influencerOf;
    } // public function fetchInfluencedBy($handle=null)
    
    
    /**
     * show
     *
     * returns all of the data klout store as part of the user, in the format specified. If the data does not exist, it makes a call to the API first.
     *
     * @return multi
     */
    public function show()
    {
        if (is_null($this->_show)) {
            $this->_show = $this->_fetch('show');
        } // if (is_null($this->_show))
        return $this->_show;
    } // public function show($handle=null)
    
    
    /**
     * topics
     *
     * returns the top 3 topics the user tweets about as determined by klout, in the format specified. If the data does not exist, it makes a call to the API first.
     *
     * @return multi
     */
    public function topics()
    {
        if (is_null($this->_topics)) {
            $this->_topics = $this->_fetch('topics')->topics;
        } // if (is_null($this->_topics))
        return $this->_topics;
    } // public function topics($handle=null)
    
    
    /**
     * _fetch
     *
     * prepares all calls to the API. 
     *
     * @param string $action The action to be performed.
     * @return string
     * @throws CalEvans_Klout_Exception
     */
    protected function _fetch($action=null)
    {
        if (is_null($action)) {
            throw new CalEvans_Klout_Exception('No action was specified.',-1);
        }
        
        $query = http_build_query(array('key'=>$this->_key,
                                        'users'=>$this->_handle),'','&');
        
        $url = $this->_baseUrl.$this->_apiVersion . '/' .
               $this->_getSubgroup($action) . $action. '.json?'.$query;
        $storage = $this->_execute($url);
        $storage = json_decode($storage);
        return $storage->users[0];
    } // protected function fetch($handle=null, $action=null)
    
    
    /**
     * _getSubgroup
     *
     * computes the needed subgroup.
     *
     * @param string $action The action to be performed.
     * @return string
     */
    protected function _getSubgroup($action=null)
    {
        switch($action) {
            case 'klout':
                $subGroup='';
                break;
            
            case 'influenced_by':
            case 'influencer_of':
                $subGroup='soi/';
                break;
            
            case 'show':
            case 'topics':
                $subGroup='users/';
                break;
            default:
                $subGroup = '';
        } // switch($action) 
        return $subGroup;        
    } // protected function _getSubroup($action=null)
    
    
    /**
     * _execute
     *
     * Makes the call to the API using curl and returns the raw results for processing.
     *
     * @param string $url The url to call.
     * @return string
     * @throws CalEvans_Klout_Exception
     * 
     */
    protected function _execute($url=null)
    {
        if (is_null($url)) {
            throw new CalEvans_Klout_Exception('No URL provided',-2);
        }

        $output = '';
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);         

        // Check if any error occured
        if(curl_errno($ch))
        {
            // curl error
            $message = curl_error($ch);
            $errno   = curl_errno($ch);

            curl_close($ch);
            
            throw new CalEvans_Klout_Exception($message,$errno);
        } else if(curl_getinfo($ch,CURLINFO_HTTP_CODE)!==200) {
            // http error
            $message = 'There was an error retrieving the api call.';
            $errno   = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            throw new CalEvans_Klout_Exception($message,$errno);
        } else {
            // everything went fine
            curl_close($ch);
        } // if(curl_errno($ch))
        
        
        return $output;    
    }
    
    
    
} // class Klout 
