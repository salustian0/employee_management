<?php

namespace App\Views;
/**
 * Classe responsável pelo gerenciamento das views
 * @author Renan Salustiano <renansalustiano2020@gmail.com>
 * 23/02/2022
 */
class View
{
    /**
     * Váriavel responsável por guardar os arquivos css setados
     * @param array $css
     */
    private $css;
    /**
     * Váriavel responsável por guardar os arquivos javascript setados
     * @param array $js
     */
    private $js;
    /**
     * Váriavel responsável por guardar as variáveis da view que será chamada
     * @param array $vars
     */
    private $vars;
    /**
     * Variável responsável por guardar o template que será chamado caso haja um
     * @param string $template
     */
    private $template;
    /**
     * Váriavel responsável por guardar a página que será chamada
     * @param string $page
     */
    private $page;

    /**
     * Caminhos das pastas Templates/Pages
     */
    const TEMPLATES_PATH = SITE_ROOT . "/App/Views/Templates/";
    const PAGES_PATH = SITE_ROOT . "/App/Views/Pages/";

    /**
     * @param bool $applyDefaults aplicar configurações padrão?
     */
    public function __construct($applyDefaults = true)
    {
        $this->vars['_MEDIA_URL'] = MEDIA_URL ?? "/";
        if ($applyDefaults) {
            $this->setTemplate('default_template');
            $this->setCssFile('estilos.css');
        }
    }

    /**
     * setar template a ser chamado (precisa estar dentro de App/Views/Templates)
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Adiciona arquivo css á instancia da view
     * @param $cssFile
     * @return false|void
     */
    public function setCssFile($cssFile): void
    {
        if (is_array($cssFile)) {
            $this->css = array_merge($this->css, $cssFile);
            return;
        }
        $this->css[] = $cssFile;
    }

    /**
     * Renderiza scripts de chamada dos arquivos css setados na instancia da view
     * @return void
     */
    private function renderCssFiles(): void
    {
        if (empty($this->css)) return;

        $html = "<!-- Arquivos css renderizados dinâmicamente -->" . PHP_EOL;
        foreach ($this->css as $css) {
            if (file_exists(SITE_ROOT . "/public/css/{$css}")) {
                $href = SITE_URL . "/public/css/{$css}";
                $html .= "<link rel='stylesheet' type='text/css' href='{$href}?CACHE_BUSTING=" . md5(time()) . "'>" . PHP_EOL;
            }
        }
        $html .= "<!-- Arquivos css renderizados dinâmicamente -->" . PHP_EOL;
        echo $html;
    }

    /**
     * Adiciona arquivo javascript a instancia da view
     * @param $jsFile
     * @return void
     */
    public function setJsFile($jsFile, $options = []): void
    {
        if (is_array($jsFile)) {
            $this->js = array_merge($this->js, $jsFile);
        }
        $this->js[] = $jsFile;
    }

    /**
     * Renderiza scripts de chamada dos arquivos js setados na instancia da view
     * @return void
     */
    private function renderJsFiles(): void
    {
        if (empty($this->js)) return;

        $html = "<!-- Arquivos js renderizados dinâmicamente -->" . PHP_EOL;
        foreach ($this->js as $js) {
            if (file_exists(SITE_ROOT . "/public/js/{$js}")) {
                $src = SITE_URL . "/public/js/{$js}";
                $html .= "<script type='text/javascript' src='{$src}?CACHE_BUSTING=" . md5(time()) . "'></script>" . PHP_EOL;
            }
        }
        $html .= "<!-- Arquivos js renderizados dinâmicamente -->" . PHP_EOL;

        echo $html;
    }

    /**
     * Echo no  valor da variável caso exista senão retorna 'null'
     * @param string $varName
     */
    public function showOrNull(string $varName): void
    {
        echo(isset($this->vars[$varName]) ? $this->vars[$varName] : null);
    }

    /**
     * Renderiza a página/template
     * @param $view
     * @param $vars
     */
    public function render(string $page, array $vars = array()): void
    {
        $this->vars = array_merge($this->vars, $vars);
        if (!empty($this->template)) {
            $path = self::TEMPLATES_PATH . $this->template . '.php';
            $this->page = $page;
        } else {
            $path = self::PAGES_PATH . $this->page . ".php";
        }

        if (file_exists($path)) {
            extract($this->vars);
            require_once $path;
            echo PHP_EOL;
        }
    }

    /**
     * Renderiza a página
     */
    private function renderPage()
    {
        $path = self::PAGES_PATH . $this->page . ".php";
        if (file_exists($path)) {
            extract($this->vars);
            require_once $path;
            echo PHP_EOL;
        }
    }
}