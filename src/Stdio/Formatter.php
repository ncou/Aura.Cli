<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Cli
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Cli\Stdio;

/**
 * 
 * Text formatting for VT100 terminals.
 * 
 * @package Aura.Cli
 * 
 * @author Paul M. Jones <pmjones@paul-m-jones.com>
 * 
 */
class Formatter
{
    /**
     * 
     * Array of <<markup>> mappings to control code numbers.
     * 
     * Based on the `ANSI/VT100 Terminal Control reference` at
     * <http://www.termsys.demon.co.uk/vtansi.htm>.
     * 
     * @var array
     * 
     */
    protected $codes = [
        'reset'       => '0',
        'bold'        => '1',
        'dim'         => '2',
        'ul'          => '4',
        'blink'       => '5',
        'reverse'     => '7',
        'black'       => '30',
        'red'         => '31',
        'green'       => '32',
        'yellow'      => '33',
        'blue'        => '34',
        'magenta'     => '35',
        'cyan'        => '36',
        'white'       => '37',
        'blackbg'     => '40',
        'redbg'       => '41',
        'greenbg'     => '42',
        'yellowbg'    => '43',
        'bluebg'      => '44',
        'magentabg'   => '45',
        'cyanbg'      => '46',
        'whitebg'     => '47',
    ];

    public function __construct()
    {
        $this->regex = '<<\s*((('
                     . implode('|', array_keys($this->codes))
                     . ')(\s*))+)>>';
    }
    
    
    /**
     * 
     * Converts <<markup>> in text to VT100 control codes.
     * 
     * @param string $text The text to format.
     * 
     * @return string The formatted text.
     * 
     */
    public function format($string, $posix)
    {
        if ($posix) {
            return preg_replace_callback(
                "/{$this->regex}/Umsi",
                [$this, 'formatCallback'],
                $string
            );
        } else {
            return preg_replace("/{$this->regex}/Umsi", '', $string);
        }
    }

    protected function formatCallback(array $matches)
    {
        $str = preg_replace('/(\s+)/msi', ';', $matches[1]);
        return chr(27) . '[' . strtr($str, $this->codes) . 'm';
    }
}
