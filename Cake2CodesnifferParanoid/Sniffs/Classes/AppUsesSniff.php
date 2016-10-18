<?php
class Cake2CodesnifferParanoid_Sniffs_Classes_AppUsesSniff implements PHP_CodeSniffer_Sniff
{
	public $supportedTokenizers = array( 'PHP' );

	protected $uses = array();

	protected $filename = null;

	public function register() {
		return array( T_EXTENDS, T_STRING );
	}

	protected function getNormalizedCommand(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$startPtr = $phpcsFile->findPrevious(array(T_OPEN_TAG, T_SEMICOLON), $stackPtr);
		$startPtr = false === $startPtr ? 0 : $startPtr + 1;
		$endPtr = $phpcsFile->findNext(array(T_CLOSE_TAG, T_SEMICOLON), $stackPtr);
		$endPtr = false === $endPtr ? 0 : max(array($endPtr - 1, 0));

		$result = array();
		for ($ptr = $startPtr ; $ptr <= $endPtr ; $ptr++) {
			$blacklist = array_merge( PHP_CodeSniffer_Tokens::$commentTokens, PHP_CodeSniffer_Tokens::$emptyTokens );
			if( false === in_array( $tokens[$ptr]['code'], $blacklist ) ) {
				$result[] = $tokens[$ptr]['content'];
			}
		}

		return implode( '', $result );
	}

	protected function checkExtendingClass(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();
		$extendedClassName = $tokens[$phpcsFile->findNext(T_STRING, $stackPtr)]['content'];

		if( false === in_array( $extendedClassName, $this->uses ) ) {
			$message = sprintf( 'Missing App::uses for extended class \'%s\'', $extendedClassName );
			$type = 'ParentClass';

			$phpcsFile->addError( $message, $stackPtr, $type );
		}
	}

	/**
	 * Reset available uses on file change.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being processed
	 */
	protected function init(PHP_CodeSniffer_File $phpcsFile) {
        $filename = $phpcsFile->getFilename();
        if ($filename !== $this->filename) {
            $this->uses  = array();
			$this->filename = $filename;
        }
	}

	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$this->init($phpcsFile);

		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$stackPtr];

		if (T_EXTENDS === $token['code']) {
			$this->checkExtendingClass($phpcsFile, $stackPtr);
		}
		else if (T_STRING === $token['code'] && 'app' === strtolower($token['content']) && array() === $token['conditions']) {
			$line = $this->getNormalizedCommand($phpcsFile, $stackPtr);
			if (1 === preg_match('/App::uses\(["\']([^"\']+)["\'],/i', $line, $matches)) {
				$this->uses[] = $matches[1];
			}
		}
	}
}
