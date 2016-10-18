<?php
class Cake2CodesnifferParanoid_Sniffs_Classes_AppUsesSniff implements PHP_CodeSniffer_Sniff
{
    public $supportedTokenizers = array( 'PHP' );

    protected $available = array();

    protected $filename = null;

    public function register()
    {
        return array( T_CLASS, T_ABSTRACT, T_EXTENDS, T_STRING );
    }

    protected function getNormalizedCommand(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $startPtr = $phpcsFile->findPrevious(array(T_OPEN_TAG, T_SEMICOLON, T_CLOSE_CURLY_BRACKET), $stackPtr);
        $startPtr = false === $startPtr ? 0 : $startPtr + 1;
        $endPtr = $phpcsFile->findNext(array(T_CLOSE_TAG, T_SEMICOLON, T_OPEN_CURLY_BRACKET), $stackPtr);
        $endPtr = false === $endPtr ? 0 : max(array($endPtr - 1, 0));

        $result = array();
        for ($ptr = $startPtr; $ptr <= $endPtr; $ptr++) {
            $blacklist = array_merge(
                PHP_CodeSniffer_Tokens::$commentTokens,
                PHP_CodeSniffer_Tokens::$emptyTokens
            );
            if (false === in_array($tokens[$ptr]['code'], $blacklist)) {
                $result[] = $tokens[$ptr]['content'];
            }
        }

        return implode(' ', $result);
    }

    protected function processExtends(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $line = $this->getNormalizedCommand($phpcsFile, $stackPtr);
        $regexp = '/(abstract ){0,1}class (?P<class>[^ ]+) extends (?P<extended>[^ ]+)/i';
        if (1 === preg_match($regexp, $line, $matches)) {
            if (false === in_array($matches['extended'], $this->available)) {
                $message = sprintf('Missing App::uses for extended class \'%s\'', $matches['extended']);
                $type = 'ParentClass';

                $phpcsFile->addError($message, $stackPtr, $type);
            }
        }
    }

    protected function processClass(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $line = $this->getNormalizedCommand($phpcsFile, $stackPtr);

        $regexp = '/(abstract ){0,1}class (?P<class>[^ ]+)/i';
        if (1 === preg_match($regexp, $line, $matches)) {
            $this->available[] = $matches['class'];
        }
    }

    protected function processString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        if ('app' === strtolower($token['content']) && array() === $token['conditions']) {
            $line = $this->getNormalizedCommand($phpcsFile, $stackPtr);
            $regexp = '/App :: uses \( ["\'](?P<extended>[^"\']+)["\'] ,/i';
            if (1 === preg_match($regexp, $line, $matches)) {
                if (false === in_array($matches['extended'], $this->available)) {
                    $this->available[] = $matches['extended'];
                }
            }
        }
    }

    protected function alwaysAvailable()
    {
        $result = spl_classes();
        $result[] = 'Exception';
        return $result;
    }

    /**
     * Reset available classes on file change.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being processed
     */
    protected function init(PHP_CodeSniffer_File $phpcsFile)
    {
        $filename = $phpcsFile->getFilename();
        if ($filename !== $this->filename) {
            $this->available  = $this->alwaysAvailable();
            $this->filename = $filename;
        }
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->init($phpcsFile);

        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        if (T_EXTENDS === $token['code']) {
            $this->processExtends($phpcsFile, $stackPtr);
        } elseif (true === in_array($token['code'], array(T_CLASS, T_ABSTRACT))) {
            $this->processClass($phpcsFile, $stackPtr);
        } elseif (T_STRING === $token['code']) {
            $this->processString($phpcsFile, $stackPtr);
        }
    }
}
