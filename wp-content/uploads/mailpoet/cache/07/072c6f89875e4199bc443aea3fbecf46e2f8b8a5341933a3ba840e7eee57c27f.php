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

/* newsletter/templates/blocks/abandonedCartContent/block.hbs */
class __TwigTemplate_7aaf6efe723d172099e84c4f02836a1d62576bd3c5ac2bbda7ab6cd811e5fb1f extends \MailPoetVendor\Twig\Template
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
<div class=\"mailpoet_abandoned_cart_content_block_overlay\" data-automation-id=\"acc_overlay\">
  <span class=\"mailpoet_overlay_message\">";
        // line 3
        echo $this->extensions['MailPoet\Twig\I18n']->translate("This is only a preview! Your customers will see the product(s) they left in their shopping cart.");
        echo "</span>
</div>
<div class=\"mailpoet_abandoned_cart_content_container\"></div>
<div class=\"mailpoet_block_highlight\"></div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/abandonedCartContent/block.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  41 => 3,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/abandonedCartContent/block.hbs", "C:\\xampp\\htdocs\\assignment2\\wp-content\\plugins\\mailpoet\\views\\newsletter\\templates\\blocks\\abandonedCartContent\\block.hbs");
    }
}
