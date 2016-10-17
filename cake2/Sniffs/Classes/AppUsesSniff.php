<?php
class Cake2_Sniffs_Classes_AppUsesSniff implements PHP_CodeSniffer_Sniff
{
	public $supportedTokenizers = array( 'PHP' );

	public function register() {
		return array( T_CLASS );
	}

	protected function isValidStackPtr( $stackPtr ) {
		return 0 <= $stackPtr && false !== $stackPtr;
	}

	protected function findPreviousAppUses( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$result = false;

		if( $i = $phpcsFile->findNext( T_STRING, $stackPtr - 1, null, false, 'App' ) ) {
			$j = $phpcsFile->findNext( T_DOUBLE_COLON, $i, null, false, null, true );
			if( $this->isValidStackPtr( $j ) ) {
				$k = $phpcsFile->findNext( T_STRING, $j, null, false, 'uses', true );
				if( $this->isValidStackPtr( $k ) ) {
					$l = $phpcsFile->findNext( T_CONSTANT_ENCAPSED_STRING, $k, null, false, null, true );
					if( $this->isValidStackPtr( $l ) ) {
						$result = trim( $tokens[$l]['content'], '"\'' );
					}
				}
			}
		}

		return $result;
	}

	protected function getClassName( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$result = false;

		$classNamePtr = $phpcsFile->findNext( T_STRING, $stackPtr );
		if( $this->isValidStackPtr( $classNamePtr ) ) {
			$result = $tokens[$classNamePtr]['content'];
		}

		return $result;
	}

	protected function getExtendedClassName( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$result = false;

		$otherStackPtr = $phpcsFile->findNext( T_EXTENDS, $stackPtr );
		if( $this->isValidStackPtr( $otherStackPtr ) ) {
			$resultPtr = $phpcsFile->findNext( T_STRING, $otherStackPtr );
			$result = $tokens[$resultPtr]['content'];
		}

		return $result;
	}

	public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		$className = $this->getClassName( $phpcsFile, $stackPtr );
		$extendedClassName = $this->getExtendedClassName( $phpcsFile, $stackPtr );

		if( false !== $className && false !== $extendedClassName ) {
			$found = array();
			$i = $stackPtr;
			while( $this->isValidStackPtr( $i ) ) {
				$i = $phpcsFile->findPrevious( T_STRING, $i, 0, false, 'App' );
				if( $this->isValidStackPtr( $i ) ) {
					$found[] = $this->findPreviousAppUses( $phpcsFile, $i );
				}
				$i-=1;
			}

			if( false === in_array( $extendedClassName, $found ) ) {
				$message = sprintf( 'Missing App::uses for extended class \'%s\'', $extendedClassName );
				$type = 'ParentClass';

				$phpcsFile->addError( $message, $stackPtr, $type );
			}
		}
	}
}
