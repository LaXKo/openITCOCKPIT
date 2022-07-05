<?php
//
// This code is based on the work of FriendsOfCake / CakePdf
// Many thanks!
// https://github.com/FriendsOfCake/CakePdf/blob/master/src/Pdf/CakePdf.php
//
// Licensed under The MIT License
//


declare(strict_types=1);

namespace PuppeteerPdf\Pdf;

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Router;
use SplFileInfo;

class PdfRenderer {
    /**
     * Layout for the View
     *
     * @var string
     */
    protected $_layout = 'default';

    /**
     * Path to the layout - defaults to 'pdf'
     *
     * @var string
     */
    protected $_layoutPath = 'pdf';

    /**
     * Template for the view
     *
     * @var string|null
     */
    protected $_template;

    /**
     * Path to the template - defaults to 'Pdf'
     *
     * @var string
     */
    protected $_templatePath = 'Pdf';

    /**
     * View for render
     *
     * @var string
     */
    protected $_viewRender = 'View';

    /**
     * Vars to sent to render
     *
     * @var array
     */
    protected $_viewVars = [];

    /**
     * Theme for the View
     *
     * @var string|null
     */
    protected $_theme = null;

    /**
     * Helpers to be used in the render
     *
     * @var array
     */
    protected $_helpers = ['Html'];

    /**
     * Html to be rendered
     *
     * @var string
     */
    protected $_html;

    /**
     * Constructor
     *
     * @param array $config Pdf configs to use
     */
    public function __construct(array $config = []) {
        $config = array_merge(
            (array)Configure::read('PuppeteerPdf'),
            $config
        );

        /*
                 * @todo implement options
                 * https://pptr.dev/#?product=Puppeteer&version=v13.7.0&show=api-pagepdfoptions
                $options = [
                    'pageSize',
                    'orientation',
                    'margin',
                    'title',
                    'encoding',
                    'protect',
                    'userPassword',
                    'ownerPassword',
                    'permissions',
                    'cache',
                    'delay',
                    'windowStatus',
                ];
                foreach ($options as $option) {
                    if (isset($config[$option])) {
                        $this->{$option}($config[$option]);
                    }
                }
                */
    }

    /**
     * Create pdf content from html. Can be used to write to file or with PdfView to display
     *
     * @param null|string $html Html content to render. If omitted, the template will be rendered with viewVars and layout that have been set.
     * @return string
     * @throws \Cake\Core\Exception\Exception
     */
    public function output(?string $html = null): string {
        if ($html === null) {
            $html = $this->_render();
        }
        $this->html($html);

        // For Debugging - dumps the HTML passed to Puppeteer into a file
        //$fd = fopen('/tmp/html2pdf_debug.html', 'w+');
        //fwrite($fd, $html);
        //fclose($fd);

        $PuppeteerClient = new PuppeteerClient();
        $output = $PuppeteerClient->html2pdf($html);
        return $output;
    }

    /**
     * Get/Set Html.
     *
     * @param null|string $html Html to set
     * @return mixed
     */
    public function html(?string $html = null) {
        if ($html === null) {
            return $this->_html;
        }
        $this->_html = $html;

        return $this;
    }

    /**
     * Writes output to file
     *
     * @param string $destination Absolute file path to write to
     * @param bool $create Create file if it does not exist (if true)
     * @param string|null $html Html to write
     * @return bool
     */
    public function write(string $destination, bool $create = true, ?string $html = null): bool {
        $output = $this->output($html);

        $fileInfo = new SplFileInfo($destination);

        if (!$create || $fileInfo->isFile()) {
            return (bool)file_put_contents($destination, $output);
        }

        if (!$fileInfo->isFile()) {
            mkdir($fileInfo->getPath(), 0777, true);
        }

        return (bool)file_put_contents($destination, $output);
    }

    /**
     * Template and layout
     *
     * @param mixed $template Template name or null to not use
     * @param mixed $layout Layout name or null to not use
     * @return mixed
     */
    public function template($template = false, $layout = null) {
        if ($template === false) {
            return [
                'template' => $this->_template,
                'layout'   => $this->_layout,
            ];
        }
        $this->_template = $template;
        if ($layout !== null) {
            $this->_layout = $layout;
        }

        return $this;
    }

    /**
     * Template path
     *
     * @param mixed $templatePath The path of the template to use
     * @return mixed
     */
    public function templatePath($templatePath = false) {
        if ($templatePath === false) {
            return $this->_templatePath;
        }

        $this->_templatePath = $templatePath;

        return $this;
    }

    /**
     * Layout path
     *
     * @param mixed $layoutPath The path of the layout file to use
     * @return mixed
     */
    public function layoutPath($layoutPath = false) {
        if ($layoutPath === false) {
            return $this->_layoutPath;
        }

        $this->_layoutPath = $layoutPath;

        return $this;
    }

    /**
     * View class for render
     *
     * @param string|null $viewClass name of the view class to use
     * @return mixed
     */
    public function viewRender(?string $viewClass = null) {
        if ($viewClass === null) {
            return $this->_viewRender;
        }
        $this->_viewRender = $viewClass;

        return $this;
    }

    /**
     * Variables to be set on render
     *
     * @param array $viewVars view variables to set
     * @return mixed
     */
    public function viewVars(?array $viewVars = null) {
        if ($viewVars === null) {
            return $this->_viewVars;
        }
        $this->_viewVars = array_merge($this->_viewVars, (array)$viewVars);

        return $this;
    }

    /**
     * Theme to use when rendering
     *
     * @param string $theme theme to use
     * @return mixed
     */
    public function theme(?string $theme = null) {
        if ($theme === null) {
            return $this->_theme;
        }
        $this->_theme = $theme;

        return $this;
    }

    /**
     * Helpers to be used in render
     *
     * @param array $helpers helpers to use
     * @return mixed
     */
    public function helpers(?array $helpers = null) {
        if ($helpers === null) {
            return $this->_helpers;
        }
        $this->_helpers = (array)$helpers;

        return $this;
    }

    /**
     * Build and set all the view properties needed to render the layout and template.
     *
     * @return string The rendered template wrapped in layout.
     */
    protected function _render(): string {
        $viewClass = $this->viewRender();
        /** @psalm-var class-string<\Cake\View\View> */
        $viewClass = App::className($viewClass, 'View', $viewClass === 'View' ? '' : 'View');

        $viewVars = [
            'theme',
            'layoutPath',
            'templatePath',
            'template',
            'layout',
            'helpers',
            'viewVars',
        ];
        $viewOptions = [];
        foreach ($viewVars as $var) {
            $prop = '_' . $var;
            $viewOptions[$var] = $this->{$prop};
        }

        $request = Router::getRequest();
        if (!$request) {
            $request = ServerRequestFactory::fromGlobals();
        }

        $View = new $viewClass(
            $request,
            null,
            null,
            $viewOptions
        );

        return $View->render();
    }
}
