<?php


namespace Okay\Core\QueryFactory;


use Aura\SqlQuery\QueryInterface;

/**
 * Відтворює механізм ?-плейсхолдерів для сумісності коду, який був у Aura.SqlQuery 2.x
 * і повністю прибраний у 3.x.
 *
 * Підтримувані стилі виклику:
 *   where('id = ?', 5)                 одне значення
 *   where('a = ? AND b = ?', $a, $b)   послідовно
 *   where('id IN (?)', [1, 2, 3])      масив розгортається в список
 *   where('id IN (?)', $subSelect)     підзапит підставляється разом з прив'язками
 *   where('id = :id', ['id' => 5])     іменовані проходять як є
 */
trait BindPlaceholdersTrait
{
    /**
     * @var int
     */
    private static $okayBindSeq = 0;

    /**
     * @param string|null $cond умова з ?-плейсхолдерами
     * @param array $binds значення прив'язок
     * @return array [$cond, $namedBinds]
     */
    protected function bindPlaceholders($cond, array $binds)
    {
        if ($cond === null || $binds === []) {
            return [$cond, []];
        }

        // Іменований стиль: асоціативний масив передаємо в Aura без змін
        if ($this->isAssocBinds($binds)) {
            return [$cond, $binds];
        }

        if (strpos((string) $cond, '?') === false
            && count($binds) === 1
            && isset($binds[0])
            && is_array($binds[0])
            && $this->isAssocBinds($binds[0])
        ) {
            return [$cond, $binds[0]];
        }

        // Якщо "?" в умові немає, перетворювати нічого
        if (strpos((string) $cond, '?') === false) {
            return [$cond, $binds];
        }

        $named = [];
        $parts = preg_split('/(\?)/', (string) $cond, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($parts as $k => $part) {
            if ($part !== '?' || $binds === []) {
                continue;
            }

            $value = array_shift($binds);

            // Підзапит як значення: передаємо як іменований бінд, щоб Aura сама
            // підставила SQL підзапиту ПІСЛЯ квотування імен у зовнішній умові
            // (інакше вже проквотовані ідентифікатори підзапиту квотуються вдруге,
            // що ламає плейсхолдери, розташовані далі в тексті умови)
            if ($value instanceof QueryInterface) {
                $name = $this->nextBindName();
                $named[$name] = $value;
                $parts[$k] = ':' . $name;
                continue;
            }

            // Масив розгортається в перелік для IN (...)
            if (is_array($value)) {
                if ($value === []) {
                    $parts[$k] = 'NULL';
                    continue;
                }

                $names = [];
                foreach ($value as $subValue) {
                    $name = $this->nextBindName();
                    $named[$name] = $subValue;
                    $names[] = ':' . $name;
                }

                $parts[$k] = implode(', ', $names);
                continue;
            }

            $name = $this->nextBindName();
            $named[$name] = $value;
            $parts[$k] = ':' . $name;
        }

        return [implode('', $parts), $named];
    }

    /**
     * @return string унікальне ім'я прив'язки
     */
    private function nextBindName()
    {
        return 'okay_bind_' . (++self::$okayBindSeq);
    }

    /**
     * @param array $array
     * @return bool чи масив асоціативний (іменовані прив'язки)
     */
    private function isAssocBinds(array $array)
    {
        if ($array === []) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }
}
