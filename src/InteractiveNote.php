<?php

namespace MonkDev\InteractiveNote;

class InteractiveNote
{
    const SINGLE_INPUT_REGEX = '/\{\{(.*?)\}\}/';
    const FREE_FORM_INPUT_REGEX = '/\{\#\#(.*?)\#\#\}/';

    protected $text;
    protected $originalText;
    protected $singleLineTemplate = "<input name='single-line[]' class='single-line' data-answer='__ANSWER__' type='text'>";
    protected $freeFormTemplate = "<textarea name='free-form[]' class='free-form' cols='30' rows='10' data-answer='__ANSWER__'>__ANSWER__</textarea>";
    protected $freeFormTextColor = 'blue';
    protected $singleInputDataAnswerAttributeName = 'data-answer';
    protected $inputTextColor = '#c90';
    protected $correctAnswerClass = 'correct-answer';
    protected $correctAnswerColor = '#090';
    protected $wrongAnswerClass = 'wrong-answer';
    protected $wrongAnswerColor = '#f00';
    protected $autoWidth = true;
    protected $disableLastPass = true;

    public function __construct($text)
    {
        $this->originalText = $text;
    }

    public function setSingleInputTemplate($template)
    {
        $this->singleLineTemplate = $template;
    }

    public function setFreeFormTemplate($template)
    {
        $this->freeFormTemplate = $template;
    }

    public function setFreeFormTextColor($color)
    {
        $this->freeFormTextColor = $color;
    }

    public function setInputTextColor($color)
    {
        $this->inputTextColor = $color;
    }

    public function setCorrectAnswerColor($color)
    {
        $this->correctAnswerColor = $color;
    }

    public function setWrongAnswerColor($color)
    {
        $this->wrongAnswerColor = $color;
    }

    public function setSingleInputDataAnswerAttributeName($name)
    {
        $this->singleInputDataAnswerAttributeName = $name;
    }

    public function setCorrectAnswerClass($class)
    {
        $this->correctAnswerClass = $class;
    }

    public function setWrongAnswerClass($class)
    {
        $this->wrongAnswerClass = $class;
    }

    public function disableAutoWidth()
    {
        $this->autoWidth = false;
        return $this;
    }

    public function enableLastPass()
    {
        $this->disableLastPass = false;
        return $this;
    }

    public function parse()
    {
        $this->convertSingleLineInputs()
            ->convertFreeFormInputs();

        return $this->text;
    }

    protected function convertSingleLineInputs()
    {
        $this->text = preg_replace_callback(self::SINGLE_INPUT_REGEX, function ($match) {
            $answer = htmlspecialchars(trim($match[1]), ENT_QUOTES | ENT_HTML5);
            $input = str_replace('__ANSWER__', $answer, $this->singleLineTemplate);
            if ($this->autoWidth) {
                $width = strlen($answer) * 0.7;
                $input = str_replace('<input', "<input style='width:{$width}em;' ", $input);
            }
            if ($this->disableLastPass) {
                $input = str_replace('<input', "<input data-lpignore='true' ", $input);
            }
            return $input;
        }, $this->originalText);
        return $this;
    }

    protected function convertFreeFormInputs()
    {
        $this->text = preg_replace_callback(self::FREE_FORM_INPUT_REGEX, function ($match) {
            $answer = htmlspecialchars(trim($match[1]), ENT_QUOTES|ENT_HTML5);
            return str_replace('__ANSWER__', $answer, $this->freeFormTemplate);
        }, $this->text);
    }

    public function getJavascriptSnippet()
    {
        $js = file_get_contents(__DIR__ . '/assets/base.js');
        $js = str_replace('__SINGLE-INPUT-DATA-ANSWER-ATTRIBUTE-NAME__', $this->singleInputDataAnswerAttributeName, $js);
        $js = str_replace('__CORRECT_ANSWER_CLASS__', $this->correctAnswerClass, $js);
        $js = str_replace('__WRONG_ANSWER_CLASS__', $this->wrongAnswerClass, $js);
        return "<script>{$js}</script>";
    }

    public function getCssSnippet()
    {
        $css = file_get_contents(__DIR__ . '/assets/base.css');
        $css = str_replace('__FREE_FORM_TEXT_COLOR__', $this->freeFormTextColor, $css);
        $css = str_replace('__INPUT_COLOR__', $this->inputTextColor, $css);
        $css = str_replace('__CORRECT_ANSWER_COLOR__', $this->correctAnswerColor, $css);
        $css = str_replace('__WRONG_ANSWER_COLOR__', $this->wrongAnswerColor, $css);
        return "<style id='notes-css'>{$css}</style>";
    }
}
