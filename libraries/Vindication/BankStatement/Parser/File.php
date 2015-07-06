<?php

namespace Vindication\BankStatement\Parser;

use Vindication\BankStatement\Abstracts;
use Vindication\Application\Utils\Encoding;

class File extends Abstracts\File
{
    protected $contents;
    protected $format_name;
    protected $parser_name;

    /**
     * 
     * @param string $filePath
     * @param string $format Format pliku wejsciowego
     * @param string $parser identyfikator parsera (nazwa banku)
     */
    public function __construct($filePath, $format, $parser)
    { 
        parent::__construct($filePath);

        $this->format_name = (string) $format;
        $this->parser_name = (string) $parser;

        $this->contents = Encoding::toUTF8( parent::getContents() );
    }

    /**
     * 
     * @return string
     */
    public function getFormatName()
    {
        return $this->format_name;
    }

    /**
     * 
     * @return string
     */
    public function getParserName()
    {
        return $this->parser_name;
    }

    public function getContents()
    {
        return $this->contents;
    }
}