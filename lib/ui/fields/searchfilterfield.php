<?php


namespace Bx\Model\UI\Fields;


use Bitrix\Main\UI\Filter\Options;

class SearchFilterField extends StringFilterField
{
    public function toArray(): array
    {
        return [
                "id" => $this->id,
                "name" => $this->title,
                "filterable" => "?",
                "quickSearch" => "?",
                "default" => $this->isDefault,
            ];
    }

    public function getFilterField(Options $options): array
    {
        return parent::getFilterField($options);
    }
}