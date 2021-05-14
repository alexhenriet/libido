<?php

namespace Libido\Console;

/*
 * @TODO: Make it work under MS Windows 10 (c)
 * Potential solutions at
 * https://stackoverflow.com/questions/16755142/how-to-make-win32-console-recognize-ansi-vt100-escape-sequences
 */
class DynamicMultiChoice
{
    const KEY_LEFT = 0;

    const KEY_RIGHT = 1;

    const KEY_UP = 2;

    const KEY_DOWN = 3;

    const KEY_ENTER = 4;

    const KEY_UNKNOWN = 5;

    private $choices;

    private $nbChoices;

    private $pos;

    public function __construct(array $choices)
    {
        $this->choices = $choices;
        $this->nbChoices = count($choices);
        $this->pos = 0;
    }

    public function ask($question)
    {
        echo self::bold($question) . PHP_EOL;
        $this->disableEcho();
        $this->hideCursor();
        $this->displayChoices();
        while (self::KEY_ENTER != ($key = $this->readKey())) {
            if ($key === self::KEY_UP) {
                if (--$this->pos < 0) {
                    $this->pos = $this->nbChoices - 1;
                }
            }
            if ($key === self::KEY_DOWN) {
                if (++$this->pos > $this->nbChoices - 1) {
                    $this->pos = 0;
                }
            }
            $this->displayChoices();
        }
        $this->jumpAfterChoices();
        $this->showCursor();
        $this->restoreEcho();
        return $this->choices[$this->pos];     
    }

    private function displayChoices()
    {
        for ($i = 0; $i < $this->nbChoices; $i++) {
            if ($i === $this->pos) { 
                echo self::bold($this->choices[$i]) . PHP_EOL;
            } else {
                echo $this->choices[$i] . PHP_EOL;
            }
            
        }
        $this->jumpBeforeChoices();
    }

    private function readKey()
    {
        $str = fread(STDIN, 3);
        $code = '';
        for ($i = 0; $i < 3; $i++) {
            if (isset($str[$i])) {
                $code .= ord($str[$i]);
            }
        }
        switch ($code) {
            case '279168':
                return self::KEY_LEFT;
            case '279167':
                return self::KEY_RIGHT;
            case '279165':
                return self::KEY_UP;
            case '279166':
                return self::KEY_DOWN;
            case '10':
                return self::KEY_ENTER;
            default:
                return self::KEY_UNKNOWN;
        }
    }

    private function disableEcho()
    {
        system('stty -echo -icanon');
    }

    private function restoreEcho()
    {
        system('stty echo icanon');
    }

    private function hideCursor()
    {
        echo chr(27) . '[?25l';
    }

    private function showCursor()
    {
        echo chr(27) . '[?25h';
    }

    private function jumpBeforeChoices()
    {
        echo chr(27) . '[' . $this->nbChoices . 'A';
    }

    private function jumpAfterChoices()
    {
        echo chr(27) . '[' . $this->nbChoices . 'B';
    }

    public static function bold($text) {
        return chr(27) . '[1m' . $text . chr(27) . '[0m';
    }
}