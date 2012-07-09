<?php

class Elm_Model_Setup
{
    const VERSION_COMPARE_EQUAL   = 0;
    const VERSION_COMPARE_LOWER   = -1;
    const VERSION_COMPARE_GREATER = 1;

    protected static $_config;
	
    protected static $_versions;

    /**
     * Setup Connection
     *
     * @var Zend_Db_Adapter
     */
    protected $_conn;

    /**
     * Flag wich allow to detect that some schema update was applied dueting request
     *
     * @var bool
     */
    protected static $_hadUpdates;

	/**
	 * @static
	 * @return array
	 */
	public static function getConfig()
	{
		if (is_null(self::$_config)) {
			self::$_config = Zend_Registry::get('config');
		}
		return self::$_config;
	}

    /**
     * Get connection object
     *
     * @return Zend_Db_Adapter
     */
    public function getConnection()
    {
		if (!$this->_conn) {
			$this->_conn = Zend_Registry::get('db');
		}
        return $this->_conn;
    }

	public function run($sql)
    {
        $this->multipleLineQuery($sql);
        return $this;
    }

    public function startSetup()
    {
        $this->multipleLineQuery("SET SQL_MODE='';
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
");
        return $this;
    }

    public function endSetup()
    {
        $this->multipleLineQuery("
SET SQL_MODE=IFNULL(@OLD_SQL_MODE,'');
SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS=0, 0, 1);
");
        return $this;
    }

    /**
     * Apply database updates whenever needed
     *
     * @return  boolean
     */
    static public function applyAllUpdates()
    {
        self::$_hadUpdates = false;

		$class = __CLASS__;
		$setup = new $class();
		$setup->applyUpdates();
        return true;
    }

    /**
     * Apply install, upgrade and data scripts
	 *
	 * @return Boolean|void
     */
    public function applyUpdates()
    {
		$config = self::getConfig();
        $dbVer = $this->getDbVersion('app');
        $configVer = (string) $config['app']['version'];

        if ($dbVer !== false) {
             switch (version_compare($configVer, $dbVer)) {
                //case self::VERSION_COMPARE_LOWER:
                //    $this->_rollbackResourceDb($configVer, $dbVer);
                //    break;
                case self::VERSION_COMPARE_GREATER:
                    $this->_upgradeResourceDb($dbVer, $configVer);
                    break;
                default:
                    return true;
                    break;
             }
        } elseif ($configVer) {
            $this->_installResourceDb($configVer);
        }
    }

	/**
     * Get version from DB
     *
     * @param   string $resName
     * @return  string
     */
    public function getDbVersion($resName)
    {
        if (is_null(self::$_versions)) {
            try {
                $select = $this->getConnection()->select()->from('config', array('name', 'version'));
                self::$_versions = $this->getConnection()->fetchPairs($select);
            }
            catch (Exception $e){
                self::$_versions = array();
            }
        }
        return isset(self::$_versions[$resName]) ? self::$_versions[$resName] : false;
    }

    /**
     * Set version in DB
     *
     * @param   string $resName
     * @param   string $version
     * @return  int
     */
    public function setDbVersion($resName, $version)
    {
        $dbModuleInfo = array('version' => $version);

        if ($this->getDbVersion($resName)) {
            self::$_versions[$resName] = $version;
            $condition = $this->getConnection()->quoteInto('name=?', $resName);
            return $this->getConnection()->update('config', $dbModuleInfo, $condition);
        }
        else {
            self::$_versions[$resName] = $version;
            return $this->getConnection()->insert('config', $dbModuleInfo);
        }
    }

	/**
     * Run resource installation file
     *
     * @param     string $newVersion
     * @return    boolean
     */
    protected function _installResourceDb($newVersion)
    {
        $oldVersion = $this->_modifyResourceDb('install', '', $newVersion);
        $this->_modifyResourceDb('upgrade', $oldVersion, $newVersion);
        $this->setDbVersion('app', $newVersion);
    }

    /**
     * Run resource upgrade files from $oldVersion to $newVersion
     *
     * @param string $oldVersion
     * @param string $newVersion
     */
    protected function _upgradeResourceDb($oldVersion, $newVersion)
    {
        $this->_modifyResourceDb('upgrade', $oldVersion, $newVersion);
        $this->setDbVersion('app', $newVersion);
    }

	/**
     * Run module modification files. Return version of last applied upgrade (false if no upgrades applied)
     *
     * @param     string $actionType install|upgrade|uninstall
     * @param     string $fromVersion
     * @param     string $toVersion
     * @return    string | false
     */
    protected function _modifyResourceDb($actionType, $fromVersion, $toVersion)
    {
        $sqlFilesDir = APPLICATION_PATH . '/data/db/';
        if (!is_dir($sqlFilesDir) || !is_readable($sqlFilesDir)) {
            return false;
        }
        // Read resource files
        $arrAvailableFiles = array();
        $sqlDir = dir($sqlFilesDir);
        while (false !== ($sqlFile = $sqlDir->read())) {
            $matches = array();
            if (preg_match('#^'.$actionType.'-(.*)\.(sql|php)$#i', $sqlFile, $matches)) {
                $arrAvailableFiles[$matches[1]] = $sqlFile;
            }
        }
        $sqlDir->close();
        if (empty($arrAvailableFiles)) {
            return false;
        }

        // Get SQL files name
        $arrModifyFiles = $this->_getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrAvailableFiles);
        if (empty($arrModifyFiles)) {
            return false;
        }

        $modifyVersion = false;
        foreach ($arrModifyFiles as $file) {
            $sqlFile = $sqlFilesDir . $file['fileName'];
            $fileType = pathinfo($file['fileName'], PATHINFO_EXTENSION);
            // Execute SQL
            if ($this->_conn) {
                try {
                    switch ($fileType) {
                        case 'sql':
                            $sql = file_get_contents($sqlFile);
                            if ($sql!='') {
                                $result = $this->run($sql);
                            } else {
                                $result = true;
                            }
                            break;
                        case 'php':
                            $conn = $this->_conn;
                            $result = include($sqlFile);
                            break;
                        default:
                            $result = false;
                    }
                    if ($result) {
						$this->setDbVersion('app', $file['toVersion']);
                    }
                } catch (Exception $e){
                    echo "<pre>".print_r($e,1)."</pre>";
                    throw Elm::exception('Colony', sprintf('Error in file: "%s" - %s', $sqlFile, $e->getMessage()));
                }
            }
            $modifyVersion = $file['toVersion'];
        }
        self::$_hadUpdates = true;
        return $modifyVersion;
    }

