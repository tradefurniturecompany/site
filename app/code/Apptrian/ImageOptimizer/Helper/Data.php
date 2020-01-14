<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
 
namespace Apptrian\ImageOptimizer\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig;
    
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    public $moduleList;
    
    /**
     * @var \Magento\Framework\Filesystem
     */
    public $fileSystem;
    
    /**
     * @var \Magento\Framework\Component\ComponentRegistrarInterface
     */
    public $componentRegistrar;
    
    /**
     * @var \Magento\Framework\Shell
     */
    public $shell;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;
    
    /**
     * Magento Root full path.
     *
     * @var null|string
     */
    public $baseDir = null;
    
    /**
     * Module Root full path.
     *
     * @var null|string
     */
    public $moduleDir = null;
    
    /**
     * Logging flag.
     *
     * @var null|int
     */
    public $logging = null;
    
    /**
     * Path to utilities.
     *
     * @var null|string
     */
    public $utilPath = null;
    
    /**
     * Extension (for win binaries)
     *
     * @var null|string
     */
    public $utilExt  = null;
    
    /**
     * Index filename.
     *
     * @var string $indexFilename
     */
    public $indexFilename = 'apptrian_imageoptimizer_index.data';
    
    /**
     * Index path.
     *
     * @var null|string $indexPath
     */
    public $indexPath = null;
    
    /**
     * Index array.
     *
     * @var array $index
     */
    public $index = [];
    
    /**
     * Total count of files in index.
     *
     * @var integer $indexTotalCount
     */
    public $indexTotalCount = 0;
    
    /**
     * Count of files that are optimized.
     *
     * @var integer $indexOptimizedCount
     */
    public $indexOptimizedCount = 0;
    
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\Component\ComponentRegistrarInterface $compReg
     * @param \Magento\Framework\Shell $shell
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\Component\ComponentRegistrarInterface $compReg,
        \Magento\Framework\Shell $shell
    ) {
        $this->scopeConfig        = $context->getScopeConfig();
        $this->logger             = $context->getLogger();
        $this->moduleList         = $moduleList;
        $this->fileSystem         = $fileSystem;
        $this->componentRegistrar = $compReg;
        $this->shell              = $shell;
        
        parent::__construct($context);
    }
    
    /**
     * Returns extension version.
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        $moduleCode = 'Apptrian_ImageOptimizer';
        $moduleInfo = $this->moduleList->getOne($moduleCode);
        return $moduleInfo['setup_version'];
    }
    
    /**
     * Based on provided configuration path returns configuration value.
     *
     * @param string $configPath
     * @return string
     */
    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath);
    }
    
    /**
     * Returns Magento Root full path.
     *
     * @return string
     */
    public function getBaseDir()
    {
        if ($this->baseDir === null) {
            $dir = $this->fileSystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::ROOT
            );
            
            $this->baseDir = $dir->getAbsolutePath();
        }
        
        return $this->baseDir;
    }
    
    /**
     * Returns Module Root full path.
     *
     * @return null|string
     */
    public function getModuleDir()
    {
        if ($this->moduleDir === null) {
            $moduleName = 'Apptrian_ImageOptimizer';
    
            $this->moduleDir = $this->componentRegistrar->getPath(
                \Magento\Framework\Component\ComponentRegistrar::MODULE,
                $moduleName
            );
        }
        
        return $this->moduleDir;
    }
    
    /**
     * Optimized way of getting logging flag from config.
     *
     * @return int
     */
    public function isLoggingEnabled()
    {
        if ($this->logging === null) {
            $this->logging = (int) $this->getConfig(
                'apptrian_imageoptimizer/utility/log_output'
            );
        }
        
        return $this->logging;
    }
    
    /**
     * Checks if exec() function is enabled in php and suhosin config.
     *
     * @return boolean
     */
    public function isExecFunctionEnabled()
    {
        $r = false;
    
        // PHP disabled functions
        $phpDisabledFunctions = array_map(
            'strtolower',
            array_map('trim', explode(',', ini_get('disable_functions')))
        );
        
        // Suhosin disabled functions
        $suhosinDisabledFunctions = array_map(
            'strtolower',
            array_map(
                'trim',
                explode(',', ini_get('suhosin.executor.func.blacklist'))
            )
        );
    
        $disabledFunctions = array_merge(
            $phpDisabledFunctions,
            $suhosinDisabledFunctions
        );
    
        $disabled = false;
    
        if (in_array('exec', $disabledFunctions)) {
            $disabled = true;
        }
    
        if (function_exists('exec') === true && $disabled === false) {
            $r = true;
        }
    
        return $r;
    }
    
    /**
     * Based on config returns array of all paths that will be scaned
     * for images.
     *
     * @return array
     */
    public function getPaths()
    {
        $paths = [];
    
        $pathsString = trim(
            trim(
                $this->getConfig('apptrian_imageoptimizer/general/paths'),
                ';'
            )
        );
        
        $rawPaths = explode(';', $pathsString);
    
        foreach ($rawPaths as $p) {
            $trimmed = trim(trim($p), '/');
            $dirs = explode('/', $trimmed);
            $paths[] = implode('/', $dirs);
        }
    
        return array_unique($paths);
    }
    
    /**
     * Optimizes single file.
     *
     * @param string $filePath
     * @return boolean
     */
    public function optimizeFile($filePath)
    {
        $info     = pathinfo($filePath);
        $output   = '';
        $exitCode = 0;
        
        try {
            switch (strtolower($info['extension'])) {
                case 'jpg':
                case 'jpeg':
                    $output = $this->shell
                        ->execute($this->getJpgUtil($filePath));
                    break;
                case 'png':
                    $output = $this->shell
                        ->execute($this->getPngUtil($filePath));
                    break;
                case 'gif':
                    $output = $this->shell
                        ->execute($this->getGifUtil($filePath));
                    break;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->debug($e->getMessage());
            $this->logger->debug($e->getPrevious()->getMessage());
            
            $exitCode = $e->getPrevious()->getCode();
            
            $this->logger->debug('Exit code: ' . $exitCode);
            
            if ($exitCode == 126) {
                $error = 'Image optimization utility is not executable.';
                        
                $this->logger->debug($error);
            }
            
            return false;
        }
        
        if ($this->isLoggingEnabled()) {
            $this->logger->debug($filePath);
            $this->logger->debug($output);
        }
        
        $permissions = (string) $this->getConfig(
            'apptrian_imageoptimizer/utility/permissions'
        );
        
        if ($permissions) {
            chmod($filePath, octdec($permissions));
        }
        
        return true;
    }
    
    /**
     * Optimization process.
     *
     * @return boolean
     */
    public function optimize()
    {
        $this->loadIndex();
        
        // Get Batch Size
        $batchSize = (int) $this->getConfig(
            'apptrian_imageoptimizer/general/batch_size'
        );
        
        // Get array of files for optimization limited by batch size
        $files = $this->getFiles($batchSize);
        
        $id          = '';
        $item        = [];
        $toUpdate    = [];
        $encodedPath = '';
        $decodedPath = '';
        $filePath    = '';
        
        // Optimize batch of files
        foreach ($files as $id => $item) {
            $encodedPath = $item['f'];
            $decodedPath = utf8_decode($encodedPath);
            $filePath    = realpath($decodedPath);
            
            // If image exists, optimize else remove it from database
            if (file_exists($filePath)) {
                if ($this->optimizeFile($filePath)) {
                    $toUpdate[$id]['f'] = $encodedPath;
                }
            } else {
                // Remove files that do not exist anymore from the index
                unset($this->index[$id]);
            }
        }
        
        $i = '';
        $f = [];
        
        // Itereate over $toUpdate array and set modified time
        // (mtime) takes a split second to update
        foreach ($toUpdate as $i => $f) {
            $encodedPath = $f['f'];
            $decodedPath = utf8_decode($encodedPath);
            $filePath    = realpath($decodedPath);
            
            if (file_exists($filePath)) {
                // Update optimized file information in index
                $this->index[$i]['t'] = filemtime($filePath);
            }
            
            // Free Memory
            unset($toUpdate[$i]);
        }
        
        return $this->saveIndex();
    }
    
    /**
     * Scan and reindex process.
     *
     * @return boolean
     */
    public function scanAndReindex()
    {
        $this->loadIndex();
        
        $id          = '';
        $item        = [];
        $encodedPath = '';
        $decodedPath = '';
        $filePath    = '';
        
        // Check index for files that need to be updated and/or removed
        foreach ($this->index as $id => $item) {
            $encodedPath = $item['f'];
            $decodedPath = utf8_decode($encodedPath);
            $filePath    = realpath($decodedPath);
            
            if (file_exists($filePath)) {
                if ($item['t'] != 0 && filemtime($filePath) != $item['t']) {
                    // Update time to 0 in index so it can be optimized again
                    $this->index[$id]['t'] = 0;
                }
            } else {
                // Remove files that do not exist anymore from the index
                unset($this->index[$id]);
            }
        }
        
        $paths    = $this->getPaths();
        $path     = '';
        
        // Scan for new files and add them to the index
        foreach ($paths as $path) {
            $this->scanAndReindexPath($path);
        }
        
        return $this->saveIndex();
    }
    
    /**
     * Scans provided path for images and adds them to index.
     *
     * @param string $path
     */
    public function scanAndReindexPath($path)
    {
        $id          = '';
        $encodedPath = '';
        $filePath    = '';
        $file        = null;
    
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->getBaseDir() . $path,
                \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
            )
        );
    
        foreach ($iterator as $file) {
            if ($file->isFile()
                && preg_match(
                    '/^.+\.(jpe?g|gif|png)$/i',
                    $file->getFilename()
                )
            ) {
                $filePath = $file->getRealPath();
                if (!is_writable($filePath)) {
                    continue;
                }

                $encodedPath = utf8_encode($filePath);
                $id          = hash('md5', $encodedPath);

                // Add only if file is not already in the index
                if (!isset($this->index[$id])) {
                    $this->index[$id] = ['f' => $encodedPath, 't' => 0];
                }
            }
    
            // Free Memory
            $file = null;
        }
        
        // Free Memory
        $iterator = null;
    }
    
    /**
     * Checks if server OS is Windows
     *
     * @return bool
     */
    public function isWindows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Alias for getUtil() and .gif
     *
     * @param string $filePath
     * @return string
     */
    public function getGifUtil($filePath)
    {
        return $this->getUtil('gif', $filePath);
    }
    
    /**
     * Alias for getUtil() and .jpg
     *
     * @param string $filePath
     * @return string
     */
    public function getJpgUtil($filePath)
    {
        return $this->getUtil('jpg', $filePath);
    }
    
    /**
     * Alias for getUtil() and .png
     *
     * @param string $filePath
     * @return string
     */
    public function getPngUtil($filePath)
    {
        return $this->getUtil('png', $filePath);
    }
    
    /**
     * Formats and returns the shell command string for an image optimization
     * utility.
     *
     * @param string $type - This is image type. Valid values gif|jpg|png
     * @param string $filePath - Path to the image to be optimized
     * @return string
     */
    public function getUtil($type, $filePath)
    {
        $exactPath = $this->getConfig(
            'apptrian_imageoptimizer/utility/' . $type . '_path'
        );
        
        // If utility exact path is set use it
        if ($exactPath != '') {
            $cmd = $exactPath;
        // Use path to extension's local utilities
        } else {
            $cmd = $this->getUtilPath()
                . '/'
                . $this->getConfig('apptrian_imageoptimizer/utility/' . $type)
                . $this->getUtilExt();
        }
        
        $cmd .= ' ' . $this->getConfig(
            'apptrian_imageoptimizer/utility/' . $type . '_options'
        );
        
        return str_replace('%filepath%', $filePath, $cmd);
    }
    
    /**
     * Gets and stores utility extension.
     * Checks server OS and determine utility extension.
     *
     * @return string
     */
    public function getUtilExt()
    {
        if ($this->utilExt === null) {
            $this->utilExt = $this->isWindows() ? '.exe' : '';
        }
         
        return $this->utilExt;
    }
    
    /**
     * Gets and stores path to utilities. Checks server OS and config to
     * determine the path where image optimization utilities are.
     *
     * @return string
     */
    public function getUtilPath()
    {
        if ($this->utilPath === null) {
            $useSixtyFourBit = (int) $this->getConfig(
                'apptrian_imageoptimizer/utility/use64bit'
            );
            
            if ($useSixtyFourBit) {
                $bit = '64';
            } else {
                $bit = '32';
            }
    
            $os = $this->isWindows() ? 'win' . $bit : 'elf' . $bit;
    
            $pathString = trim(
                trim($this->getConfig('apptrian_imageoptimizer/utility/path')),
                '/'
            );
            
            $dirs       = explode('/', $pathString);
            $path       = implode('/', $dirs);
    
            $this->utilPath = $this->getModuleDir() . '/' . $path . '/' . $os;
        }
    
        return $this->utilPath;
    }
    
    /**
     * Returns index path.
     *
     * @return string
     */
    public function getIndexPath()
    {
        if ($this->indexPath === null) {
            $dir = $this->fileSystem->getDirectoryRead(
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR
            );
            
            $this->indexPath = $dir->getAbsolutePath() . $this->indexFilename;
        }
        
        return $this->indexPath;
    }
    
    /**
     * Returns array of files for optimization limited by $batchSize.
     *
     * @param int $batchSize
     */
    public function getFiles($batchSize)
    {
        $files   = [];
        $counter = 0;
    
        foreach ($this->index as $id => $f) {
            if ($counter == $batchSize) {
                break;
            }
    
            if ($f['t'] == 0) {
                $files[$id] = $f;
                $counter++;
            }
        }
    
        return $files;
    }
    
    /**
     * Returns count of indexed and optmized files.
     *
     * @return array
     */
    public function getFileCount()
    {
        $this->loadIndex();
    
        $r['indexed']   = $this->indexTotalCount;
        $r['optimized'] = $this->indexOptimizedCount;
    
        // Free memory
        $this->index = null;
    
        return $r;
    }
    
    /**
     * Clear index (Empty index file).
     *
     * @return boolean
     */
    public function clearIndex()
    {
        $r = file_put_contents($this->getIndexPath(), '', LOCK_EX);
        
        if ($r === false) {
            $this->logger->debug('Clear index operation failed.');
        } else {
            $r = true;
        }
        
        return $r;
    }
    
    /**
     * Load index from a file.
     */
    public function loadIndex()
    {
        $filePath = $this->getIndexPath();
        
        if (file_exists($filePath)) {
            $line = '';
            $l    = [];
            $id   = '';
            $file = [];
            
            $str = file_get_contents($filePath);
            
            if ($str != '') {
                $data = explode("\n", $str);
                
                // Free Memory
                unset($str);
                
                $this->indexTotalCount = count($data);
                
                $i = 0;
                
                for ($i = 0; $i < $this->indexTotalCount; $i++) {
                    $line      = $data[$i];
                    $l         = explode('|', $line);
                    
                    if (!array_key_exists(0, $l)
                        || !array_key_exists(1, $l)
                        || !array_key_exists(2, $l)
                    ) {
                        $i++;
                        $message = sprintf(
                            __('Your image index is corrupted at line %s.'),
                            $i
                        );
                        
                        $this->logger->critical($message);
                            
                        throw new \Exception($message);
                    }
                    
                    $id        = (string) $l[0];
                    $file['f'] = (string) $l[1];
                    $file['t'] = (int) $l[2];
                    
                    $this->index[$id] = $file;
                    
                    if ($file['t'] > 0) {
                        $this->indexOptimizedCount++;
                    }
                    
                    // Free Memory
                    unset($data[$i]);
                }
                
                // Free Memory
                $data = null;
            }
            
            if (!$this->index) {
                $this->index = [];
            }
        }
    }
    
    /**
     * Save index to a file.
     *
     * @return boolean
     */
    public function saveIndex()
    {
        $id   = '';
        $f    = '';
        $data = [];
        $c    = 0;
        $b    = 0;
        $r    = false;
        
        // Truncate existing index file
        $this->clearIndex();
        
        foreach ($this->index as $id => $f) {
            // str_replace() removes | from filename because | is delimiter
            $data[] = sprintf(
                '%s|%s|%d',
                $id,
                str_replace('|', '', $f['f']),
                $f['t']
            );
            
            // Free memory
            unset($this->index[$id]);
            
            if ($c == 100000) {
                // Save part of the file
                $r = $this->saveToFile($data, $b);
                
                // Free memory
                $data = [];
                
                // Increment batch
                $b++;
                
                // Reset count
                $c = 0;
            } else {
                // Increment count
                $c++;
            }
        }
        
        // Save last part of the file
        $r = $this->saveToFile($data, $b);
        
        // Free memory
        $this->index = null;
        
        if ($r === false) {
            $this->logger->debug('Writting index to a file failed.');
        } else {
            $r = true;
        }
        
        return $r;
    }
    
    /**
     * Saves batch of data to a file.
     *
     * @param array $data
     * @param int $b
     */
    public function saveToFile($data, $b)
    {
        $r = true;
        
        if (!empty($data)) {
            $fh = fopen($this->getIndexPath(), 'a');
            
            if ($b != 0) {
                fwrite($fh, "\n");
            }
            
            $r = fwrite($fh, implode("\n", $data));
            
            fclose($fh);
        }
        
        return $r;
    }
}
