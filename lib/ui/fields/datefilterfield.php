<?php


namespace Bx\Model\UI\Fields;


use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\UI\Filter\Options;

class DateFilterField extends BaseFilterField
{

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'type' => 'date',
            'default' => $this->isDefault,
        ];
    }

    /**
     * @param Options $options
     * @return array
     * @throws ObjectException
     */
    public function getFilterField(Options $options): array
    {
        $data = $this->getOptionsFilter($options);
        $type = $data[$this->id.'_datesel'];
        $currentDay = new Date();
        switch ($type) {
            case 'YESTERDAY':
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add('-1 day'),
                    "to_{$this->prefix}{$this->id}" => $currentDay
                ];
            case 'CURRENT_DAY':
                return [
                    "from_{$this->prefix}{$this->id}" => $currentDay,
                    "to_{$this->prefix}{$this->id}" => (clone $currentDay)->add('1 days'),
                ];
            case 'TOMORROW':
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add('1 day'),
                    "to_{$this->prefix}{$this->id}" => (clone $currentDay)->add('2 days'),
                ];
            case 'CURRENT_WEEK':
                $diff = (int)date('N') - 1;
                $startDate = (clone $currentDay)->add("-{$diff} days");
                $endDate = (clone $startDate)->add('7 days');
                return [
                    "from_{$this->prefix}{$this->id}" => $startDate,
                    "to_{$this->prefix}{$this->id}" => $endDate
                ];
            case 'CURRENT_MONTH':
                $startMonth = new Date(date('Y-m-').'01', 'Y-m-d');
                return [
                    "from_{$this->prefix}{$this->id}" => $startMonth,
                    "to_{$this->prefix}{$this->id}" => (clone $startMonth)->add('1 months'),
                ];
            case 'CURRENT_QUARTER':
                $startMonth = new Date(date('Y-m-').'01', 'Y-m-d');
                return [
                    "from_{$this->prefix}{$this->id}" => $startMonth,
                    "to_{$this->prefix}{$this->id}" => (clone $startMonth)->add('4 months'),
                ];
            case 'LAST_7_DAYS':
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add('-7 days'),
                    "to_{$this->prefix}{$this->id}" => $currentDay
                ];
            case 'LAST_30_DAYS':
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add('-30 days'),
                    "to_{$this->prefix}{$this->id}" => $currentDay
                ];
            case 'LAST_60_DAYS':
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add('-60 days'),
                    "to_{$this->prefix}{$this->id}" => $currentDay
                ];
            case 'LAST_90_DAYS':
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add('-90 days'),
                    "to_{$this->prefix}{$this->id}" => $currentDay
                ];
            case 'PREV_DAYS':
                $days = (int)$data[$this->id.'_days'];
                return [
                    "from_{$this->prefix}{$this->id}" => (clone $currentDay)->add("-{$days} days"),
                    "to_{$this->prefix}{$this->id}" => $currentDay
                ];
            case 'NEXT_DAYS':
                $days = (int)$data[$this->id.'_days'];
                return [
                    "from_{$this->prefix}{$this->id}" => $currentDay,
                    "to_{$this->prefix}{$this->id}" => (clone $currentDay)->add("{$days} days")
                ];
            case 'MONTH':
                $month = (int)$data[$this->id.'_month'];
                $year = (int)$data[$this->id.'_year'];
                $startMonth = new Date("{$year}-{$month}-01", 'Y-n-d');
                return [
                    "from_{$this->prefix}{$this->id}" => $startMonth,
                    "to_{$this->prefix}{$this->id}" => (clone $startMonth)->add("1 months")
                ];
            case 'QUARTER':
                $list = [1, 1, 4, 7, 10];
                $quarter = (int)$data[$this->id.'_quarter'];
                $year = (int)$data[$this->id.'_year'];
                $startQuarter = new Date("{$year}-{$list[$quarter]}-01", 'Y-n-d');
                return [
                    "from_{$this->prefix}{$this->id}" => $startQuarter,
                    "to_{$this->prefix}{$this->id}" => (clone $startQuarter)->add("3 months")
                ];
            case 'YEAR':
                $year = (int)$data[$this->id.'_year'];
                $startYear = new Date("{$year}-01-01", 'Y-m-d');
                return [
                    "from_{$this->prefix}{$this->id}" => $startYear,
                    "to_{$this->prefix}{$this->id}" => (clone $startYear)->add("1 years")
                ];
            case 'EXACT':
            case 'RANGE':
                $startDate = new Date($data[$this->id.'_from'], 'd.m.Y');
                $endDate = new Date($data[$this->id.'_to'], 'd.m.Y');
                return [
                    "from_{$this->prefix}{$this->id}" => $startDate,
                    "to_{$this->prefix}{$this->id}" => $endDate->add("1 days")
                ];
            case 'LAST_WEEK':
            case 'LAST_MONTH':
            case 'NEXT_WEEK':
            case 'NEXT_MONTH':
                return [];
        }

        return [];
    }
}