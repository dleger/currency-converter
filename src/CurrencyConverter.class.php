<?php
/*
 * Currency Converter V1.0
 *
 * By Denis Leger
 *
 * Free to use under the MIT license
 * http://www.opensource.org/licenses/mit-license.php
 */
 
class CurrencyConverter
{
    const URL_CURRENCIES = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    const URL_BASE_CACHE = '/var/www/html';
    
    private $currency_from;
    private $currency_to;
    
    private $cache_presence;
    private $cache;
    
    private $content_currencies_from_url;
    private $table_currencies;
    
    function __construct($cache_switch, $cache_location, $cache_time) {
        // ********************************************************
        // * Initialisation getting the currenvies rates from EUR *
        // ********************************************************
        $this->cache_presence = $cache_switch;
        
        $this->cache = new Gilbitron\Util\SimpleCache();
        $this->cache->cache_path = self::URL_BASE_CACHE . '/' . $cache_location;
        $this->cache->cache_time = $cache_time;
        
        if ($this->cache_presence) {
            
            $data = $this->cache->get_cache('XMLCurrenciesTree');
            
            if ($data) {
                $this->table_currencies = new SimpleXMLElement($data);
            } else {
                $this->content_currencies_from_url = file_get_contents(self::URL_CURRENCIES);
                $this->table_currencies = new SimpleXMLElement($this->content_currencies_from_url);
                
                $this->cache->set_cache('XMLCurrenciesTree', $this->content_currencies_from_url);
            }
        } else {
            $this->content_currencies_from_url = file_get_contents(self::URL_CURRENCIES);
            $this->table_currencies = new SimpleXMLElement($this->content_currencies_from_url);
        }
    }
    
    private function getXMLTableCurrencies () {
        return $this->table_currencies;
    }
    
    private function getRateCurrencyFrom ($currency) {
        
        foreach ($this->getXMLTableCurrencies()->Cube->Cube->Cube as $value) {
            if ($currency == $value['currency'] && (float)$value['rate'] > 0) {
                return ((float)$value['rate']);
            } else if ($currency == $value['currency'] && !((float)$value['rate'] > 0)) {
                throw new Exception('Currency rate egual to Zero', 0);
            }
        }
        
        throw new Exception('Currency name not valid or not handled by this class', 0);
    }

    public function convert($currency_from, $currency_to, $amount) {
        $this->currency_from = $currency_from;
        $this->currency_to = $currency_to;
        
        if ($currency_from == 'EUR' && $currency_to == 'EUR') {
            return $amount;
        } else if ($currency_to == 'EUR') {
            try {
                return ($amount / $this->getRateCurrencyFrom($currency_from));
            } catch (Exception $ex) {
                echo $ex->getMessage() . "\n";
            }
        } else if ($currency_from == 'EUR') {
            try {
                return ($this->getRateCurrencyFrom($currency_to) * $amount);
            } catch (Exception $ex) {
                echo $ex->getMessage() . "\n";
            }
        } else {
            try {
                $currency_from_rate = $this->getRateCurrencyFrom($currency_from);
                $currency_to_rate = $this->getRateCurrencyFrom($currency_to);
                
                return ((1 / $currency_from_rate) * $currency_to_rate) * $amount;
            } catch (Exception $ex) {
                echo $ex->getMessage() . "\n";
            }
        }    
    }
}
