<?php
/**
 * Source code for the Cake2_Sniffs_Classes_AppUsesSniff class.
 *
 * @author Christian Buffin
 * @license https://opensource.org/licenses/MIT MIT License
 */
require_once dirname(dirname(__FILE__)).'/bootstrap.php';

/**
 * The Cake2_Sniffs_Classes_AppUsesSniff class provides sniffs for
 * App::uses calls in a CakePHP 2.x.x application.
 *
 * Provides
 *   - Cake2.Classes.AppUses.MissingParentClass
 *   - Cake2.Classes.AppUses.AlreadyImported
 *   - Cake2.Classes.AppUses.AlreadyImportedDifferent
 *   - Cake2.Classes.AppUses.WrongType
 *
 * Cake2.Classes.AppUses.MissingParentClass checks that every class in a
 * CakePHP 2.x file has access to the parent class, either directly or through
 * an App::uses call.
 *
 * Cake2.Classes.AppUses.AlreadyImported Checks if the class has already been
 * imported in the file (type-independant).
 *
 * Cake2.Classes.AppUses.AlreadyImportedDifferent Checks if the class has already
 * been imported in the file, type and plugin dependant.
 *
 * Cake2.Classes.AppUses.WrongType checks that every type used in an
 * App::uses call is correct by checking the $types attribute.
 */
