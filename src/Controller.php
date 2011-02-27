<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\cli;
use aura\signal\Manager as SignalManager;

/**
 * 
 * The CLI equivalent of a page controller to perform a command.
 * 
 * @package aura.cli
 * 
 */
abstract class Controller
{
    /**
     * 
     * A Getopt object for the Controller; retains the short and long options
     * passed at the command line.
     * 
     * @var aura\cli\Getopt
     * 
     */
    protected $getopt;
    
    /**
     * 
     * The option definitions for the Getopt object.
     * 
     * @var array
     * 
     */
    protected $options = array();
    
    /**
     * 
     * Should Getopt be strict about how options are processed?  In strict
     * mode, passing an undefined option will throw an exception; in
     * non-strict, it will not.
     * 
     * @var bool
     * 
     */
    protected $options_strict = Getopt::STRICT;
    
    /**
     * 
     * The positional (numeric) arguments passed at the command line.
     * 
     * @var array
     * 
     */
    protected $params = array();
    
    /**
     * 
     * Constructor.
     * 
     * @param Context $context The command-line context.
     * 
     * @param Stdio $stdio Standard input/output streams.
     * 
     * @param Getopt $getopt An options processor and reader.
     * 
     * @param aura\signal\Manager $signal A signal manager to send signals to.
     * 
     */
    public function __construct(
        Context       $context,
        Stdio         $stdio,
        Getopt        $getopt,
        SignalManager $signal
    ) {
        // marshal into properties
        $this->context = $context;
        $this->stdio   = $stdio;
        $this->getopt  = $getopt;
        $this->signal  = $signal;
        
        // handle these signals
        $this->signal->handler($this, 'pre_action', array($this, 'preAction'));
        $this->signal->handler($this, 'post_action', array($this, 'postAction'));
        
        // load the getopt and params properties
        $this->loadGetoptParams();
    }
    
    /**
     * 
     * Passes the Context arguments to `$getopt` and retains the numeric
     * parameters in `$params`.
     * 
     * @return void
     * 
     */
    protected function loadGetoptParams()
    {
        $this->getopt->init($this->options, $this->options_strict);
        $this->getopt->load($this->context->getArgv());
        $this->params = $this->getopt->getParams();
    }
    
    /**
     * 
     * Executes the Controller.  In order, it does these things:
     * 
     * - signals `'pre_action'`
     * - calls `action()`
     * - signals `'post_action'`
     * - resets the terminal to normal colors
     * 
     * @signal 'pre_action'
     * 
     * @signal 'post_action'
     * 
     * @see action()
     * 
     * @return void
     * 
     */
    public function exec()
    {
        $this->signal->send($this, 'pre_action');
        $this->action();
        $this->signal->send($this, 'post_action');
        
        // return terminal output to normal colors
        $this->stdio->out("%n");
        $this->stdio->err("%n");
    }
    
    /**
     * 
     * Runs before `action()` as part of the `'pre_action'` signal.
     * 
     * @return mixed
     * 
     */
    public function preAction()
    {
    }
    
    /**
     * 
     * The main logic for the Controller.
     * 
     * @return void
     * 
     */
    abstract public function action();
    
    /**
     * 
     * Runs after `action()` as part of the `'post_action'` signal.
     * 
     * @return mixed
     * 
     */
    public function postAction()
    {
    }
}
