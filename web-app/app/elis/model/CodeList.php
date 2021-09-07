<?php

namespace elis\model;

use elis\utils;

/**
 * Code list class
 * @version 0.2.0 210619 exists()
 * @version 0.0.1 201128 creating
 */
class CodeList
{

    /**
     * CodeListItem array
     *
     * @var array
     */
    private $items;

    /**
     * Path to the code list directory
     *
     * @var string
     */
    private static $path = 'code-list/';

    /**
     * Constructor can load json file into items array
     *
     * @param string $filename
     */
    public function __construct(string $filename = null)
    {
        $this->items = [];
        if ($filename) {
            $this->load($filename);
        }
    }

    /**
     * Load json file into items array
     *
     * @param string $filename
     * @return bool true when success, otherwise false
     */
    public function load(string $filename): bool
    {
        if (($content = file_get_contents(self::$path . $filename)) === false) {
            return false;
        }
        $items = json_decode($content, true);
        if (!is_array($items)) {
            return false;
        }
        foreach ($items as $code => $data) {
            if (is_string($data)) {
                $data = ['name' => $data];
            }
            $this->items[$code] = new CodeListItem($code, $data);
        }
        return true;
    }

    /**
     * Code List Item array
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get Code List Item from items
     *
     * @param string $code
     * @return CodeListItem|null
     */
    public function getItem(string $code): ?CodeListItem
    {
        if (isset($this->items[$code])) {
            return $this->items[$code];
        }
        return null;
    }

    public function exists(string $state): bool
    {
        $exists = false;
        foreach ($this->getItems() as $item) {
            if ($item->getCode() == $state) {
                return true;
            }
        }
        return $exists;
    }


    public function legendToStr(string $caption): string
    {
        $itemTmplt = new utils\Template("other/legend-item.html");
        $items = '';
        foreach ($this->getItems() as $item) {
            $itemTmplt->clearData()->setAllData([
                'code' => $item->getCode(),
                'name' => $item->getName()
            ]);
            $items .= $itemTmplt;
        }
        return strval(new utils\Template("other/legend.html", [
            'caption' => $caption,
            'content' => $items
        ]));
    }
}
