<?php


namespace Bx\Model\UI\Admin\Form;

use Bx\Model\Interfaces\ModelServiceInterface;
use CAdminForm;

class ModelForm
{
    /**
     * @var ModelServiceInterface
     */
    private $modelService;
    /**
     * @var CAdminForm
     */
    private $form;
    /**
     * @var string
     */
    private $formName;
    /**
     * @var FormTab[]
     */
    private $formTabs;

    public function __construct(ModelServiceInterface $modelService, string $formName)
    {
        $this->modelService = $modelService;
        $this->formName = $formName;
    }

    /**
     * @param string $code
     * @param string $title
     * @param string $icon
     * @return FormTab
     */
    public function addTab(string $code, string $title, string $icon = ''): FormTab
    {
        return $this->formTabs[$code] = new FormTab($code, $title, $icon);
    }

    /**
     * @return array
     */
    private function prepareTabs(): array
    {
        $result = [];
        foreach ($this->formTabs as $tab) {
            $result[] = $tab->toArray();
        }

        return $result;
    }

    public function show()
    {
        $this->form = new CAdminForm($this->formName, $this->prepareTabs());
    }
}