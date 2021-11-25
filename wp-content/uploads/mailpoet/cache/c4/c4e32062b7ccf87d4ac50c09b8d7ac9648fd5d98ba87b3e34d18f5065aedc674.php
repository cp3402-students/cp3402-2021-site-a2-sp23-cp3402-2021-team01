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

/* newsletter/templates/blocks/products/settings.hbs */
class __TwigTemplate_089dff8f80dc210d2742ffe3eceef4192b2efb4f69e6243d8b92f1a4145745b2 extends \MailPoetVendor\Twig\Template
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
        echo "<h3>";
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Product selection");
        echo "</h3>
<div class=\"mailpoet_settings_products_selection\"></div>
<div class=\"mailpoet_settings_products_display_options mailpoet_closed\"></div>
<div class=\"mailpoet_settings_products_controls\">
  <div class=\"mailpoet_form_field\">
      <a href=\"javascript:;\" class=\"mailpoet_settings_products_show_product_selection mailpoet_hidden\">";
        // line 6
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Back to selection");
        echo "</a>
      <a href=\"javascript:;\" class=\"mailpoet_settings_products_show_display_options\">";
        // line 7
        echo $this->extensions['MailPoet\Twig\I18n']->translate("Display options");
        echo "</a>
  </div>
  <input type=\"button\" class=\"button button-primary mailpoet_settings_products_insert_selected\" value=\"";
        // line 9
        echo \MailPoetVendor\twig_escape_filter($this->env, $this->extensions['MailPoet\Twig\I18n']->translate("Insert selected"), "html_attr");
        echo "\" />
</div>
";
    }

    public function getTemplateName()
    {
        return "newsletter/templates/blocks/products/settings.hbs";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  55 => 9,  50 => 7,  46 => 6,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "newsletter/templates/blocks/products/settings.hbs", "C:\\xampp\\htdocs\\assignment2\\wp-content\\plugins\\mailpoet\\views\\newsletter\\templates\\blocks\\products\\settings.hbs");
    }
}
