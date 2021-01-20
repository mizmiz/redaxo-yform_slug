<?php

use Ausi\SlugGenerator\SlugOptions;
use Ausi\SlugGenerator\SlugGenerator;

class rex_yform_value_slug extends rex_yform_value_abstract
{
    public function postFormAction()
    {
        $separator = $this->getElement('separator') ?: '-';

        $field = $this->getElement('field');
        $fieldValue = $this->params['value_pool']['email'][$field];

        $generator = new SlugGenerator((new SlugOptions)
            ->setLocale('de')
            ->setDelimiter($separator)
        );
        $slug = $generator->generate($fieldValue);

        $this->setValue($slug);

        $this->params['value_pool']['email'][$this->getName()] = $slug;

        if ($this->saveInDb()) {
            $this->params['value_pool']['sql'][$this->getName()] = $slug;
        }

        if ($this->needsOutput() && $this->params['send'] === 1) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.showvalue.tpl.php');
        }
    }

    function enterObject()
    {
        if ($this->needsOutput()) {
            $this->params['form_output'][$this->getId()] = $this->parse('value.showvalue.tpl.php');
        }
    }

    function getDescription()
    {
        return 'name|label|field|[separator]';
    }

    public function getDefinitions()
    {
        return [
            'type' => 'value',
            'name' => 'slug',
            'values' => [
                'name' => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'field' => ['type' => 'select_name', 'label' => rex_i18n::msg('yform_values_slug_field')],
                'separator' => [
                    'type' => 'choice',
                    'label' => rex_i18n::msg('yform_values_slug_separator'),
                    'default' => '-',
                    'choices' => '-,_,.'
                ],
                'no_db' => ['type' => 'no_db', 'label' => rex_i18n::msg('yform_values_defaults_table')],
            ],
            'description' => rex_i18n::msg('yform_values_slug_description'),
            'db_type' => ['varchar(191)', 'text'],
            'multi_edit' => false,
        ];
    }
}
