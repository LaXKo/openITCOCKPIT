<?php


namespace itnovum\openITCOCKPIT\ConfigGenerator;


use App\itnovum\openITCOCKPIT\ConfigGenerator\ContainerConfigInterface;
use Cake\Core\Configure;

class PhpFpmOitc extends ConfigGenerator implements ConfigInterface, ContainerConfigInterface {
    /** @var string */
    protected $templateDir = 'PhpFpmOitc';
    /** @var string */
    protected $template = 'oitc.conf.tpl';
    /** @var string */
    protected $realOutfile = '';
    /** @var string */
    protected $linkedOutfile = '';
    /** @var string */
    protected $commentChar = ';';
    /** @var array */
    protected $defaults = [
        'int' => [
            'max_children'      => 5,
            'start_servers'     => 2,
            'min_spare_servers' => 1,
            'max_spare_servers' => 3
        ],
    ];

    /**
     * PhpFpmOitc constructor.
     * determines the current php version as the file gets written in the latest php-fpm pool.d directory
     * this changes with every php update (7.4 to 7.5 to 7.6 and so on)
     */
    public function __construct() {
        try {
            $version = substr(PHP_VERSION, 0, 3);
            if (is_string($version)) {
                if (is_dir('/etc/php/' . $version)) {
                    $this->realOutfile = '/etc/php/' . $version . '/fpm/pool.d/oitc.conf';
                    $this->linkedOutfile = '/etc/php/' . $version . '/fpm/pool.d/oitc.conf';
                } else {
                    throw new \Exception('/etc/php/' . $version . ' directory does not exists');
                }
            } else {
                throw new \Exception('PHP version could not be determined');
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function getAngularDirective() {
        return 'php-fpm-oitc';
    }

    protected $dbKey = 'PhpFpmOitc';

    /**
     * @param array $data
     * @return array|bool|true
     */
    public function customValidationRules($data) {
        return true;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getHelpText($key) {
        $help = [
            'max_children'      => __('Maximum number of children that can be alive at the same time'),
            'start_servers'     => __('Number of children created on startup.'),
            'min_spare_servers' => __('Minimum number of children in idle state (waiting to process).'),
            'max_spare_servers' => __('Maximum number of children in idle state (waiting to process).'),
        ];

        if (isset($help[$key])) {
            return $help[$key];
        }

        return '';
    }

    public function getValuesFromEnvironment() {
        return [
            [
                'key'   => 'max_children',
                'value' => env('OITC_PHP_FPM_PM_MAX_CHILDREN', 5),
            ],
            [
                'key'   => 'start_servers',
                'value' => env('OITC_PHP_FPM_PM_START_SERVERS', 2),
            ],
            [
                'key'   => 'min_spare_servers',
                'value' => env('OITC_PHP_FPM_PM_MIN_SPARE_SERVERS', 1),
            ],
            [
                'key'   => 'max_spare_servers',
                'value' => env('OITC_PHP_FPM_PM_MAX_SPARE_SERVERS', 3),
            ]
        ];
    }

    /**
     * @param array $dbRecords
     * @return bool|int
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function writeToFile($dbRecords) {
        $config = $this->mergeDbResultWithDefaultConfiguration($dbRecords);
        $configToExport = [];
        foreach ($config as $type => $fields) {
            foreach ($fields as $key => $value) {
                $configToExport[$key] = $value;
            }
        }
        return $this->saveConfigFile($configToExport);
    }


    /**
     * @param array $dbRecords
     * @return array|bool
     */
    public function migrate($dbRecords) {
        if (!file_exists($this->linkedOutfile)) {
            return false;
        }
        return $this->mergeDbResultWithDefaultConfiguration($dbRecords);
    }
}
