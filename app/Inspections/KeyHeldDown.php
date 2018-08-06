<?

namespace App\Inspections;

use Exception;

class KeyHeldDown {
    /**
     * 
     * 
     * @return 
     */
    public function detect($body) {
        if( preg_match('/(.)\\1{4,}/', $body) ) {
            throw new \Exception('You reply contains spam');
        } 
    }
}