    /**
     * Get sql files for modifications
     *
     * @param     $actionType
     * @param     $fromVersion
     * @param     $toVersion
     * @param     $arrFiles
     * @return    array
     */
    protected function _getModifySqlFiles($actionType, $fromVersion, $toVersion, $arrFiles)
    {
        $arrRes = array();

        switch ($actionType) {
            case 'install':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    if (version_compare($version, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[0] = array('toVersion'=>$version, 'fileName'=>$file);
                    }
                }
                break;

            case 'upgrade':
                uksort($arrFiles, 'version_compare');
                foreach ($arrFiles as $version => $file) {
                    $version_info = explode('-', $version);

                    // In array must be 2 elements: 0 => version from, 1 => version to
                    if (count($version_info)!=2) {
                        break;
                    }
                    $infoFrom = $version_info[0];
                    $infoTo   = $version_info[1];
                    if (version_compare($infoFrom, $fromVersion)!==self::VERSION_COMPARE_LOWER
                        && version_compare($infoTo, $toVersion)!==self::VERSION_COMPARE_GREATER) {
                        $arrRes[] = array('toVersion'=>$infoTo, 'fileName'=>$file);
                    }
                }
                break;
        }
        return $arrRes;
    }

	/**
     * Run Multi-line queries
     *
     * @param string $sql
     * @return array
     */
    public function multipleLineQuery($sql)
    {
        try {
            $stmts = $this->_splitMultiLineQuery($sql);
            $result = array();
            foreach ($stmts as $stmt) {
                $result[] = $this->_conn->query($stmt);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

	/**
     * Split multi statement query
     *
     * @param $sql string
     * @return array
     */
    protected function _splitMultiLineQuery($sql)
    {
        $parts = preg_split('#(;|\'|"|\\\\|//|--|\n|/\*|\*/)#',
            $sql,
            null,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        $q = false;
        $c = false;
        $stmts = array();
        $s = '';

        foreach ($parts as $i=>$part) {
            // strings
            if (($part==="'" || $part==='"') && ($i===0 || $parts[$i-1]!=='\\')) {
                if ($q===false) {
                    $q = $part;
                } else if ($q===$part) {
                    $q = false;
                }
            }

            // single line comments
            if (($part==='//' || $part==='--') && ($i===0 || $parts[$i-1]==="\n")) {
                $c = $part;
            } else if ($part==="\n" && ($c==='//' || $c==='--')) {
                $c = false;
            }

            // multi line comments
            if ($part==='/*' && $c===false) {
                $c = '/*';
            } else if ($part==='*/' && $c==='/*') {
                $c = false;
            }

            // statements
            if ($part===';' && $q===false && $c===false) {
                if (trim($s)!=='') {
                    $stmts[] = trim($s);
                    $s = '';
                }
            } else {
                $s .= $part;
            }
        }
        if (trim($s)!=='') {
            $stmts[] = trim($s);
        }

        return $stmts;
    }
}
