<?php


namespace Bx\Model\UI\Fields;


use Bitrix\Main\UI\Filter\Options;

class StringFilterField extends BaseFilterField
{

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'default' => $this->isDefault,
        ];
    }

    /**
     * @param Options $options
     * @return array
     */
    public function getFilterField(Options $options): array
    {
        $data = $this->getOptionsFilter($options);
        if (!empty($data[$this->id])) {
            return [
                'like_'.$this->id => $data[$this->id],
            ];
        }

        return [];
    }
}