class Cake2_Sniffs_Classes_AppUsesSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of currently available class names.
     * A first key is set for the file, a second key for the class name.
     *
     * Manipulate through the isAvailable and addAvailable methods.
     *
     * @var array
     */
    protected $available = array();

    /**
     * Holds a per-file connection between a class name and it's imports through
     * App::uses (line, type, plugin).
     *
     * @var array
     */
    protected $classMap = array();

    /**
     * The regexp strings that matches various normalized lines:
     *  - APP_USES: App::uses('modelname', 'type')
     *  - CLASS: class Classname
     *  - EXTENDS: class Classname extends ParentClass
     *
     * @var array
     */
    protected $regexps = array(
        'APP_USES' => '/App :: uses \( ["\'](?P<extended>[^"\']+)["\'] , ["\'](?P<type>[^"\']+)["\'] \)/i',
        'CLASS' => '/((abstract|final) ){0,1}class (?P<class>[^ ]+)/i',
        'EXTENDS' => '/((abstract|final) ){0,1}class (?P<class>[^ ]+) extends (?P<extended>[^ ]+)/i'
    );

    /**
     * A list of accepted types for App::uses.
     *
     * @see CakePHP 2.x.x's list of directories.
     *
     * @var array
     */
    protected $types = array(
        'Cache',
        'Cache/Engine',
        'Configure',
        'Console',
        'Console/Command',
        'Console/Command/Task',
        'Console/Helper',
        'Controller',
        'Controller/Component',
        'Controller/Component/Acl',
        'Controller/Component/Auth',
        'Core',
        'Error',
        'Event',
        'I18n',
        'Log',
        'Log/Engine',
        'Model',
        'Model/Behavior',
        'Model/Datasource',
        'Model/Datasource/Database',
        'Model/Datasource/Session',
        'Model/Validator',
        'Network',
        'Network/Email',
        'Network/Http',
        'Routing',
        'Routing/Filter',
        'Routing/Route',
        'TestSuite',
        'TestSuite/Coverage',
        'TestSuite/Fixture',
        'TestSuite/Reporter',
        'TestSuite/Stub',
        'Utility',
        'View',
        'View/Helper',
        'View/Scaffolds',
    );

    /**
     * Checks the class name current availibility in a file.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param string $classname The class name
     * @return boolean
     */
    protected function isAvailable(PHP_CodeSniffer_File $phpcsFile, $classname)
    {
        $filename = $phpcsFile->getFilename();
        return true === isset($this->available[$filename][$classname]);
    }

    /**
     * Adds class name availibility in a file.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param string $classname The class name
     * @return boolean
     */
    protected function addAvailable(PHP_CodeSniffer_File $phpcsFile, $classname)
    {
        $filename = $phpcsFile->getFilename();
        $this->available[$filename][$classname] = $classname;
    }

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_CLASS, T_ABSTRACT, T_EXTENDS, T_STRING);
    }

    /**
     * Returns the token stack position for the start of a "logical line" with
     * reference to the given stack position, or 0 if none found.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The reference position.
     * @return int
     */
    protected function findLogicalLineStart(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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
     * @return int
     */
    protected function findLogicalLineEnd(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
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
        $startPtr = $this->findLogicalLineStart($phpcsFile, $stackPtr);
        $endPtr = $this->findLogicalLineEnd($phpcsFile, $stackPtr);

        $tokens = $phpcsFile->getTokens();
        $result = array();

        $blacklist = array_merge(
            PHP_CodeSniffer_Tokens::$commentTokens,
            PHP_CodeSniffer_Tokens::$emptyTokens
        );

        for ($ptr = $startPtr; $ptr <= $endPtr; $ptr++) {
            if (false === in_array($tokens[$ptr]['code'], $blacklist)) {
                $result[] = $tokens[$ptr]['content'];
            }
        }

        return implode(' ', $result);
    }

    /**
     * Process T_EXTENDS. Find the name of the extended class and check for
     * availability.
     *
     * Adds the Cake2.Classes.AppUses.MissingParentClass error if the
     * class is not currently available
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The stack position of the current T_EXTENDS token
     */
    protected function processExtends(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $line = $this->getNormalizedLogicalLine($phpcsFile, $stackPtr);

        if (1 === preg_match($this->regexps['EXTENDS'], $line, $matches)) {
            if (true !== $this->isAvailable($phpcsFile, $matches['extended'])) {
                $message = sprintf('Missing App::uses for extended class \'%s\'', $matches['extended']);
                $messageType = 'MissingParentClass';

                $phpcsFile->addError($message, $stackPtr, $messageType);
            }
        }
    }

    /**
     * Process T_CLASS and T_ABSTRACT. Get the name of the class and add it to
     * the available classes.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The stack position of the current T_CLASS or T_ABSTRACT token
     */
    protected function processClass(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $line = $this->getNormalizedLogicalLine($phpcsFile, $stackPtr);

        if (1 === preg_match($this->regexps['CLASS'], $line, $matches)) {
            $this->addAvailable($phpcsFile, $matches['class']);
        }
    }

    /**
     * Process a line where an App::uses call has been made.
     *
     * Adds the Cake2.Classes.AppUses.WrongType error if type is not
     * recognized, the Cake2.Classes.AppUses.AlreadyImported warning if the
     * class has already been imported in the file or the
     * Cake2.Classes.AppUses.AlreadyImportedDifferent error if the class has
     * already been imported with a different type or plugin.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The current file being checked.
     * @param int $stackPtr The stack position of the current T_STRING token
     *  containing 'App'.
     * @param string $line
     */
    protected function processAppUses(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $line)
    {
        $tokens = $phpcsFile->getTokens();
        $token = $tokens[$stackPtr];

        if (1 === preg_match($this->regexps['APP_USES'], $line, $matches)) {
            $this->addAvailable($phpcsFile, $matches['extended']);

            // Check available types (plugin split)
            list($plugin, $type) = pluginSplit($matches['type']);

            if (false === in_array($type, $this->types)) {
                $message = sprintf('Wrong type for the App::uses call: \'%s\'', $type);
                $messageType = 'WrongType';

                $phpcsFile->addError($message, $stackPtr, $messageType);
            }

            // Add to classMap, check if already defined
            $filename = $phpcsFile->getFilename();
            if (true === isset($this->classMap[$filename][$matches['extended']]))
            {
                $first = $this->classMap[$filename][$matches['extended']][0];

                if ($first['plugin'] !== $plugin || $first['type'] !== $type) {
                    $message = sprintf(
                        'Class \'%s\' was first imported on line %d with a different type or plugin (\'%s\')',
                        $matches['extended'],
                        $first['line'],
                        null !== $plugin ? $plugin.'.'.$type : $type
                    );
                    $messageType = 'AlreadyImportedDifferent';
                    $phpcsFile->addError($message, $stackPtr, $messageType);
                } else {
                    $message = sprintf('Class \'%s\' was first imported on line %d', $matches['extended'], $first['line']);
                    $messageType = 'AlreadyImported';
                    $phpcsFile->addWarning($message, $stackPtr, $messageType);
                }
            }
            $this->classMap[$filename][$matches['extended']][] = array(
                'plugin' => $plugin,
                'type' => $type,
                'line' => $token['line']
            );
        }
    }

    /**
     * Process T_STRING. Try to get the name of a class that is loaded with
     * App::uses and processes it if found.
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

            if (1 === preg_match($this->regexps['APP_USES'], $line)) {
                $this->processAppUses($phpcsFile, $stackPtr, $line);
            }
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
        $filename = $phpcsFile->getFilename();
        if (false === isset($this->available[$filename])) {
            $this->available[$filename] = array_merge(
                spl_classes(),
                array('Exception' => 'Exception')
            );
        }

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
