<?php

use MailPoetVendor\Twig\Environment;
use MailPoetVendor\Twig\Error\LoaderError;
use MailPoetVendor\Twig\Error\RuntimeError;
use MailPoetVendor\Twig\Extension\SandboxExtension;
use MailPoetVendor\Twig\Markup;
use MailPoetVendor\Twig\Sandbox\SecurityError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedTagError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFilterError;
use MailPoetVendor\Twig\Sandbox\SecurityNotAllowedFunctionError;
use MailPoetVendor\Twig\Source;
use MailPoetVendor\Twig\Template;

/* newsletter/templates/blocks/social/block.hbs */
class __TwigTemplate_5d7f2b13e6cd85909603bd9b5d0e9c091d060c834f582a1c8baa510ed13f609b extends \MailPoetVendor\Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<div class=\"mailpoet_tools\"></div>
<div
    class=\"mailpoet_content mailpoet_social\"
    data-automation-id=\"socialBlock\"
    style=\"text-align: {{model.styles.block.textAlign}}\"
>
</div>
<div class=\"mailpoet_block_highlight\"></div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/social/block.hbs";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/social/block.hbs", "C:\\xampp\\htdocs\\assignment2\\wp-content\\plugins\\mailpoet\\views\\newsletter\\templates\\blocks\\social\\block.hbs");
    }
}
