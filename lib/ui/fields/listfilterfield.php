<?php


namespace Bx\Model\UI\Fields;


use Bitrix\Main\UI\Filter\Options;

class ListFilterField extends BaseFilterField
{
    /**
     * @var array
     */
    private $options;
    private $isMultiple;

    public function __construct(string $id, string $title, array $options)
    {
        parent::__construct($id, $title);
        $this->options = $options;
    }

    /**
     * @return $this
     */
    public function markAsMultiple(): ListFilterField
    {
        $this->isMultiple = true;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'type' => 'list',
            'items' => $this->options,
            'default' => $this->isDefault,
            'params' => [
                'multiple' => $this->isMultiple ? 'Y' : 'N',
            ],
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
                $this->prefix.$this->id => $data[$this->id],
            ];
        }

        return [];
    }
}