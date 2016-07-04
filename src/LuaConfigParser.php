<?php


namespace Bitov8\LuaParameterHandler;


class LuaConfigParser
{
    private $offset = 0;
    private $lines = [];
    private $currentLineNb = -1;
    private $currentLine = '';
    private $refs = [];


    /**
     * @param string $value
     *
     * @return array
     */
    public function parse($value)
    {

        $data = [];
        $this->lines = explode("\n", $value);
        $this->currentLineNb = -1;
        $this->currentLine = '';
        while ($this->moveToNextLine()) {
            if ($this->isCurrentLineEmpty()) {
                continue;
            }

            $line = $this->parseLine($this->currentLine);
            $data[$line['key']] = $line['value'];
        }

        return $data;
    }

    /**
     * Moves the parser to the next line.
     *
     * @return bool
     */
    private function moveToNextLine()
    {
        if ($this->currentLineNb >= count($this->lines) - 1) {
            return false;
        }

        $this->currentLine = $this->lines[++$this->currentLineNb];

        return true;
    }

    /**
     * Returns true if the current line is blank or if it is a comment line.
     *
     * @return bool Returns true if the current line is empty or if it is a comment line, false otherwise
     */
    private function isCurrentLineEmpty()
    {
        return $this->isCurrentLineBlank() || $this->isCurrentLineComment();
    }

    /**
     * Returns true if the current line is blank.
     *
     * @return bool Returns true if the current line is blank, false otherwise
     */
    private function isCurrentLineBlank()
    {
        return '' == trim($this->currentLine, ' ');
    }

    /**
     * Returns true if the current line is a comment line.
     *
     * @return bool Returns true if the current line is a comment line, false otherwise
     */
    private function isCurrentLineComment()
    {
        //checking explicitly the first char of the trim is faster than loops or strpos
        $ltrimmedLine = ltrim($this->currentLine, ' ');

        return $ltrimmedLine[0] === '#';
    }

    /**
     *
     * Parse current line
     *
     * @param string $currentLine
     *
     * @return array
     */
    private function parseLine($currentLine)
    {
        preg_match('/^set\s+(\S+)\s+\"(\S*)\"\;$/', $currentLine, $matches);
        
        return [
           'key' => $matches[1],
            'value' => $matches[2],
        ];
    }

}