<?php
namespace App\Core;
class Render{

    private string $viewPath;
    private string $templatePath;
    private array $data = [];
    public function __construct(string $view, string $template="frontoffice"){
        $this->setViewPath($view);
        $this->setTemplatePath($template);
    }

    public function setViewPath(string $view): void{
        $this->viewPath = "../Views/".$view.".php";
    }

    public function setTemplatePath(string $template): void{
        $this->templatePath = "../Views/Templates/".$template.".php";
    }

    public function assign(string $key, string $value){
        $this->data[$key] = $value;
    }

    public function render(): void{
        extract($this->data);
        include $this->templatePath;
    }
}