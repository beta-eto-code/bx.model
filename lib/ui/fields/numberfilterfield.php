<?php


namespace Bx\Model\UI\Fields;


use Bitrix\Main\UI\Filter\Options;

class NumberFilterField extends BaseFilterField
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'type' => 'number',
            'default' => $this->isDefault,
        ];
    }

    public function getFilterField(Options $options): array
    {
        $data = $this->getOptionsFilter($options);
        if (!empty($data[$this->id])) {
            return [
                $this->id => $data[$this->id],
            ];
        }

        return [];
    }
}