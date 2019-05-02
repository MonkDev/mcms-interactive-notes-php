<?php

namespace MonkDev\InteractiveNote\Tests;

use PHPUnit\Framework\TestCase;
use MonkDev\InteractiveNote\InteractiveNote;

class InteractiveNoteTest extends TestCase
{
    /** @test */
    public function it_parses_a_block_of_text_for_single_line_input_placeholders()
    {
        $unparsedText = file_get_contents(__DIR__ . '/stubs/unparsed-single-line-text.stub');
        $parsedText = file_get_contents(__DIR__ . '/stubs/parsed-single-line-text.stub');

        $note = new InteractiveNote($unparsedText);
        $note->disableAutoWidth();
        $note->enableLastPass();

        $this->assertEquals($parsedText, $note->parse());
    }

    /** @test */
    public function it_parses_a_block_of_text_for_free_form_text_input_placeholders()
    {
        $unparsedText = file_get_contents(__DIR__ . '/stubs/unparsed-free-form-text.stub');
        $parsedText = file_get_contents(__DIR__ . '/stubs/parsed-free-form-text.stub');
        $note = new InteractiveNote($unparsedText);
        $note->disableAutoWidth();
        $note->enableLastPass();

        $this->assertEquals($parsedText, $note->parse());
    }

    /** @test */
    public function it_can_parse_html_without_issue()
    {
        $unparsedHtml = file_get_contents(__DIR__ . '/stubs/unparsed-html.stub');
        $parsedHtml = file_get_contents(__DIR__ . '/stubs/parsed-html.stub');
        $note = new InteractiveNote($unparsedHtml);
        $note->disableAutoWidth();
        $note->enableLastPass();

        $this->assertEquals($parsedHtml, $note->parse());
    }

    /** @test */
    public function you_can_override_the_default_single_input_template()
    {
        $note = new InteractiveNote('{{hello world}}');
        $note->disableAutoWidth();
        $note->enableLastPass();
        $note->setSingleInputTemplate("<input class='override' type='text' required='required' data-corect='__ANSWER__'>");

        $this->assertEquals("<input class='override' type='text' required='required' data-corect='hello world'>", $note->parse());
    }

    /** @test */
    public function you_can_override_the_default_free_form_template()
    {
        $note = new InteractiveNote('{## hello world ##}');
        $note->setFreeFormTemplate("<textarea class='override' data-hint='__ANSWER__'></textarea><p class='hint'>__ANSWER__</p>");

        $this->assertEquals("<textarea class='override' data-hint='hello world'></textarea><p class='hint'>hello world</p>", $note->parse());
    }

    /** @test */
    public function you_can_override_the_javascript_data_answer_attribute()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('data-answer', $note->getJavascriptSnippet());
        $this->assertNotContains('data-correct', $note->getJavascriptSnippet());

        $note->setSingleInputDataAnswerAttributeName('data-correct');

        $this->assertNotContains('data-answer', $note->getJavascriptSnippet());
        $this->assertContains('data-correct', $note->getJavascriptSnippet());
    }

    /** @test */
    public function you_can_override_the_javascript_correct_answer_class()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('correct-answer', $note->getJavascriptSnippet());
        $this->assertNotContains('answer-is-correct', $note->getJavascriptSnippet());

        $note->setCorrectAnswerClass('answer-is-correct');

        $this->assertNotContains('correct-answer', $note->getJavascriptSnippet());
        $this->assertContains('answer-is-correct', $note->getJavascriptSnippet());
    }

    /** @test */
    public function you_can_override_the_javascript_wrong_answer_class()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('wrong-answer', $note->getJavascriptSnippet());
        $this->assertNotContains('answer-is-wrong', $note->getJavascriptSnippet());

        $note->setWrongAnswerClass('answer-is-wrong');

        $this->assertNotContains('wrong-answer', $note->getJavascriptSnippet());
        $this->assertContains('answer-is-wrong', $note->getJavascriptSnippet());
    }

    /** @test */
    public function you_can_override_the_css_free_form_text_color()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('color: blue;', $note->getCssSnippet());
        $this->assertNotContains('color: red;', $note->getCssSnippet());

        $note->setFreeFormTextColor('red');

        $this->assertNotContains('color: blue;', $note->getCssSnippet());
        $this->assertContains('color: red;', $note->getCssSnippet());
    }

    /** @test */
    public function you_can_override_the_css_input_text_color()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('color: #c90;', $note->getCssSnippet());
        $this->assertNotContains('color: #ccc;', $note->getCssSnippet());

        $note->setInputTextColor('#ccc');

        $this->assertNotContains('color: #c90;', $note->getCssSnippet());
        $this->assertContains('color: #ccc;', $note->getCssSnippet());
    }

    /** @test */
    public function you_can_override_the_css_correct_answer_color()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('color: #090;', $note->getCssSnippet());
        $this->assertContains('border-color: #090;', $note->getCssSnippet());
        $this->assertNotContains('color: #023;', $note->getCssSnippet());
        $this->assertNotContains('border-color: #023;', $note->getCssSnippet());

        $note->setCorrectAnswerColor('#023');

        $this->assertNotContains('color: #090;', $note->getCssSnippet());
        $this->assertNotContains('border-color: #090;', $note->getCssSnippet());
        $this->assertContains('color: #023;', $note->getCssSnippet());
        $this->assertContains('border-color: #023;', $note->getCssSnippet());
    }

    /** @test */
    public function you_can_override_the_css_wrong_answer_color()
    {
        $note = new InteractiveNote('{{ hello world }}');
        $this->assertContains('color: #f00;', $note->getCssSnippet());
        $this->assertContains('border-color: #f00;', $note->getCssSnippet());
        $this->assertNotContains('color: #55C;', $note->getCssSnippet());
        $this->assertNotContains('border-color: #55C;', $note->getCssSnippet());

        $note->setWrongAnswerColor('#55C');

        $this->assertNotContains('color: #f00;', $note->getCssSnippet());
        $this->assertNotContains('border-color: #f00;', $note->getCssSnippet());
        $this->assertContains('color: #55C;', $note->getCssSnippet());
        $this->assertContains('border-color: #55C;', $note->getCssSnippet());
    }

    /** @test */
    public function you_can_disable_auto_width_functionality()
    {
        $note = new InteractiveNote('{{ hello world }}');

        $this->assertContains("style='width:7.7em;'", $note->parse());

        $note->disableAutoWidth();
        $note->enableLastPass();

        $this->assertNotContains("style='width:", $note->parse());

    }
}
