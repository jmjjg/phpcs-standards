<?php
/**
 * Source code for the Cake2CodesnifferParanoid_Sniffs_Classes_AppUsesSniff class.
 *
 * @author Christian Buffin
 * @license https://opensource.org/licenses/MIT MIT License
 */

/**
 * The Cake2CodesnifferParanoid_Sniffs_Classes_AppUsesSniff class...
 */
class Cake2CodesnifferParanoid_Sniffs_Classes_AppUsesSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array( 'PHP' );

    /**
     * A list of currently available class names.
     *
     * @var array
     */
    protected $available = array();

    /**
     * The path of the current file being processed.
     *
     * @var string
     */
    protected $filename = null;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array( T_CLASS, T_ABSTRACT, T_EXTENDS, T_STRING );
    }

    /**
     * Returns the token stack position for the start of a "logical line" with
     * reference to the given stack position, or 0 if none found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The reference position.
     * @param array $tokens The tokens to check.
     * @return int
     */
    protected function findLogicalLineStart(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $tokens)
    {
        $startPtr = $phpcsFile->findPrevious(array(T_OPEN_TAG, T_SEMICOLON, T_CLOSE_CURLY_BRACKET), $stackPtr);
        return false === $startPtr ? 0 : $startPtr + 1;
    }

    /**
     * Returns the token stack position for the end of a "logical line" with
     * reference to the given stack position, or 0 if none found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The reference position.
     * @param array $tokens The tokens to check.
     * @return int
     */
    protected function findLogicalLineEnd(PHP_CodeSniffer_File $phpcsFile, $stackPtr, array $tokens)
    {
        $endPtr = $phpcsFile->findNext(array(T_CLOSE_TAG, T_SEMICOLON, T_OPEN_CURLY_BRACKET), $stackPtr);
        return false === $endPtr ? 0 : max(array($endPtr - 1, 0));
    }

    /**
     * Returns a "normalized" string (without comments or empty characters, with
     * a single space between each part) with regards to the "logical line"
     * surrounding the reference position.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The reference position.
     * @return string
     */
    protected function getNormalizedLogicalLine(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $startPtr = $this->findLogicalLineStart($phpcsFile, $stackPtr, $tokens);
        $endPtr = $this->findLogicalLineEnd($phpcsFile, $stackPtr, $tokens);

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

    /**
     * Process T_EXTENDS. Find the name of the extended class and check for
     * availability. If the class is not currently available, add an error.
     *
     * @todo PHP_CodeSniffer_Tokens::$methodPrefixes
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The stack position of the current T_EXTENDS token
     */
    protected function processExtends(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $line = $this->getNormalizedLogicalLine($phpcsFile, $stackPtr);
        $regexp = '/(abstract ){0,1}class (?P<class>[^ ]+) extends (?P<extended>[^ ]+)/i';

        if (1 === preg_match($regexp, $line, $matches)) {
            if (false === in_array($matches['extended'], $this->available)) {
                $message = sprintf('Missing App::uses for extended class \'%s\'', $matches['extended']);
                $type = 'ParentClass';

                $phpcsFile->addError($message, $stackPtr, $type);
            }
        }
    }

    /**
     * Process T_CLASS and T_ABSTRACT. Get the name of the class and add it to
     * the available classes.
     *
     * @todo PHP_CodeSniffer_Tokens::$methodPrefixes
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The stack position of the current T_CLASS or T_ABSTRACT token
     */
    protected function processClass(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $line = $this->getNormalizedLogicalLine($phpcsFile, $stackPtr);
        $regexp = '/(abstract ){0,1}class (?P<class>[^ ]+)/i';

        if (1 === preg_match($regexp, $line, $matches)) {
            $this->available[] = $matches['class'];
        }
    }

    /**
     * Process T_STRING. Try to get the name of a class that is loaded with
     * App::uses and add it to the available classes if found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The stack position of the current T_STRING token.
     */
    protected function processString(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        if ('app' === strtolower($token['content']) && array() === $token['conditions']) {
            $line = $this->getNormalizedLogicalLine($phpcsFile, $stackPtr);
            $regexp = '/App :: uses \( ["\'](?P<extended>[^"\']+)["\'] ,/i';
            if (1 === preg_match($regexp, $line, $matches)) {
                if (false === in_array($matches['extended'], $this->available)) {
                    $this->available[] = $matches['extended'];
                }
            }
        }
    }

    /**
     * Returns the class name that are always available: Exception and the SPL
     * classes.
     *
     * @todo Check for other always available class names
     *
     * @return array
     */
    protected function alwaysAvailable()
    {
        $result = spl_classes();
        $result[] = 'Exception';
        return $result;
    }

    /**
     * Initialise work with the current file, reset available classes on file
     * change.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     */
    protected function init(PHP_CodeSniffer_File $phpcsFile)
    {
        $filename = $phpcsFile->getFilename();
        if ($filename !== $this->filename) {
            $this->available  = $this->alwaysAvailable();
            $this->filename = $filename;
        }
    }

    /**
     * Initialises the file and process the registered tokens.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The position of the current token in the stack passed
     *  in $tokens.
     */
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
