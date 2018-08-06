<?

namespace App\Inspections;

use Exception;

class InvalidKeywords {

    protected $keywords = [
        'Yahoo Customer Support'
    ];

    /**
     * 
     * 
     * @return 
     */
    public function detect($body) {
        foreach($this->keywords as $keyword) {
            if( stripos($body, $keyword) !== false ) {
                throw new Exception('You reply contains spam');
            }
        }
    }
}